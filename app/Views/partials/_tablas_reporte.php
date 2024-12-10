<!-- Tabs de Reportes -->
<div class="card fade-in">
    <div class="card-header">
        <ul class="nav nav-tabs card-header-tabs">
            <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#registros">
                    <i class="fas fa-list"></i> Registros Detallados
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#horasPorDia">
                    <i class="fas fa-calendar-day"></i> Horas por Día
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#horasTotales">
                    <i class="fas fa-clock"></i> Horas Totales
                </a>
            </li>
        </ul>
    </div>
    <div class="card-body">
        <div class="tab-content">
            <!-- Registros Detallados -->
            <div class="tab-pane fade show active" id="registros">
                <div class="d-flex justify-content-end mb-4">
                    <button onclick="exportarExcel('detallado')" class="btn btn-success">
                        <i class="fas fa-file-excel me-2"></i> Exportar Detallado
                    </button>
                </div>
                <?php if (empty($registros)): ?>
                <div class="alert alert-info fade-in">
                    <i class="fas fa-info-circle me-2"></i> No se encontraron registros con los filtros seleccionados.
                </div>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover" id="tablaRegistros">
                        <thead>
                            <tr>
                                <?php if (isset($mostrarUsuario) && $mostrarUsuario): ?>
                                <th><i class="fas fa-user me-2"></i> Usuario</th>
                                <?php endif; ?>
                                <th><i class="fas fa-calendar me-2"></i> Fecha</th>
                                <th><i class="fas fa-clock me-2"></i> Entrada</th>
                                <th><i class="fas fa-clock me-2"></i> Salida</th>
                                <th><i class="fas fa-hourglass-half me-2"></i> Duración</th>
                                <th><i class="fas fa-circle me-2"></i> Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($registros as $registro): ?>
                            <tr>
                                <?php if (isset($mostrarUsuario) && $mostrarUsuario): ?>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-user-circle me-2 text-muted"></i>
                                        <?= esc($registro['nombre']) ?> <?= esc($registro['apellido']) ?>
                                    </div>
                                </td>
                                <?php endif; ?>
                                <td><?= date('d/m/Y', strtotime($registro['hora_entrada'])) ?></td>
                                <td><?= date('H:i', strtotime($registro['hora_entrada'])) ?></td>
                                <td><?= $registro['hora_salida'] ? date('H:i', strtotime($registro['hora_salida'])) : '<span class="badge bg-warning">En curso</span>' ?></td>
                                <td>
                                    <?php if ($registro['duracion']): ?>
                                        <span class="badge bg-info">
                                            <?= number_format($registro['duracion'], 2) ?> horas
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-<?= !$registro['hora_salida'] ? 'primary' : 'success' ?>">
                                        <?= !$registro['hora_salida'] ? 'Activo' : 'Finalizado' ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>

            <!-- Horas por Día -->
            <div class="tab-pane fade" id="horasPorDia">
                <div class="d-flex justify-content-end mb-4">
                    <button onclick="exportarExcel('por_dia')" class="btn btn-success">
                        <i class="fas fa-file-excel me-2"></i> Exportar Horas por Día
                    </button>
                </div>
                <?php if (empty($horasPorDia)): ?>
                <div class="alert alert-info fade-in">
                    <i class="fas fa-info-circle me-2"></i> No se encontraron registros con los filtros seleccionados.
                </div>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover" id="tablaHorasPorDia">
                        <thead>
                            <tr>
                                <?php if (isset($mostrarUsuario) && $mostrarUsuario): ?>
                                <th><i class="fas fa-user me-2"></i> Usuario</th>
                                <?php endif; ?>
                                <th><i class="fas fa-calendar me-2"></i> Fecha</th>
                                <th><i class="fas fa-clock me-2"></i> Horas</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($horasPorDia as $registro): ?>
                            <tr>
                                <?php if (isset($mostrarUsuario) && $mostrarUsuario): ?>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-user-circle me-2 text-muted"></i>
                                        <?= esc($registro['nombre']) ?> <?= esc($registro['apellido']) ?>
                                    </div>
                                </td>
                                <?php endif; ?>
                                <td><?= date('d/m/Y', strtotime($registro['fecha'])) ?></td>
                                <td>
                                    <span class="badge bg-info">
                                        <?= isset($registro['total_horas']) ? number_format($registro['total_horas'], 2) : '0.00' ?> horas
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>

            <!-- Horas Totales -->
            <div class="tab-pane fade" id="horasTotales">
                <div class="d-flex justify-content-end mb-4">
                    <button onclick="exportarExcel('totales')" class="btn btn-success">
                        <i class="fas fa-file-excel me-2"></i> Exportar Horas Totales
                    </button>
                </div>
                <?php if (empty($horasTotales)): ?>
                <div class="alert alert-info fade-in">
                    <i class="fas fa-info-circle me-2"></i> No se encontraron registros con los filtros seleccionados.
                </div>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover" id="tablaHorasTotales">
                        <thead>
                            <tr>
                                <th><i class="fas fa-user me-2"></i> Usuario</th>
                                <th><i class="fas fa-clock me-2"></i> Horas Totales</th>
                                <th><i class="fas fa-clock me-2"></i> Promedio Horas/Día</th>
                                <th><i class="fas fa-calendar me-2"></i> Días Trabajados</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($horasTotales as $registro): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-user-circle me-2 text-muted"></i>
                                        <?= esc($registro['nombre']) ?> <?= esc($registro['apellido']) ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-info">
                                        <?= isset($registro['total_horas']) ? number_format($registro['total_horas'], 2) : '0.00' ?> horas
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">
                                        <?= isset($registro['promedio_horas']) ? number_format($registro['promedio_horas'], 2) : '0.00' ?> horas/día
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-primary">
                                        <?= isset($registro['dias_trabajados']) ? $registro['dias_trabajados'] : '0' ?> días
                                    </span>
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
