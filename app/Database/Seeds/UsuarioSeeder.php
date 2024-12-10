<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UsuarioSeeder extends Seeder
{
    public function run()
    {
        $data = [
            'nombre'    => 'Admin',
            'apellido'  => 'Sistema',
            'email'     => 'admin@example.com',
            'password'  => password_hash('123456', PASSWORD_DEFAULT),
            'rol'       => 'admin',
            'activo'    => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $this->db->table('usuarios')->insert($data);
    }
}
