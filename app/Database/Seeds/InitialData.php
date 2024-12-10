<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class InitialData extends Seeder
{
    public function run()
    {
        // Crear usuario administrador
        $data = [
            'nombre' => 'Admin',
            'apellido' => 'System',
            'email' => 'admin@system.com',
            'password' => password_hash('admin123', PASSWORD_DEFAULT),
            'rol' => 'admin',
            'activo' => true,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $this->db->table('usuarios')->insert($data);

        // Crear usuario normal de ejemplo
        $data = [
            'nombre' => 'Usuario',
            'apellido' => 'Demo',
            'email' => 'usuario@demo.com',
            'password' => password_hash('usuario123', PASSWORD_DEFAULT),
            'rol' => 'user',
            'activo' => true,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $this->db->table('usuarios')->insert($data);
    }
}
