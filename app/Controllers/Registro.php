<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;

class Registro extends ResourceController
{
    use ResponseTrait;

    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    public function entrada()
    {
        try {
            $session = session();
            $user = $session->get('user');
            
            if (!$user || !isset($user['id'])) {
                return $this->respond([
                    'error' => 'Usuario no autenticado'
                ], 401);
            }

            $userId = $user['id'];
            
            // Verificar si ya existe una entrada sin salida
            $existingEntry = $this->db->table('registros')
                ->where('usuario_id', $userId)
                ->where('hora_salida IS NULL')
                ->get()
                ->getRow();

            if ($existingEntry) {
                return $this->respond([
                    'error' => 'Ya tienes una entrada registrada sin salida'
                ], 400);
            }

            // Registrar nueva entrada
            $data = [
                'usuario_id' => $userId,
                'hora_entrada' => date('Y-m-d H:i:s')
            ];

            $this->db->table('registros')->insert($data);

            return $this->respond([
                'message' => 'Entrada registrada correctamente'
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error al registrar entrada: ' . $e->getMessage());
            return $this->respond([
                'error' => 'Error al registrar entrada'
            ], 500);
        }
    }

    public function salida()
    {
        try {
            $session = session();
            $user = $session->get('user');
            
            if (!$user || !isset($user['id'])) {
                return $this->respond([
                    'error' => 'Usuario no autenticado'
                ], 401);
            }

            $userId = $user['id'];
            
            // Buscar entrada sin salida
            $entry = $this->db->table('registros')
                ->where('usuario_id', $userId)
                ->where('hora_salida IS NULL')
                ->get()
                ->getRow();

            if (!$entry) {
                return $this->respond([
                    'error' => 'No hay una entrada registrada'
                ], 400);
            }

            // Registrar salida
            $horaSalida = date('Y-m-d H:i:s');
            $horaEntrada = new \DateTime($entry->hora_entrada);
            $horaSalidaObj = new \DateTime($horaSalida);
            $duracion = $horaEntrada->diff($horaSalidaObj);
            $duracionHoras = $duracion->h + ($duracion->days * 24);

            $this->db->table('registros')
                ->where('id', $entry->id)
                ->update([
                    'hora_salida' => $horaSalida,
                    'duracion' => $duracionHoras
                ]);

            return $this->respond([
                'message' => 'Salida registrada correctamente'
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error al registrar salida: ' . $e->getMessage());
            return $this->respond([
                'error' => 'Error al registrar salida'
            ], 500);
        }
    }
}
