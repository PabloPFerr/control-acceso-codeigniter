<!-- Gráficos -->
<div class="row mb-4">
    <?php if (isset($mostrarUsuario) && $mostrarUsuario): ?>
    <!-- Vista de admin: dos gráficos -->
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
    <?php else: ?>
    <!-- Vista de usuario: un gráfico -->
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
    <?php endif; ?>
