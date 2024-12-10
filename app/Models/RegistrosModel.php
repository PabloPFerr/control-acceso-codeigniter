<?php

namespace App\Models;

use CodeIgniter\Model;

class RegistrosModel extends Model
{
    protected $table = 'registros';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'usuario_id',
        'hora_entrada',
        'hora_salida',
        'duracion'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $beforeUpdate = ['calcularDuracion'];

    // Validación
    protected $validationRules = [
        'usuario_id' => 'required|integer',
        'hora_entrada' => 'required|valid_date',
        'hora_salida' => 'permit_empty|valid_date'
    ];

    protected $validationMessages = [
        'usuario_id' => [
            'required' => 'El ID de usuario es requerido',
            'integer' => 'El ID de usuario debe ser un número entero'
        ],
        'hora_entrada' => [
            'required' => 'La hora de entrada es requerida',
            'valid_date' => 'La hora de entrada debe ser una fecha válida'
        ],
        'hora_salida' => [
            'valid_date' => 'La hora de salida debe ser una fecha válida'
        ]
    ];

    protected function calcularDuracion(array $data)
    {
        // Solo calculamos la duración si hay hora de salida
        if (isset($data['data']['hora_salida'])) {
            $entrada = new \DateTime($data['data']['hora_entrada'] ?? $this->find($data['id'])['hora_entrada']);
            $salida = new \DateTime($data['data']['hora_salida']);
            $intervalo = $entrada->diff($salida);
            $data['data']['duracion'] = $intervalo->h + ($intervalo->i / 60);
        }
        return $data;
    }

    /**
     * Obtiene los registros de asistencia con información del usuario
     *
     * @param array $filtros Array con los filtros a aplicar
     * @return array
     */
    public function getRegistrosConUsuario($filtros = [])
    {
        $builder = $this->db->table($this->table);
        $builder->select('registros.*, usuarios.nombre, usuarios.apellido');
        $builder->join('usuarios', 'usuarios.id = registros.usuario_id');
        
        if (!empty($filtros['usuario_id'])) {
            $builder->where('usuarios.id', $filtros['usuario_id']);
        }
        if (!empty($filtros['fecha_inicio'])) {
            $builder->where('DATE(registros.hora_entrada) >=', $filtros['fecha_inicio']);
        }
        if (!empty($filtros['fecha_fin'])) {
            $builder->where('DATE(registros.hora_entrada) <=', $filtros['fecha_fin']);
        }
        
        $builder->orderBy('registros.hora_entrada', 'DESC');
        
        $registros = $builder->get()->getResultArray();
        
        // Calcular la duración para cada registro
        foreach ($registros as &$registro) {
            if ($registro['hora_salida']) {
                $entrada = new \DateTime($registro['hora_entrada']);
                $salida = new \DateTime($registro['hora_salida']);
                $duracion = $salida->diff($entrada);
                $registro['duracion'] = $duracion->h + ($duracion->i / 60);
            } else {
                $registro['duracion'] = null;
            }
        }
        
        return $registros;
    }

    /**
     * Obtiene los registros de hoy
     *
     * @return array
     */
    public function getRegistrosHoy()
    {
        $hoy = date('Y-m-d');
        $builder = $this->db->table($this->table);
        $builder->select('registros.*, usuarios.nombre, usuarios.apellido');
        $builder->join('usuarios', 'usuarios.id = registros.usuario_id');
        $builder->where('DATE(registros.hora_entrada)', $hoy);
        $builder->orderBy('registros.hora_entrada', 'DESC');
        
        $registros = $builder->get()->getResultArray();
        
        // Calcular la duración para cada registro
        foreach ($registros as &$registro) {
            if ($registro['hora_salida']) {
                $entrada = new \DateTime($registro['hora_entrada']);
                $salida = new \DateTime($registro['hora_salida']);
                $duracion = $salida->diff($entrada);
                $registro['duracion'] = $duracion->h + ($duracion->i / 60);
            } else {
                $registro['duracion'] = null;
            }
        }
        
        return $registros;
    }

    /**
     * Registra una entrada
     *
     * @param int $usuarioId ID del usuario
     * @return array|bool
     */
    public function registrarEntrada($usuarioId)
    {
        $data = [
            'usuario_id' => $usuarioId,
            'hora_entrada' => date('Y-m-d H:i:s')
        ];

        if ($this->insert($data)) {
            return $this->find($this->insertID);
        }

        return false;
    }

