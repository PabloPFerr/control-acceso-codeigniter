<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\Registro;

class CerrarRegistros extends BaseCommand
{
    protected $group       = 'Registros';
    protected $name       = 'registros:cerrar';
    protected $description = 'Cierra automáticamente los registros que quedaron abiertos del día anterior';

    public function run(array $params)
    {
        $registro = new Registro();
        
        // Buscar registros abiertos del día anterior
        $registrosAbiertos = $registro->where('hora_salida IS NULL')
            ->where('DATE(hora_entrada) <', date('Y-m-d'))
            ->findAll();

        if (empty($registrosAbiertos)) {
            CLI::write('No hay registros abiertos para cerrar', 'green');
            return;
        }

        $count = 0;
        foreach ($registrosAbiertos as $reg) {
            // Establecer hora de salida a las 23:59:59 del mismo día
            $fechaEntrada = date('Y-m-d', strtotime($reg['hora_entrada']));
            $horaSalida = $fechaEntrada . ' 23:59:59';
            
            // Calcular duración
            $horaEntrada = new \DateTime($reg['hora_entrada']);
            $duracion = (new \DateTime($horaSalida))->diff($horaEntrada);
            $duracionHoras = $duracion->h + ($duracion->days * 24);

            // Actualizar registro
            $registro->update($reg['id'], [
                'hora_salida' => $horaSalida,
                'duracion' => $duracionHoras
            ]);

            $count++;
        }

        CLI::write("Se cerraron {$count} registros automáticamente", 'green');
    }
}
