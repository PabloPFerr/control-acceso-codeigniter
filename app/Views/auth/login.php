<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-md-6 col-lg-4">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">Iniciar Sesión</h4>
            </div>
            <div class="card-body">
                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger">
                        <?= session()->getFlashdata('error') ?>
                    </div>
                <?php endif; ?>
                
                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success">
                        <?= session()->getFlashdata('success') ?>
                    </div>
                <?php endif; ?>

                <form action="<?= base_url('login') ?>" method="post" class="needs-validation" novalidate>
                    <?= csrf_field() ?>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" 
                               class="form-control <?= session('errors.email') ? 'is-invalid' : '' ?>" 
                               id="email" 
                               name="email" 
                               value="<?= old('email') ?>"
                               required>
                        <?php if (session('errors.email')) : ?>
                            <div class="invalid-feedback">
                                <?= session('errors.email') ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Contraseña</label>
                        <input type="password" 
                               class="form-control <?= session('errors.password') ? 'is-invalid' : '' ?>" 
                               id="password" 
                               name="password" 
                               required>
                        <?php if (session('errors.password')) : ?>
                            <div class="invalid-feedback">
                                <?= session('errors.password') ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Iniciar Sesión</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
