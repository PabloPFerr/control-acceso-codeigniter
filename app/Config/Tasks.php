<?php

namespace Config;

use CodeIgniter\Tasks\Scheduler;

class Tasks extends \CodeIgniter\Config\BaseConfig
{
    /**
     * Register any tasks within this method for the application.
     */
    public function init(Scheduler $schedule)
    {
        // Ejecutar el comando de cierre de registros todos los días a las 00:01
        $schedule->command('registros:cerrar')->daily('00:01');
    }
}
