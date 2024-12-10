<?php

namespace App\Models;

use CodeIgniter\Model;

class Registro extends Model
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

    protected $validationRules = [
        'usuario_id'   => 'required|numeric|is_not_unique[usuarios.id]',
        'hora_entrada' => 'required|valid_date',
        'hora_salida'  => 'permit_empty|valid_date',
        'duracion'     => 'permit_empty|numeric'
    ];

    // Relación con el modelo Usuario
    public function usuario()
    {
        return $this->belongsTo('App\Models\Usuario', 'usuario_id', 'id');
    }

    public function getRegistrosConUsuario($filtros = [])
    {
        $builder = $this->db->table('registros r')
            ->select('r.*, u.nombre, u.apellido')
            ->join('usuarios u', 'u.id = r.usuario_id');

        if (!empty($filtros['usuario_id'])) {
            $builder->where('r.usuario_id', $filtros['usuario_id']);
        }
        if (!empty($filtros['fecha_inicio'])) {
            $builder->where('DATE(r.hora_entrada) >=', $filtros['fecha_inicio']);
        }
        if (!empty($filtros['fecha_fin'])) {
            $builder->where('DATE(r.hora_entrada) <=', $filtros['fecha_fin']);
        }

        return $builder->orderBy('r.hora_entrada', 'DESC')->get()->getResultArray();
    }

    public function getHorasPorDia($filtros = [])
    {
        $builder = $this->db->table('registros r')
            ->select('DATE(r.hora_entrada) as fecha, u.nombre, u.apellido, u.id as usuario_id, 
                     SUM(COALESCE(r.duracion, 0)) as total_horas')
            ->join('usuarios u', 'u.id = r.usuario_id')
            ->where('r.duracion IS NOT NULL')
            ->where('r.duracion > 0')
            ->groupBy('DATE(r.hora_entrada), u.id')
            ->orderBy('fecha', 'ASC');

        if (!empty($filtros['usuario_id'])) {
            $builder->where('r.usuario_id', $filtros['usuario_id']);
        }
        if (!empty($filtros['fecha_inicio'])) {
            $builder->where('DATE(r.hora_entrada) >=', $filtros['fecha_inicio']);
        }
        if (!empty($filtros['fecha_fin'])) {
            $builder->where('DATE(r.hora_entrada) <=', $filtros['fecha_fin']);
        }

        // Obtener la consulta SQL para debug
        $sql = $builder->getCompiledSelect();
        log_message('debug', 'SQL Horas por Día: ' . $sql);
        
        $result = $builder->get()->getResultArray();
        log_message('debug', 'Resultado Horas por Día: ' . print_r($result, true));
        
        return $result;
    }

    public function getHorasTotales($filtros = [])
    {
        $builder = $this->db->table('registros r')
            ->select('u.nombre, u.apellido, u.id as usuario_id, 
                     SUM(COALESCE(r.duracion, 0)) as total_horas,
                     ROUND(AVG(COALESCE(r.duracion, 0)), 2) as promedio_horas,
                     COUNT(DISTINCT DATE(r.hora_entrada)) as dias_trabajados')
            ->join('usuarios u', 'u.id = r.usuario_id')
            ->where('r.duracion IS NOT NULL')
            ->where('r.duracion > 0')
            ->groupBy('u.id')
            ->orderBy('total_horas', 'DESC');

        if (!empty($filtros['usuario_id'])) {
            $builder->where('r.usuario_id', $filtros['usuario_id']);
        }
        if (!empty($filtros['fecha_inicio'])) {
            $builder->where('DATE(r.hora_entrada) >=', $filtros['fecha_inicio']);
        }
        if (!empty($filtros['fecha_fin'])) {
            $builder->where('DATE(r.hora_entrada) <=', $filtros['fecha_fin']);
        }

        // Obtener la consulta SQL para debug
        $sql = $builder->getCompiledSelect();
        log_message('debug', 'SQL Horas Totales: ' . $sql);
        
        $result = $builder->get()->getResultArray();
        log_message('debug', 'Resultado Horas Totales: ' . print_r($result, true));
        
        return $result;
    }
}
