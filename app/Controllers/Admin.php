<?php

namespace App\Controllers;

use App\Models\Usuario;
use App\Models\RegistrosModel;
use CodeIgniter\Controller;
use CodeIgniter\API\ResponseTrait;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Admin extends Controller
{
    use ResponseTrait;

    protected $usuario;
    protected $registrosModel;
    protected $db;
    protected $session;

    public function __construct()
    {
        $this->usuario = new Usuario();
        $this->registrosModel = new RegistrosModel();
        $this->db = \Config\Database::connect();
        $this->session = session();
    }

    private function verificarAdmin()
    {
        return $this->session->has('user') && $this->session->get('user')['rol'] === 'admin';
    }

    private function verificarAccesoReportes()
    {
        $user = $this->session->get('user');
        return $this->session->has('user') && ($user['rol'] === 'admin' || $user['rol'] === 'auditor');
    }

    public function dashboard()
    {
        if (!$this->verificarAdmin()) {
            return redirect()->to(base_url('dashboard'))->with('error', 'Acceso no autorizado');
        }

        $registrosHoy = $this->registrosModel->getRegistrosHoy();
        $totalUsuarios = $this->usuario->countAll();
        $usuariosActivos = $this->usuario->where('activo', 1)->countAllResults();
        
        return view('admin/dashboard', [
            'titulo' => 'Dashboard',
            'registrosHoy' => $registrosHoy,
            'totalUsuarios' => $totalUsuarios,
            'usuariosActivos' => $usuariosActivos
        ]);
    }

    public function usuarios()
    {
        if (!$this->verificarAdmin()) {
            return redirect()->to(base_url('dashboard'))->with('error', 'Acceso no autorizado');
        }

        try {
            $usuarios = $this->usuario->findAll();
            
            // Debug
            echo "<!-- Debug: Método usuarios() llamado -->\n";
            echo "<!-- Debug: Usuarios encontrados: " . count($usuarios) . " -->\n";
            
            $data = [
                'titulo' => 'Gestión de Usuarios',
                'usuarios' => $usuarios
            ];
            
            echo "<!-- Debug: Data preparada -->\n";
            
            return view('admin/usuarios', $data);
        } catch (\Exception $e) {
            // Log del error
            log_message('error', 'Error en Admin::usuarios: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            
            // Mostrar error
            echo "Error: " . $e->getMessage();
            throw $e;
        }
    }

    public function create()
    {
        helper(['form']);
        $rules = [
            'nombre' => 'required|min_length[3]',
            'email' => 'required|valid_email|is_unique[usuarios.email]',
            'password' => 'required|min_length[6]',
            'role' => 'required|in_list[admin,user,auditor]'
        ];

        if($this->request->getMethod() === 'post' && $this->validate($rules)) {
            $model = new UsuarioModel();
            $data = [
                'nombre' => $this->request->getPost('nombre'),
                'email' => $this->request->getPost('email'),
                'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
                'role' => $this->request->getPost('role'),
                'estado' => $this->request->getPost('estado')
            ];
            
            $model->save($data);
            return redirect()->to(site_url('admin/usuarios'))->with('msg', 'Usuario creado exitosamente')->with('msg_type', 'success');
        }

        return view('admin/usuarios_form', [
            'title' => 'Crear Usuario',
            'validation' => $this->validator
        ]);
    }

    public function edit($id = null)
    {
        helper(['form']);
        $model = new UsuarioModel();
        $usuario = $model->find($id);

        if(empty($usuario)) {
            return redirect()->to(site_url('admin/usuarios'))->with('msg', 'Usuario no encontrado')->with('msg_type', 'danger');
        }

        $rules = [
            'nombre' => 'required|min_length[3]',
            'email' => "required|valid_email|is_unique[usuarios.email,id,{$id}]",
            'role' => 'required|in_list[admin,user,auditor]'
        ];

        // Solo validar password si se proporciona uno nuevo
        if($this->request->getPost('password')) {
            $rules['password'] = 'min_length[6]';
        }

        if($this->request->getMethod() === 'post' && $this->validate($rules)) {
            $data = [
                'id' => $id,
                'nombre' => $this->request->getPost('nombre'),
                'email' => $this->request->getPost('email'),
                'role' => $this->request->getPost('role'),
                'estado' => $this->request->getPost('estado')
            ];

            // Solo actualizar password si se proporciona uno nuevo
            if($this->request->getPost('password')) {
                $data['password'] = password_hash($this->request->getPost('password'), PASSWORD_DEFAULT);
            }
            
            $model->save($data);
            return redirect()->to(site_url('admin/usuarios'))->with('msg', 'Usuario actualizado exitosamente')->with('msg_type', 'success');
        }

        return view('admin/usuarios_form', [
            'title' => 'Editar Usuario',
            'usuario' => $usuario,
            'validation' => $this->validator
        ]);
    }

    public function guardar()
    {
        if (!$this->verificarAdmin()) {
            return $this->response->setJSON(['error' => 'Acceso no autorizado']);
        }

        $id = $this->request->getPost('id');
        $data = [
            'nombre' => $this->request->getPost('nombre'),
            'apellido' => $this->request->getPost('apellido'),
            'email' => $this->request->getPost('email'),
            'rol' => $this->request->getPost('rol')
        ];

        // Si se proporciona una contraseña, hashearla
        if ($password = $this->request->getPost('password')) {
            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        try {
            if ($id) {
                // Actualizar usuario existente
                if (!$this->usuario->update($id, $data)) {
                    return $this->response->setJSON([
                        'error' => 'Error al actualizar: ' . implode(', ', $this->usuario->errors())
                    ]);
                }
            } else {
                // Crear nuevo usuario
                if (!$this->usuario->insert($data)) {
                    return $this->response->setJSON([
                        'error' => 'Error al crear: ' . implode(', ', $this->usuario->errors())
                    ]);
                }
            }

            return $this->response->setJSON(['success' => true]);
        } catch (\Exception $e) {
            log_message('error', 'Error en guardar usuario: ' . $e->getMessage());
            return $this->response->setJSON(['error' => 'Error al procesar la solicitud']);
        }
    }

    public function toggleEstado($id = null)
    {
        if (!$this->verificarAdmin()) {
            return $this->response->setJSON(['error' => 'Acceso no autorizado']);
        }

        if (!$id) {
            return $this->response->setJSON(['error' => 'ID de usuario no proporcionado']);
        }

        try {
            $usuario = $this->usuario->find($id);
            if (!$usuario) {
                return $this->response->setJSON(['error' => 'Usuario no encontrado']);
            }

            $nuevoEstado = $usuario['activo'] ? 0 : 1;
            if (!$this->usuario->update($id, ['activo' => $nuevoEstado])) {
                return $this->response->setJSON([
                    'error' => 'Error al actualizar estado: ' . implode(', ', $this->usuario->errors())
                ]);
            }

            return $this->response->setJSON(['success' => true]);
        } catch (\Exception $e) {
            log_message('error', 'Error en toggleEstado: ' . $e->getMessage());
            return $this->response->setJSON(['error' => 'Error al procesar la solicitud']);
        }
    }

    public function guardarUsuario()
    {
        if (!$this->verificarAdmin()) {
            return redirect()->to(base_url('admin/usuarios'))->with('error', 'Acceso no autorizado');
        }

        $id = $this->request->getPost('id');
        $rules = [
            'nombre' => 'required|min_length[3]',
            'apellido' => 'required|min_length[3]',
            'email' => $id ? "required|valid_email|is_unique[usuarios.email,id,$id]" : 'required|valid_email|is_unique[usuarios.email]',
            'rol' => 'required|in_list[admin,user,auditor]'
        ];

        // Solo validar password si es un nuevo usuario o si se proporciona uno nuevo
        if (!$id || $this->request->getPost('password')) {
            $rules['password'] = 'required|min_length[6]';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $data = [
            'nombre' => $this->request->getPost('nombre'),
            'apellido' => $this->request->getPost('apellido'),
            'email' => $this->request->getPost('email'),
            'rol' => $this->request->getPost('rol'),
            'activo' => 1
        ];

        // Agregar password solo si se proporciona uno
        if ($password = $this->request->getPost('password')) {
            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        try {
            if ($id) {
                $this->usuario->update($id, $data);
                $mensaje = 'Usuario actualizado exitosamente';
            } else {
                $this->usuario->insert($data);
                $mensaje = 'Usuario creado exitosamente';
            }

            return redirect()->to(base_url('admin/usuarios'))
                ->with('mensaje', $mensaje)
                ->with('tipo', 'success');
        } catch (\Exception $e) {
            log_message('error', '[Admin::guardarUsuario] Error: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al procesar la solicitud: ' . $e->getMessage());
        }
    }

    public function reportes()
    {
        if (!$this->verificarAccesoReportes()) {
            return redirect()->to(base_url('dashboard'))->with('error', 'Acceso no autorizado');
        }

        $filtros = [
            'usuario_id' => $this->request->getGet('usuario_id'),
            'fecha_inicio' => $this->request->getGet('fecha_inicio') ?: date('Y-m-d', strtotime('-1 month')),
            'fecha_fin' => $this->request->getGet('fecha_fin') ?: date('Y-m-d')
        ];

        // Obtener registros detallados
        $registros = $this->registrosModel->getRegistrosConUsuario($filtros);
        
        // Obtener horas por día y usuario
        $horasPorDia = $this->registrosModel->getHorasPorDia($filtros);
        
        // Obtener horas totales por usuario
        $horasTotales = $this->registrosModel->getHorasTotales($filtros);
        
        // Obtener horas por mes y usuario
        $horasPorMes = $this->registrosModel->getHorasPorMes($filtros);
        
        // Preparar datos para gráficos
        $datosGraficoLineas = $this->prepararDatosGraficoLineas($horasPorDia);
        $datosGraficoBarra = $this->prepararDatosGraficoBarra($horasTotales);
        $datosGraficoMensual = $this->prepararDatosGraficoMensual($horasPorMes);

        $data = [
            'titulo' => 'Reportes',
            'usuarios' => $this->usuario->findAll(),
            'filtros' => $filtros,
            'registros' => $registros,
            'horasPorDia' => $horasPorDia,
            'horasTotales' => $horasTotales,
            'horasPorMes' => $horasPorMes,
            'datosGraficoLineas' => json_encode($datosGraficoLineas),
            'datosGraficoBarra' => json_encode($datosGraficoBarra),
            'datosGraficoMensual' => json_encode($datosGraficoMensual),
            'mostrarUsuario' => true
        ];

        // Si se solicita exportación a Excel
        if ($this->request->getGet('export') === 'excel') {
            $tipoReporte = $this->request->getGet('tipo_reporte') ?? 'detallado';
            return $this->exportarExcel($tipoReporte, $data);
        }

        return view('admin/reportes', $data);
    }

    public function reporteUsuario()
    {
        if (!$this->session->has('user')) {
            return redirect()->to(base_url('login'))->with('error', 'Debe iniciar sesión');
        }

        $usuario = $this->session->get('user');
        $registrosModel = new \App\Models\RegistrosModel();
        
        // Obtener filtros
        $filtros = [
            'usuario_id' => $usuario['id'], // Forzar el ID del usuario actual
            'fecha_inicio' => $this->request->getGet('fecha_inicio') ?: date('Y-m-d', strtotime('-1 month')),
            'fecha_fin' => $this->request->getGet('fecha_fin') ?: date('Y-m-d')
        ];

        // Obtener registros detallados
        $registros = $registrosModel->getRegistrosConUsuario($filtros);
        
        // Obtener horas por día
        $horasPorDia = $registrosModel->getHorasPorDia($filtros);
        
        // Obtener horas totales
        $horasTotales = $registrosModel->getHorasTotales($filtros);
        
        // Preparar datos para gráficos
        $datosGraficoLineas = $this->prepararDatosGraficoLineas($horasPorDia);
        $datosGraficoBarra = $this->prepararDatosGraficoBarra($horasTotales);

        $data = [
            'titulo' => 'Mi Reporte de Asistencia',
            'filtros' => $filtros,
            'registros' => $registros,
            'horasPorDia' => $horasPorDia,
            'horasTotales' => $horasTotales,
            'datosGraficoLineas' => json_encode($datosGraficoLineas),
            'datosGraficoBarra' => json_encode($datosGraficoBarra),
            'mostrarUsuario' => true
        ];

        // Si se solicita exportación a Excel
        if ($this->request->getGet('export') === 'excel') {
            $tipoReporte = $this->request->getGet('tipo_reporte') ?? 'detallado';
            return $this->exportarExcel($tipoReporte, $data);
        }

        return view('user/reporte', $data);
    }

    private function prepararDatosGraficoLineas($horasPorDia)
    {
        $datos = [
            'labels' => [],
            'datasets' => []
        ];
        
        $usuariosDatos = [];
        
        // Organizar datos por usuario y fecha
        foreach ($horasPorDia as $registro) {
            $fecha = date('d/m/Y', strtotime($registro['fecha']));
            if (!in_array($fecha, $datos['labels'])) {
                $datos['labels'][] = $fecha;
            }
            
            $nombreUsuario = $registro['nombre'] . ' ' . $registro['apellido'];
            if (!isset($usuariosDatos[$nombreUsuario])) {
                $usuariosDatos[$nombreUsuario] = [];
            }
            $usuariosDatos[$nombreUsuario][$fecha] = round($registro['total_horas'], 1);
        }
        
        // Crear datasets para cada usuario
        $colores = ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40'];
        $colorIndex = 0;
        
        foreach ($usuariosDatos as $usuario => $horas) {
            $dataset = [
                'label' => $usuario,
                'data' => array_map(function($fecha) use ($horas) {
                    return $horas[$fecha] ?? 0;
                }, $datos['labels']),
                'borderColor' => $colores[$colorIndex % count($colores)],
                'fill' => false
            ];
            $datos['datasets'][] = $dataset;
            $colorIndex++;
        }
        
        return $datos;
    }

    private function prepararDatosGraficoBarra($horasTotales)
    {
        $datos = [
            'labels' => [],
            'datasets' => [
                [
                    'label' => 'Horas Totales',
                    'data' => [],
                    'backgroundColor' => '#36A2EB'
                ]
            ]
        ];
        
        foreach ($horasTotales as $registro) {
            $datos['labels'][] = $registro['nombre'] . ' ' . $registro['apellido'];
            $datos['datasets'][0]['data'][] = round($registro['total_horas'], 1);
        }
        
        return $datos;
    }

    private function prepararDatosGraficoMensual($horasPorMes)
    {
        $datos = [
            'labels' => [],
            'datasets' => []
        ];
        
        $usuariosDatos = [];
        
        // Organizar datos por usuario y mes
        foreach ($horasPorMes as $registro) {
            $mes = date('M Y', strtotime($registro['mes'] . '-01')); // Convertir YYYY-MM a formato legible
            if (!in_array($mes, $datos['labels'])) {
                $datos['labels'][] = $mes;
            }
            
            $nombreUsuario = $registro['nombre'] . ' ' . $registro['apellido'];
            if (!isset($usuariosDatos[$nombreUsuario])) {
                $usuariosDatos[$nombreUsuario] = [];
            }
            $usuariosDatos[$nombreUsuario][$mes] = round($registro['total_horas'], 1);
        }
        
        // Crear datasets para cada usuario
        $colores = ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40'];
        $colorIndex = 0;
        
        foreach ($usuariosDatos as $usuario => $horas) {
            $dataset = [
                'label' => $usuario,
                'data' => array_map(function($mes) use ($horas) {
                    return $horas[$mes] ?? 0;
                }, $datos['labels']),
                'backgroundColor' => $colores[$colorIndex % count($colores)],
                'borderColor' => $colores[$colorIndex % count($colores)],
                'borderWidth' => 1
            ];
            $datos['datasets'][] = $dataset;
            $colorIndex++;
        }
        
        return $datos;
    }

    private function exportarExcel($tipoReporte, $data)
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        switch ($tipoReporte) {
            case 'por_dia':
                $this->exportarHorasPorDia($sheet, $data['horasPorDia']);
                $filename = 'horas_por_dia.xlsx';
                break;
            case 'totales':
                $this->exportarHorasTotales($sheet, $data['horasTotales']);
                $filename = 'horas_totales.xlsx';
                break;
            case 'por_mes':
                $this->exportarHorasPorMes($sheet, $data['horasPorMes']);
                $filename = 'horas_por_mes.xlsx';
                break;
            default:
                $this->exportarRegistrosDetallados($sheet, $data['registros']);
                $filename = 'registros_detallados.xlsx';
        }
        
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }

    private function exportarHorasPorDia($sheet, $horasPorDia)
    {
        $sheet->setCellValue('A1', 'Usuario');
        $sheet->setCellValue('B1', 'Fecha');
        $sheet->setCellValue('C1', 'Horas Totales');
        
        $row = 2;
        foreach ($horasPorDia as $registro) {
            $sheet->setCellValue('A' . $row, $registro['nombre'] . ' ' . $registro['apellido']);
            $sheet->setCellValue('B' . $row, date('d/m/Y', strtotime($registro['fecha'])));
            $sheet->setCellValue('C' . $row, round($registro['total_horas'], 1));
            $row++;
        }
    }

    private function exportarHorasTotales($sheet, $horasTotales)
    {
        $sheet->setCellValue('A1', 'Usuario');
        $sheet->setCellValue('B1', 'Horas Totales');
        
        $row = 2;
        foreach ($horasTotales as $registro) {
            $sheet->setCellValue('A' . $row, $registro['nombre'] . ' ' . $registro['apellido']);
            $sheet->setCellValue('B' . $row, round($registro['total_horas'], 1));
            $row++;
        }
    }

    private function exportarHorasPorMes($sheet, $horasPorMes)
    {
        $sheet->setCellValue('A1', 'Usuario');
        $sheet->setCellValue('B1', 'Mes');
        $sheet->setCellValue('C1', 'Horas Totales');

        $row = 2;
        foreach ($horasPorMes as $registro) {
            $sheet->setCellValue('A' . $row, $registro['nombre'] . ' ' . $registro['apellido']);
            $sheet->setCellValue('B' . $row, date('M Y', strtotime($registro['mes'] . '-01')));
            $sheet->setCellValue('C' . $row, round($registro['total_horas'], 1));
            $row++;
        }

        // Ajustar ancho de columnas
        foreach (range('A', 'C') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }

    private function exportarRegistrosDetallados($sheet, $registros)
    {
        $sheet->setCellValue('A1', 'Usuario');
        $sheet->setCellValue('B1', 'Fecha');
        $sheet->setCellValue('C1', 'Entrada');
        $sheet->setCellValue('D1', 'Salida');
        $sheet->setCellValue('E1', 'Duración (horas)');
        $sheet->setCellValue('F1', 'Estado');
        
        $row = 2;
        foreach ($registros as $registro) {
            $sheet->setCellValue('A' . $row, $registro['nombre'] . ' ' . $registro['apellido']);
            $sheet->setCellValue('B' . $row, date('d/m/Y', strtotime($registro['hora_entrada'])));
            $sheet->setCellValue('C' . $row, date('H:i:s', strtotime($registro['hora_entrada'])));
            $sheet->setCellValue('D' . $row, $registro['hora_salida'] ? date('H:i:s', strtotime($registro['hora_salida'])) : 'En curso');
            $sheet->setCellValue('E' . $row, $registro['duracion'] ? round($registro['duracion'], 1) : '-');
            $sheet->setCellValue('F' . $row, $registro['hora_salida'] ? 'Finalizado' : 'Activo');
            $row++;
        }
    }
}
