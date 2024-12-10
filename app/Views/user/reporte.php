<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-12">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0">Mi Reporte de Asistencia</h5>
                    <small class="text-muted">Visualiza tus registros de asistencia</small>
                </div>
            </div>
            <div class="card-body">
                <form method="get" class="row g-3">
                    <div class="col-md-6">
                        <label for="fecha_inicio" class="form-label">Fecha Inicio</label>
                        <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" 
                               value="<?= $filtros['fecha_inicio'] ?>">
                    </div>
                    <div class="col-md-6">
                        <label for="fecha_fin" class="form-label">Fecha Fin</label>
                        <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" 
                               value="<?= $filtros['fecha_fin'] ?>">
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Filtrar
                        </button>
                        <a href="<?= base_url('user/reporte') ?>" class="btn btn-secondary">
                            <i class="fas fa-undo"></i> Limpiar Filtros
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <?php 
        echo $this->include('partials/_tablas_reporte');
        ?>

        <!-- Gráficos -->
        <div class="row mb-4">
            <div class="col-md-12 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Horas Trabajadas por Día</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="graficoLineas"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Datos para los gráficos
const datosLineas = <?= $datosGraficoLineas ?>;

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
                text: 'Horas por Día'
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
</script>
<?= $this->endSection() ?>
