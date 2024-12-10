<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-12">
        <div class="card mb-4">
            <div class="card-body">
                <h4 class="card-title">Control de Asistencia</h4>
                <div class="d-flex gap-2">
                    <form id="formEntrada" action="<?= base_url('registro/entrada') ?>" method="post" class="d-inline">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-sign-in-alt"></i> Registrar Entrada
                        </button>
                    </form>
                    
                    <form id="formSalida" action="<?= base_url('registro/salida') ?>" method="post" class="d-inline">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-sign-out-alt"></i> Registrar Salida
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Mis Registros</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Hora Entrada</th>
                                <th>Hora Salida</th>
                                <th>Duración (horas)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($registros as $registro): ?>
                            <tr>
                                <td><?= date('d/m/Y', strtotime($registro['hora_entrada'])) ?></td>
                                <td><?= date('H:i:s', strtotime($registro['hora_entrada'])) ?></td>
                                <td><?= $registro['hora_salida'] ? date('H:i:s', strtotime($registro['hora_salida'])) : '-' ?></td>
                                <td><?= $registro['duracion'] ?? '-' ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    $('#formEntrada').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if (response.message) {
                    location.reload();
                } else {
                    alert(response.error);
                }
            },
            error: function(xhr) {
                console.log('Error:', xhr);
                alert(xhr.responseJSON?.error || 'Error al registrar entrada');
            }
        });
    });

    $('#formSalida').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if (response.message) {
                    location.reload();
                } else {
                    alert(response.error);
                }
            },
            error: function(xhr) {
                console.log('Error:', xhr);
                alert(xhr.responseJSON?.error || 'Error al registrar salida');
            }
        });
    });
});
</script>
<?= $this->endSection() ?>
