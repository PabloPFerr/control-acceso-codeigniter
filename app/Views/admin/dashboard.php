<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card stats-card bg-primary text-white fade-in">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0">Total Usuarios</h6>
                        <h2 class="my-2 display-4"><?= $totalUsuarios ?></h2>
                    </div>
                    <div class="text-white">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stats-card bg-success text-white fade-in">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0">Usuarios Activos</h6>
                        <h2 class="my-2 display-4"><?= $usuariosActivos ?></h2>
                    </div>
                    <div class="text-white">
                        <i class="fas fa-user-check"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stats-card bg-info text-white fade-in">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0">Registros Hoy</h6>
                        <h2 class="my-2 display-4"><?= count($registrosHoy) ?></h2>
                    </div>
                    <div class="text-white">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card fade-in">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0">Registros de Hoy</h5>
                    <small class="text-muted"><?= date('d/m/Y') ?></small>
                </div>
                <div>
                    <a href="<?= base_url('admin/usuarios') ?>" class="btn btn-outline-primary me-2">
                        <i class="fas fa-users me-1"></i> Gestionar Usuarios
                    </a>
                    <a href="<?= base_url('admin/reportes') ?>" class="btn btn-primary">
                        <i class="fas fa-chart-bar me-1"></i> Ver Reportes
                    </a>
                </div>
            </div>
            <div class="card-body">
                <?php if (empty($registrosHoy)): ?>
                <div class="alert alert-info fade-in">
                    <i class="fas fa-info-circle me-2"></i> No hay registros para el día de hoy.
                </div>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th><i class="fas fa-user me-1"></i> Usuario</th>
                                <th><i class="fas fa-clock me-1"></i> Hora Entrada</th>
                                <th><i class="fas fa-clock me-1"></i> Hora Salida</th>
                                <th><i class="fas fa-hourglass-half me-1"></i> Duración</th>
                                <th><i class="fas fa-circle me-1"></i> Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($registrosHoy as $registro): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-user-circle me-2 text-muted"></i>
                                        <?= esc($registro['nombre']) ?> <?= esc($registro['apellido']) ?>
                                    </div>
                                </td>
                                <td><?= date('H:i:s', strtotime($registro['hora_entrada'])) ?></td>
                                <td>
                                    <?php if ($registro['hora_salida']): ?>
                                        <?= date('H:i:s', strtotime($registro['hora_salida'])) ?>
                                    <?php else: ?>
                                        <span class="badge bg-warning">En curso</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($registro['duracion']): ?>
                                        <span class="badge bg-info">
                                            <?= number_format($registro['duracion'], 1) ?> horas
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($registro['hora_salida']): ?>
                                        <span class="badge bg-success">Finalizado</span>
                                    <?php else: ?>
                                        <span class="badge bg-primary">Activo</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
