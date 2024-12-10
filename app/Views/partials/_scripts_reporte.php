<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tabla de registros detallados
    const tablaRegistros = new DataTable('#tablaRegistros', {
        order: [[1, 'desc'], [2, 'asc']], // Ordenar por fecha desc y hora asc por defecto
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
        },
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'excel',
                text: '<i class="fas fa-file-excel"></i> Exportar a Excel',
                className: 'btn btn-success',
                title: 'Registros Detallados'
            }
        ],
        pageLength: 25,
        orderMulti: true, // Habilitar ordenamiento múltiple
        columnDefs: [
            {
                targets: -1, // Última columna (estado)
                orderable: false
            }
        ]
    });

    // Tabla de horas por día
    const tablaHorasDia = new DataTable('#tablaHorasPorDia', {
        order: [[0, 'desc']], // Ordenar por fecha desc
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
        },
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'excel',
                text: '<i class="fas fa-file-excel"></i> Exportar a Excel',
                className: 'btn btn-success',
                title: 'Horas por Día'
            }
        ],
        pageLength: 25,
        orderMulti: true // Habilitar ordenamiento múltiple
    });

    // Tabla de horas totales
    const tablaHorasTotales = new DataTable('#tablaHorasTotales', {
        order: [[1, 'desc']], // Ordenar por total de horas por defecto
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
        },
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'excel',
                text: '<i class="fas fa-file-excel"></i> Exportar a Excel',
                className: 'btn btn-success',
                title: 'Horas Totales'
            }
        ],
        pageLength: 25,
        orderMulti: true // Habilitar ordenamiento múltiple
    });

    // Función para exportar a Excel
    window.exportarExcel = function(tipo) {
        // Obtener los parámetros actuales de la URL
        const urlParams = new URLSearchParams(window.location.search);
        
        // Agregar los parámetros de exportación
        urlParams.set('export', 'excel');
        urlParams.set('tipo_reporte', tipo);
        
        // Redirigir a la misma página con los parámetros de exportación
        window.location.href = `${window.location.pathname}?${urlParams.toString()}`;
    }
});</script>
