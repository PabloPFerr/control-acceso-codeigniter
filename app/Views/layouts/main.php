<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <base href="<?= base_url() ?>">
    <title><?= $this->renderSection('title') ?> - Control de Acceso</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?= base_url('favicon.ico') ?>">
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/custom.css') ?>">
    
    <?= csrf_meta() ?>
    <?= $this->renderSection('styles') ?>
</head>
<body class="<?= session()->has('user') ? 'logged-in' : '' ?>">
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <a class="navbar-brand" href="<?= base_url() ?>">
                <i class="fas fa-fingerprint me-2"></i>
                Control de Acceso
            </a>
            <?php if (session()->has('user')): ?>
                <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                    <div class="user-info">
                        <i class="fas fa-user-circle"></i>
                        <span><?= session()->get('user')['nombre'] ?> <?= session()->get('user')['apellido'] ?></span>
                        <span class="badge <?= session()->get('user')['rol'] === 'admin' ? 'bg-accent' : 'bg-secondary' ?>">
                            <?= ucfirst(session()->get('user')['rol']) ?>
                        </span>
                        <a href="<?= base_url('logout') ?>" class="btn btn-outline-light btn-sm ms-3">
                            <i class="fas fa-sign-out-alt"></i>
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </nav>

    <?php if (session()->has('user')): ?>
    <!-- Sidebar -->
    <div class="sidebar">
        <ul class="nav flex-column">
            <?php if (session()->get('user')['rol'] === 'admin'): ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?= base_url('admin/dashboard') ?>">
                        <i class="fas fa-chart-line"></i>
                        <span>Dashboard Admin</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= base_url('admin/usuarios') ?>">
                        <i class="fas fa-users"></i>
                        <span>Usuarios</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= base_url('admin/reportes') ?>">
                        <i class="fas fa-file-alt"></i>
                        <span>Reportes</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= base_url('dashboard') ?>">
                        <i class="fas fa-clock"></i>
                        <span>Control de Asistencia</span>
                    </a>
                </li>
            <?php elseif (session()->get('user')['rol'] === 'auditor'): ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?= base_url('reportes') ?>">
                        <i class="fas fa-file-alt"></i>
                        <span>Reportes</span>
                    </a>
                </li>
            <?php else: ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?= base_url('dashboard') ?>">
                        <i class="fas fa-clock"></i>
                        <span>Control de Asistencia</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= base_url('user/reporte') ?>">
                        <i class="fas fa-file-alt"></i>
                        <span>Mi Reporte</span>
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
    <?php endif; ?>

    <!-- Main Content -->
    <div class="<?= session()->has('user') ? 'main-content' : 'container my-4' ?>">
        <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger fade-in">
            <i class="fas fa-exclamation-circle me-2"></i> <?= session()->getFlashdata('error') ?>
        </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success fade-in">
            <i class="fas fa-check-circle me-2"></i> <?= session()->getFlashdata('success') ?>
        </div>
        <?php endif; ?>

        <?= $this->renderSection('content') ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <?= $this->renderSection('scripts') ?>
    <script src="<?= base_url('assets/js/background.js') ?>"></script>
    <script src="<?= base_url('assets/js/sidebar.js') ?>"></script>
</body>
</html>
