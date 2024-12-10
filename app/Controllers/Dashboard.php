<?php

namespace App\Controllers;

use App\Models\Registro;
use CodeIgniter\Controller;

class Dashboard extends Controller
{
    protected $registro;

    public function __construct()
    {
        $this->registro = new Registro();
        helper(['form']);
    }

    public function index()
    {
        $session = session();
        if (!$session->has('user')) {
            return redirect()->to(site_url('login'));
        }

        $user = $session->get('user');
        $registros = $this->registro
            ->where('usuario_id', $user['id'])
            ->orderBy('hora_entrada', 'DESC')
            ->findAll();

        return view('dashboard', [
            'registros' => $registros,
            'user' => $user
        ]);
    }

    public function registrarEntrada()
    {
        log_message('debug', '=== Iniciando registro de entrada ===');
        log_message('debug', 'Headers: ' . print_r(apache_request_headers(), true));
        log_message('debug', 'POST data: ' . print_r($this->request->getPost(), true));
        log_message('debug', 'CSRF Token Name: ' . csrf_token());
        log_message('debug', 'CSRF Cookie: ' . get_cookie('csrf_cookie_name'));
        log_message('debug', 'CSRF Header: ' . $this->request->getHeaderLine('X-CSRF-TOKEN'));

        $session = session();
        if (!$session->has('user')) {
            return $this->response->setJSON(['error' => 'No autorizado'])->setStatusCode(401);
        }

        $user = $session->get('user');
        
        // Verificar si ya existe un registro abierto
        $registroAbierto = $this->registro
            ->where('usuario_id', $user['id'])
            ->where('hora_salida IS NULL')
            ->first();

        if ($registroAbierto) {
            return $this->response->setJSON(['error' => 'Ya tienes un registro abierto'])->setStatusCode(400);
        }

        $data = [
            'usuario_id' => $user['id'],
            'hora_entrada' => date('Y-m-d H:i:s')
        ];

        if ($this->registro->insert($data)) {
            return $this->response->setJSON(['message' => 'Entrada registrada correctamente']);
        }

        return $this->response->setJSON(['error' => 'Error al registrar entrada'])->setStatusCode(500);
    }

    public function registrarSalida()
    {
        log_message('debug', '=== Iniciando registro de salida ===');
        log_message('debug', 'Headers: ' . print_r(apache_request_headers(), true));
        log_message('debug', 'POST data: ' . print_r($this->request->getPost(), true));
        log_message('debug', 'CSRF Token Name: ' . csrf_token());
        log_message('debug', 'CSRF Cookie: ' . get_cookie('csrf_cookie_name'));
        log_message('debug', 'CSRF Header: ' . $this->request->getHeaderLine('X-CSRF-TOKEN'));

        $session = session();
        if (!$session->has('user')) {
            return $this->response->setJSON(['error' => 'No autorizado'])->setStatusCode(401);
        }

        $user = $session->get('user');
        
        // Buscar el último registro abierto
        $registroAbierto = $this->registro
            ->where('usuario_id', $user['id'])
            ->where('hora_salida IS NULL')
            ->first();

        if (!$registroAbierto) {
            return $this->response->setJSON(['error' => 'No hay registro de entrada abierto'])->setStatusCode(400);
        }

        $horaSalida = date('Y-m-d H:i:s');
        $horaEntrada = new \DateTime($registroAbierto['hora_entrada']);
        $duracion = (new \DateTime($horaSalida))->diff($horaEntrada);
        $duracionHoras = $duracion->h + ($duracion->days * 24);

        $data = [
            'hora_salida' => $horaSalida,
            'duracion' => $duracionHoras
        ];

        if ($this->registro->update($registroAbierto['id'], $data)) {
            return $this->response->setJSON(['message' => 'Salida registrada correctamente']);
        }

        return $this->response->setJSON(['error' => 'Error al registrar salida'])->setStatusCode(500);
    }
}
