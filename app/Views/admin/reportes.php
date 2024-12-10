<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-12">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0">Reportes de Asistencia</h5>
                    <small class="text-muted">Filtra y visualiza los registros de asistencia</small>
                </div>
            </div>
            <div class="card-body">
                <form method="get" class="row g-3">
                    <div class="col-md-4">
                        <label for="usuario_id" class="form-label">Usuario</label>
                        <select name="usuario_id" id="usuario_id" class="form-select">
                            <option value="">Todos los usuarios</option>
                            <?php foreach ($usuarios as $usuario): ?>
                            <option value="<?= $usuario['id'] ?>" <?= $filtros['usuario_id'] == $usuario['id'] ? 'selected' : '' ?>>
                                <?= esc($usuario['nombre']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="fecha_inicio" class="form-label">Fecha Inicio</label>
                        <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" 
                               value="<?= $filtros['fecha_inicio'] ?>">
                    </div>
                    <div class="col-md-4">
                        <label for="fecha_fin" class="form-label">Fecha Fin</label>
                        <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" 
                               value="<?= $filtros['fecha_fin'] ?>">
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Filtrar
                        </button>
                        <a href="<?= base_url('admin/reportes') ?>" class="btn btn-secondary">
                            <i class="fas fa-undo"></i> Limpiar Filtros
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <?php 
        echo $this->include('partials/_tablas_reporte');
        echo $this->include('partials/_graficos_reporte');
        ?>

        <!-- Gráficos -->
        <div class="row mb-4">
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Horas por Usuario y Día</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="graficoLineas"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Horas Totales por Usuario</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="graficoBarra"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<?= $this->include('partials/_scripts_reporte') ?>
<?= $this->include('partials/_scripts_graficos') ?>

<script>
// Datos para los gráficos
const datosLineas = <?= $datosGraficoLineas ?>;
const datosBarra = <?= $datosGraficoBarra ?>;

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
                text: 'Horas por Usuario y Día'
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

// Gráfico de barras
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
                    text: 'Horas'
                }
            }
        }
    }
});
</script>
<?= $this->endSection() ?>
