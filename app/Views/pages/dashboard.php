<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?php
    $session  = session()->get('logged_in');
   
    date_default_timezone_set('Asia/Manila');
?>
<style>
.welcome-icon{
    width:80px;
    height:80px;
    border-radius:20px;
    background:linear-gradient(135deg,#2563eb,#4f46e5);
    color:#fff;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:34px;
    box-shadow:0 15px 35px rgba(37,99,235,.20);
}

.card{
    border-radius:20px;
}
</style>

      <div class="body-wrapper">
        <div class="container-fluid">

            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-body p-5">

                    <div class="row align-items-center">

                        <div class="col-lg-8">

                            <div class="d-flex align-items-center mb-4">

                                <div class="welcome-icon me-4">
                                    <i class="ti ti-shield-check"></i>
                                </div>

                                <div>
                                    <h2 class="fw-bold mb-1">
                                        Welcome to GatePass Management System
                                    </h2>

                                    <h5 class="text-primary mb-0">
                                    <?= $session['full_name'] ?? ''; ?>
                                    </h5>

                                    <small class="text-muted">
                                     <?= $session['department_name'] ?? ''; ?> | <?= $session['position_name'] ?? ''; ?>
                                    </small>
                                </div>

                            </div>

                            <p class="text-muted fs-4 mb-4">
                                This system is designed to streamline the management of gate pass requests,
                                monitor company assets being brought in or out of the premises,
                                track approvals, and maintain a secure record of all gate pass transactions.
                            </p>

                            <div class="row">

                                <div class="col-md-4">
                                    <div class="border rounded-3 p-3 bg-light">
                                        <h6 class="fw-bold mb-1">Create Request</h6>
                                        <small class="text-muted">
                                            Submit a new gate pass request for approval.
                                        </small>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="border rounded-3 p-3 bg-light">
                                        <h6 class="fw-bold mb-1">Track Status</h6>
                                        <small class="text-muted">
                                            Monitor pending, approved, and rejected requests.
                                        </small>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="border rounded-3 p-3 bg-light">
                                        <h6 class="fw-bold mb-1">Manage Assets</h6>
                                        <small class="text-muted">
                                            Keep records of company equipment and accessories.
                                        </small>
                                    </div>
                                </div>

                            </div>

                        </div>

                        <div class="col-lg-4">

                            <div class="card bg-primary text-white border-0 shadow-sm">
                                <div class="card-body">

                                    <h5 class="fw-bold">
                                        System Information
                                    </h5>

                                    <hr class="border-light">

                                    <div class="mb-3">
                                        <small class="opacity-75">Current Date</small>
                                        <div class="fw-semibold">
                                            <?php echo date('F d, Y'); ?>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <small class="opacity-75">Current Time</small>
                                        <div class="fw-semibold">
                                            <?php echo date('h:i A'); ?>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <small class="opacity-75">Account Name</small>
                                        <div class="fw-semibold">
                                            <?= $session['full_name'] ?? ''; ?>
                                        </div>
                                    </div>

                                    <div>
                                        <small class="opacity-75">System Status</small>
                                        <div class="fw-semibold">
                                            <span class="badge bg-success">
                                                Online
                                            </span>
                                        </div>
                                    </div>

                                </div>
                            </div>

                        </div>

                    </div>

                </div>
            </div>

        </div>
      </div>
   
<?= $this->endSection() ?>