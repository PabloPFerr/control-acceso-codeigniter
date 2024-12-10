<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Rutas de autenticación
$routes->get('/auth/test', 'Auth::test');
$routes->get('login', 'Auth::index');
$routes->post('login', 'Auth::login');
$routes->get('logout', 'Auth::logout');

// Rutas de registro de asistencia
$routes->post('registro/entrada', 'Registro::entrada');
$routes->post('registro/salida', 'Registro::salida');

// Rutas protegidas
$routes->get('dashboard', 'Dashboard::index', ['filter' => 'auth']);
$routes->get('user/reporte', 'User::reporte', ['filter' => 'auth']);

// Ruta de reportes (accesible por admin y auditores)
$routes->get('reportes', 'Admin::reportes', ['filter' => 'auth']);

// Rutas de administrador
$routes->group('admin', ['filter' => 'auth'], function($routes) {
    $routes->get('dashboard', 'Admin::dashboard');
    $routes->get('usuarios', 'Admin::usuarios');
    $routes->get('reportes', 'Admin::reportes');
    $routes->post('usuarios/guardarUsuario', 'Admin::guardarUsuario');
    $routes->post('usuarios/toggle-estado/(:num)', 'Admin::toggleEstado/$1');
});

// Ruta por defecto
$routes->get('/', function() {
    return redirect()->to(base_url('login'));
});
