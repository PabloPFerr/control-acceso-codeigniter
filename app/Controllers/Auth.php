<?php

namespace App\Controllers;

use App\Models\Usuario;
use CodeIgniter\Controller;

class Auth extends Controller
{
    protected $usuario;

    public function __construct()
    {
        $this->usuario = new Usuario();
        helper(['form', 'url']);
    }

    public function index()
    {
        // Si ya está logueado, redirigir al dashboard
        if (session()->get('isLoggedIn')) {
            $user = session()->get('user');
            if ($user['rol'] === 'admin') {
                return redirect()->to('/admin/dashboard');
            }
            return redirect()->to('/dashboard');
        }

        // Mostrar el formulario de login
        return view('auth/login', [
            'title' => 'Iniciar Sesión'
        ]);
    }

    public function login()
    {
        log_message('debug', '==== Procesando intento de login ====');
        log_message('debug', 'POST data: ' . print_r($this->request->getPost(), true));
        
        // Validación
        $rules = [
            'email' => [
                'rules' => 'required|valid_email',
                'errors' => [
                    'required' => 'El email es requerido',
                    'valid_email' => 'Por favor ingrese un email válido'
                ]
            ],
            'password' => [
                'rules' => 'required|min_length[6]',
                'errors' => [
                    'required' => 'La contraseña es requerida',
                    'min_length' => 'La contraseña debe tener al menos 6 caracteres'
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            log_message('debug', 'Validación fallida: ' . print_r($this->validator->getErrors(), true));
            return redirect()->back()
                           ->withInput()
                           ->with('errors', $this->validator->getErrors());
        }

        // Intentar login
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');
        
        $user = $this->usuario->where('email', $email)->first();
        
        if (!$user) {
            log_message('debug', 'Usuario no encontrado: ' . $email);
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Credenciales inválidas');
        }

        if (!password_verify($password, $user['password'])) {
            log_message('debug', 'Contraseña incorrecta para usuario: ' . $email);
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Credenciales inválidas');
        }

        if (!$user['activo']) {
            log_message('debug', 'Usuario inactivo: ' . $email);
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Usuario inactivo');
        }

        // Login exitoso
        log_message('info', 'Login exitoso para usuario: ' . $email);
        
        $userData = [
            'id' => $user['id'],
            'nombre' => $user['nombre'],
            'apellido' => $user['apellido'],
            'email' => $user['email'],
            'rol' => $user['rol']
        ];
        
        session()->set('user', $userData);
        session()->set('isLoggedIn', true);
        
        // Redirigir según el rol
        switch ($user['rol']) {
            case 'admin':
                return redirect()->to(base_url('admin/dashboard'));
            case 'auditor':
                return redirect()->to(base_url('reportes'));
            default:
                return redirect()->to(base_url('dashboard'));
        }
    }

    public function logout()
    {
        session()->remove('user');
        session()->remove('isLoggedIn');
        return redirect()->to('/login');
    }
}
