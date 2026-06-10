<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>


<div class="container-fluid py-4">

    <!-- Welcome Card -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
        <div class="card-body p-5"
             style="background: linear-gradient(135deg, #0d6efd, #4f8cff); color: white;">

            <div class="d-flex justify-content-between align-items-start flex-wrap">

                <div>
                    <h1 class="fw-bold mb-2">
                        Welcome to Kinect Gatepass System
                    </h1>

                    <div class="mb-3">
                        <span class="badge bg-light text-dark px-3 py-2 rounded-pill">
                            <i class="fas fa-user me-2"></i>
                            Hello <?= esc($username) ?>,
                        </span>
                    </div>

                    <p class="mb-0 fs-6 opacity-75">
                        Monitor visitors, employee gatepasses, and approvals in one modern dashboard.
                    </p>
                </div>

            </div>

        </div>
    </div>

    <!-- Dashboard Cards -->
    <div class="row g-4">

        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body text-center p-4">
                    <div class="mb-3">
                        <i class="fas fa-id-card fa-3x text-primary"></i>
                    </div>

                    <h5 class="fw-bold">Gatepass Records</h5>

                    <p class="text-muted mb-0">
                        View and manage all submitted gatepasses.
                    </p>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body text-center p-4">
                    <div class="mb-3">
                        <i class="fas fa-users fa-3x text-success"></i>
                    </div>

                    <h5 class="fw-bold">Visitors</h5>

                    <p class="text-muted mb-0">
                        Track visitor entries and exits in real-time.
                    </p>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body text-center p-4">
                    <div class="mb-3">
                        <i class="fas fa-shield-alt fa-3x text-warning"></i>
                    </div>

                    <h5 class="fw-bold">Security</h5>

                    <p class="text-muted mb-0">
                        Ensure secure and organized gate monitoring.
                    </p>
                </div>
            </div>
        </div>

    </div>

</div>

<?= $this->endSection() ?>