    /**
     * Registra una salida
     *
     * @param int $registroId ID del registro
     * @return bool
     */
    public function registrarSalida($registroId)
    {
        return $this->update($registroId, [
            'hora_salida' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Verifica si un usuario tiene un registro activo (sin hora de salida)
     *
     * @param int $usuarioId ID del usuario
     * @return array|null
     */
    public function getRegistroActivo($usuarioId)
    {
        return $this->where([
            'usuario_id' => $usuarioId,
            'hora_salida' => null
        ])->first();
    }

    /**
     * Obtiene las horas totales y días trabajados agrupados por usuario
     *
     * @param array $filtros Array con los filtros a aplicar
     * @return array
     */
    public function getHorasTotales($filtros = [])
    {
        $builder = $this->db->table($this->table);
        $builder->select('usuarios.id, usuarios.nombre, usuarios.apellido,
                         SUM(CASE 
                             WHEN registros.hora_salida IS NOT NULL 
                             THEN TIME_TO_SEC(TIMEDIFF(registros.hora_salida, registros.hora_entrada))/3600 
                             ELSE 0 
                         END) as total_horas,
                         AVG(CASE 
                             WHEN registros.hora_salida IS NOT NULL 
                             THEN TIME_TO_SEC(TIMEDIFF(registros.hora_salida, registros.hora_entrada))/3600 
                             ELSE 0 
                         END) as promedio_horas,
                         COUNT(DISTINCT DATE(registros.hora_entrada)) as dias_trabajados');
        $builder->join('usuarios', 'usuarios.id = registros.usuario_id');
        
        if (!empty($filtros['usuario_id'])) {
            $builder->where('usuarios.id', $filtros['usuario_id']);
        }
        if (!empty($filtros['fecha_inicio'])) {
            $builder->where('DATE(registros.hora_entrada) >=', $filtros['fecha_inicio']);
        }
        if (!empty($filtros['fecha_fin'])) {
            $builder->where('DATE(registros.hora_entrada) <=', $filtros['fecha_fin']);
        }
        
        $builder->groupBy('usuarios.id, usuarios.nombre, usuarios.apellido');
        
        return $builder->get()->getResultArray();
    }

    /**
     * Obtiene las horas trabajadas por día para cada usuario
     *
     * @param array $filtros Array con los filtros a aplicar
     * @return array
     */
    public function getHorasPorDia($filtros = [])
    {
        $builder = $this->db->table($this->table);
        $builder->select('usuarios.nombre, usuarios.apellido,
                         DATE(registros.hora_entrada) as fecha,
                         SUM(CASE 
                             WHEN registros.hora_salida IS NOT NULL 
                             THEN TIME_TO_SEC(TIMEDIFF(registros.hora_salida, registros.hora_entrada))/3600 
                             ELSE 0 
                         END) as total_horas');
        $builder->join('usuarios', 'usuarios.id = registros.usuario_id');
        
        if (!empty($filtros['usuario_id'])) {
            $builder->where('usuarios.id', $filtros['usuario_id']);
        }
        if (!empty($filtros['fecha_inicio'])) {
            $builder->where('DATE(registros.hora_entrada) >=', $filtros['fecha_inicio']);
        }
        if (!empty($filtros['fecha_fin'])) {
            $builder->where('DATE(registros.hora_entrada) <=', $filtros['fecha_fin']);
        }
        
        $builder->groupBy('usuarios.nombre, usuarios.apellido, DATE(registros.hora_entrada)');
        $builder->orderBy('fecha', 'ASC');
        
        return $builder->get()->getResultArray();
    }

    /**
     * Obtiene las horas trabajadas por mes
     *
     * @param array $filtros Array con los filtros a aplicar
     * @return array
     */
    public function getHorasPorMes($filtros = [])
    {
        $builder = $this->db->table($this->table);
        $builder->select('usuarios.nombre, usuarios.apellido,
                         DATE_FORMAT(registros.hora_entrada, "%Y-%m") as mes,
                         SUM(CASE 
                             WHEN registros.hora_salida IS NOT NULL 
                             THEN TIME_TO_SEC(TIMEDIFF(registros.hora_salida, registros.hora_entrada))/3600 
                             ELSE 0 
                         END) as total_horas');
        $builder->join('usuarios', 'usuarios.id = registros.usuario_id');
        
        if (!empty($filtros['usuario_id'])) {
            $builder->where('usuarios.id', $filtros['usuario_id']);
        }
        if (!empty($filtros['fecha_inicio'])) {
            $builder->where('DATE(registros.hora_entrada) >=', $filtros['fecha_inicio']);
        }
        if (!empty($filtros['fecha_fin'])) {
            $builder->where('DATE(registros.hora_entrada) <=', $filtros['fecha_fin']);
        }
        
        $builder->groupBy('usuarios.nombre, usuarios.apellido, DATE_FORMAT(registros.hora_entrada, "%Y-%m")');
        $builder->orderBy('mes', 'ASC');
        
        return $builder->get()->getResultArray();
    }

    /**
     * Obtiene las horas totales trabajadas
     *
     * @param array $filtros Array con los filtros a aplicar
     * @return array
     */
    public function getHorasTotalesTrabajadas($filtros)
    {
        $builder = $this->db->table($this->table);
        $builder->select("
            usuarios.id as usuario_id,
            usuarios.nombre,
            usuarios.apellido,
            SUM(
                CASE 
                    WHEN registros.hora_salida IS NOT NULL 
                    THEN TIMESTAMPDIFF(SECOND, registros.hora_entrada, registros.hora_salida) / 3600.0
                    ELSE 0 
                END
            ) as horas_totales
        ");
        $builder->join('usuarios', 'usuarios.id = registros.usuario_id');
        
        if (!empty($filtros['usuario_id'])) {
            $builder->where('usuarios.id', $filtros['usuario_id']);
        }
        if (!empty($filtros['fecha_inicio'])) {
            $builder->where('DATE(registros.hora_entrada) >=', $filtros['fecha_inicio']);
        }
        if (!empty($filtros['fecha_fin'])) {
            $builder->where('DATE(registros.hora_entrada) <=', $filtros['fecha_fin']);
        }
        
        $builder->groupBy('usuarios.id, usuarios.nombre, usuarios.apellido');
        $builder->orderBy('usuarios.nombre, usuarios.apellido');
        
        return $builder->get()->getResultArray();
    }
}
