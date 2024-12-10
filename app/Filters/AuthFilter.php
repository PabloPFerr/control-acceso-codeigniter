<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Si no está logueado, redirigir a login
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        $uri = trim($request->getUri()->getPath(), '/');
        $role = session()->get('user')['rol'];

        // Verificar acceso a rutas admin
        if (strpos($uri, 'admin') === 0 && $role !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Acceso denegado');
        }

        // Verificar acceso a reportes
        if ($uri === 'reportes') {
            if ($role !== 'admin' && $role !== 'auditor') {
                return redirect()->to('/dashboard')->with('error', 'Acceso denegado');
            }
        }

        // Restringir acceso al dashboard para auditores
        if ($uri === 'dashboard' && $role === 'auditor') {
            return redirect()->to('/reportes')->with('error', 'Los auditores solo tienen acceso a reportes');
        }

        return $request;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No hacemos nada después de la solicitud
    }
}
