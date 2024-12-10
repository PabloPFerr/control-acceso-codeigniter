<?php
$validation = \Config\Services::validation();
?>

<div class="container-fluid px-4">
    <h1 class="mt-4"><?= $title ?></h1>
    
    <?php if(session()->getFlashdata('msg')): ?>
        <div class="alert alert-<?= session()->getFlashdata('msg_type') ?>">
            <?= session()->getFlashdata('msg') ?>
        </div>
    <?php endif; ?>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-user-plus"></i>
            <?= isset($usuario) ? 'Editar Usuario' : 'Nuevo Usuario' ?>
        </div>
        <div class="card-body">
            <form action="<?= isset($usuario) ? base_url('admin/usuarios/update/'.$usuario['id']) : base_url('admin/usuarios/create') ?>" method="post">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="nombre" class="form-label">Nombre</label>
                        <input type="text" class="form-control <?= ($validation->hasError('nombre')) ? 'is-invalid' : '' ?>" 
                               id="nombre" name="nombre" value="<?= set_value('nombre', isset($usuario) ? $usuario['nombre'] : '') ?>">
                        <?php if($validation->hasError('nombre')): ?>
                            <div class="invalid-feedback">
                                <?= $validation->getError('nombre') ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="col-md-6">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control <?= ($validation->hasError('email')) ? 'is-invalid' : '' ?>" 
                               id="email" name="email" value="<?= set_value('email', isset($usuario) ? $usuario['email'] : '') ?>">
                        <?php if($validation->hasError('email')): ?>
                            <div class="invalid-feedback">
                                <?= $validation->getError('email') ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="password" class="form-label">Contraseña<?= !isset($usuario) ? ' *' : ' (dejar en blanco para mantener)' ?></label>
                        <input type="password" class="form-control <?= ($validation->hasError('password')) ? 'is-invalid' : '' ?>" 
                               id="password" name="password">
                        <?php if($validation->hasError('password')): ?>
                            <div class="invalid-feedback">
                                <?= $validation->getError('password') ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="col-md-6">
                        <label for="role" class="form-label">Rol</label>
                        <select class="form-select <?= ($validation->hasError('role')) ? 'is-invalid' : '' ?>" 
                                id="role" name="role">
                            <option value="user" <?= set_select('role', 'user', isset($usuario) && $usuario['role'] == 'user') ?>>Usuario</option>
                            <option value="admin" <?= set_select('role', 'admin', isset($usuario) && $usuario['role'] == 'admin') ?>>Administrador</option>
                            <option value="auditor" <?= set_select('role', 'auditor', isset($usuario) && $usuario['role'] == 'auditor') ?>>Auditor</option>
                        </select>
                        <?php if($validation->hasError('role')): ?>
                            <div class="invalid-feedback">
                                <?= $validation->getError('role') ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="estado" class="form-label">Estado</label>
                        <select class="form-select" id="estado" name="estado">
                            <option value="1" <?= set_select('estado', '1', isset($usuario) && $usuario['estado'] == 1) ?>>Activo</option>
                            <option value="0" <?= set_select('estado', '0', isset($usuario) && $usuario['estado'] == 0) ?>>Inactivo</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Guardar
                    </button>
                    <a href="<?= base_url('admin/usuarios') ?>" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
