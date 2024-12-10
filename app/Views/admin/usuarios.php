<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0">Gestión de Usuarios</h5>
                    <small class="text-muted">Administra los usuarios del sistema</small>
                </div>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalUsuario">
                    <i class="fas fa-plus"></i> Nuevo Usuario
                </button>
            </div>
            <div class="card-body">
                <?php if (empty($usuarios)): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> No hay usuarios registrados.
                </div>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th><i class="fas fa-user"></i> Nombre</th>
                                <th><i class="fas fa-envelope"></i> Email</th>
                                <th><i class="fas fa-tag"></i> Rol</th>
                                <th><i class="fas fa-circle"></i> Estado</th>
                                <th><i class="fas fa-clock"></i> Creado</th>
                                <th><i class="fas fa-cogs"></i> Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($usuarios as $usuario): ?>
                            <tr>
                                <td><?= esc($usuario['nombre']) ?> <?= esc($usuario['apellido']) ?></td>
                                <td><?= esc($usuario['email']) ?></td>
                                <td>
                                    <span class="badge bg-<?= $usuario['rol'] === 'admin' ? 'danger' : 'primary' ?>">
                                        <?= ucfirst($usuario['rol']) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-<?= $usuario['activo'] ? 'success' : 'secondary' ?>">
                                        <?= $usuario['activo'] ? 'Activo' : 'Inactivo' ?>
                                    </span>
                                </td>
                                <td><?= date('d/m/Y H:i', strtotime($usuario['created_at'])) ?></td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-outline-primary" 
                                            onclick="editarUsuario(<?= $usuario['id'] ?>, '<?= $usuario['nombre'] ?>', '<?= $usuario['apellido'] ?>', '<?= $usuario['email'] ?>', '<?= $usuario['rol'] ?>')"
                                            data-usuario='<?= json_encode($usuario) ?>'>
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-<?= $usuario['activo'] ? 'warning' : 'success' ?>"
                                            onclick="toggleEstado(<?= $usuario['id'] ?>, <?= $usuario['activo'] ?>)">
                                        <i class="fas fa-<?= $usuario['activo'] ? 'ban' : 'check' ?>"></i>
                                    </button>
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

<!-- Modal Usuario -->
<div class="modal fade" id="modalUsuario" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Nuevo Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formUsuario" action="<?= base_url('admin/usuarios/guardarUsuario') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <input type="hidden" name="id" id="userId">
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" required>
                    </div>
                    <div class="mb-3">
                        <label for="apellido" class="form-label">Apellido</label>
                        <input type="text" class="form-control" id="apellido" name="apellido" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Contraseña</label>
                        <input type="password" class="form-control" id="password" name="password">
                        <small class="text-muted">Dejar en blanco para mantener la actual</small>
                    </div>
                    <div class="mb-3">
                        <label for="rol" class="form-label">Rol</label>
                        <select class="form-select" id="rol" name="rol" required>
                            <option value="user">Usuario</option>
                            <option value="admin">Administrador</option>
                            <option value="auditor">Auditor</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Limpiar el formulario cuando se abre el modal para nuevo usuario
    document.getElementById('modalUsuario').addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const form = document.getElementById('formUsuario');
        form.reset();
        document.getElementById('userId').value = '';
        document.getElementById('modalTitle').textContent = 'Nuevo Usuario';
        // Si es nuevo usuario, el campo password es requerido
        document.getElementById('password').setAttribute('required', 'required');
    });

    // Cuando se edita un usuario existente
    window.editarUsuario = function(id, nombre, apellido, email, rol) {
        const modal = new bootstrap.Modal(document.getElementById('modalUsuario'));
        document.getElementById('userId').value = id;
        document.getElementById('nombre').value = nombre;
        document.getElementById('apellido').value = apellido;
        document.getElementById('email').value = email;
        document.getElementById('rol').value = rol;
        document.getElementById('modalTitle').textContent = 'Editar Usuario';
        // Si es edición, el campo password no es requerido
        document.getElementById('password').removeAttribute('required');
        modal.show();
    };
});

// Obtener el token CSRF del meta tag
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

function toggleEstado(id, estado) {
    if (!confirm('¿Estás seguro de cambiar el estado del usuario?')) return;
    
    fetch(`<?= base_url('admin/usuarios/toggle-estado/') ?>${id}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.error || 'Error al cambiar el estado');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al procesar la solicitud');
    });
}

document.getElementById('formUsuario').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch(this.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': csrfToken
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.error || 'Error al guardar el usuario');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al procesar la solicitud');
    });
});
</script>
<?= $this->endSection() ?>
