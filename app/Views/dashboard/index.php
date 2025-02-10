<?= $this->include('layouts/head') ?>
<?= $this->include('layouts/sidebar') ?>
<?= $this->include('layouts/navbar') ?>

    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header text-center">
                        <h2>Welcome, <?= session()->get('username') ?></h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
<?= $this->include('layouts/footer') ?>
