<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\Registro;
use App\Models\Usuario;

class Dashboard extends BaseController
{
    protected $registro;
    protected $usuario;

    public function __construct()
    {
        $this->registro = new Registro();
        $this->usuario = new Usuario();
    }

    public function index()
    {
        $session = session();
        if (!$session->has('user') || $session->get('user')['rol'] !== 'admin') {
            return redirect()->to(base_url('login'));
        }

        try {
            // Obtener todos los usuarios
            $usuarios = $this->usuario->findAll();
            
            // Calcular usuarios activos
            $usuariosActivos = $this->usuario->where('activo', 1)->countAllResults();
            
            // Obtener registros de hoy
            $db = \Config\Database::connect();
            $query = $db->query("
                SELECT 
                    r.*,
                    u.nombre,
                    u.apellido
                FROM registros r
                INNER JOIN usuarios u ON u.id = r.usuario_id
                WHERE DATE(r.hora_entrada) = CURDATE()
                ORDER BY r.hora_entrada DESC
            ");
            
            $registrosHoy = $query->getResultArray();
            
            // Debug: Verificar la estructura de los datos
            log_message('debug', 'Query ejecutado: ' . $db->getLastQuery());
            log_message('debug', 'Registros encontrados: ' . json_encode($registrosHoy, JSON_PRETTY_PRINT));

            if (empty($registrosHoy)) {
                log_message('debug', 'No se encontraron registros para hoy');
            } else {
                log_message('debug', 'Estructura del primer registro: ' . json_encode($registrosHoy[0], JSON_PRETTY_PRINT));
                log_message('debug', 'Campos disponibles: ' . implode(', ', array_keys($registrosHoy[0])));
            }

            $data = [
                'totalUsuarios' => count($usuarios),
                'usuariosActivos' => $usuariosActivos,
                'registrosHoy' => $registrosHoy,
                'user' => $session->get('user')
            ];
            
            return view('admin/dashboard', $data);
            
        } catch (\Exception $e) {
            log_message('error', 'Error en Dashboard/index: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            
            return view('admin/dashboard', [
                'totalUsuarios' => 0,
                'usuariosActivos' => 0,
                'registrosHoy' => [],
                'user' => $session->get('user'),
                'error' => 'Hubo un error al cargar los datos. Por favor, intente de nuevo.'
            ]);
        }
    }

    public function reportes()
    {
        $session = session();
        if (!$session->has('user') || $session->get('user')['rol'] !== 'admin') {
            return redirect()->to(base_url('login'));
        }

        $usuarios = $this->usuario->findAll();
        $filtroUsuario = $this->request->getGet('usuario_id');
        $fechaInicio = $this->request->getGet('fecha_inicio');
        $fechaFin = $this->request->getGet('fecha_fin');

        $query = $this->registro
            ->select('registros.*, usuarios.nombre, usuarios.apellido')
            ->join('usuarios', 'usuarios.id = registros.usuario_id');

        if ($filtroUsuario) {
            $query->where('usuario_id', $filtroUsuario);
        }

        if ($fechaInicio) {
            $query->where('hora_entrada >=', $fechaInicio . ' 00:00:00');
        }

        if ($fechaFin) {
            $query->where('hora_entrada <=', $fechaFin . ' 23:59:59');
        }

        $registros = $query->findAll();

        return view('admin/reportes', [
            'registros' => $registros,
            'usuarios' => $usuarios,
            'filtros' => [
                'usuario_id' => $filtroUsuario,
                'fecha_inicio' => $fechaInicio,
                'fecha_fin' => $fechaFin
            ],
            'user' => $session->get('user')
        ]);
    }
}
