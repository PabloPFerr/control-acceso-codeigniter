<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class User extends BaseController
{
    protected $registrosModel;

    public function __construct()
    {
        $this->registrosModel = model('RegistrosModel');
    }

    public function reporte()
    {
        $userId = session()->get('user')['id'];
        $filtros = [
            'usuario_id' => $userId,
            'fecha_inicio' => $this->request->getGet('fecha_inicio'),
            'fecha_fin' => $this->request->getGet('fecha_fin')
        ];

        // Obtener datos para los gráficos
        $horasPorDia = $this->registrosModel->getHorasPorDia($filtros);
        $horasPorMes = $this->registrosModel->getHorasPorMes($filtros);

        // Preparar datos para el gráfico de líneas
        $labels = array_column($horasPorDia, 'fecha');
        $valores = array_column($horasPorDia, 'horas');

        $datosGraficoLineas = [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Horas por día',
                    'data' => $valores,
                    'fill' => false,
                    'borderColor' => 'rgb(75, 192, 192)',
                    'tension' => 0.1
                ]
            ]
        ];

        $data = [
            'titulo' => 'Mi Reporte',
            'horasPorDia' => $horasPorDia,
            'horasTotales' => $this->registrosModel->getHorasTotales($filtros),
            'horasPorMes' => $horasPorMes,
            'filtros' => $filtros,
            'datosGraficoLineas' => json_encode($datosGraficoLineas)
        ];

        return view('user/reporte', $data);
    }
}
