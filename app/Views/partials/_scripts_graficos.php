<script>
// Datos para los gráficos
const datosLineas = <?= $datosGraficoLineas ?>;
<?php if (isset($mostrarUsuario) && $mostrarUsuario): ?>
const datosBarra = <?= $datosGraficoBarra ?>;
<?php endif; ?>

// Gráfico de líneas
const ctxLineas = document.getElementById('graficoLineas').getContext('2d');
new Chart(ctxLineas, {
    type: 'line',
    data: datosLineas,
    options: {
        responsive: true,
        plugins: {
            title: {
                display: true,
                text: <?= isset($mostrarUsuario) && $mostrarUsuario ? "'Horas por Usuario y Día'" : "'Horas por Día'" ?>
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                title: {
                    display: true,
                    text: 'Horas'
                }
            }
        }
    }
});

<?php if (isset($mostrarUsuario) && $mostrarUsuario): ?>
// Gráfico de barras (solo para admin)
const ctxBarra = document.getElementById('graficoBarra').getContext('2d');
new Chart(ctxBarra, {
    type: 'bar',
    data: datosBarra,
    options: {
        responsive: true,
        plugins: {
            title: {
                display: true,
                text: 'Horas Totales por Usuario'
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                title: {
                    display: true,
                    text: 'Horas Totales'
                }
            }
        }
    }
});
<?php endif; ?></script>
