<?php

namespace App\Models;

use CodeIgniter\Model;

class Usuario extends Model
{
    protected $table = 'usuarios';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'nombre',
        'apellido',
        'email',
        'password',
        'rol',
        'activo'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'nombre'   => 'required|min_length[3]',
        'apellido' => 'required|min_length[3]',
        'email'    => 'required|valid_email|is_unique[usuarios.email,id,{id}]',
        'password' => 'required|min_length[6]',
        'rol'      => 'required|in_list[admin,user,auditor]',
    ];
}
