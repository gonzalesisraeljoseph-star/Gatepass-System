<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<style>

.page-header {
    background: #fff;
    color: #0f172a;
    padding: 26px 30px;
    border-radius: 22px;
    margin-bottom: 22px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 4px 20px rgba(15,23,42,.04);
}

.page-header h3 {
    font-weight: 700;
    margin-bottom: 4px;
}

.page-header small {
    color: #64748b;
}

.card-box {
    background: #fff;
    border-radius: 22px;
    box-shadow: 0 10px 30px rgba(0,0,0,.05);
    overflow: hidden;
}

.table thead th {
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: .05em;
    color: #64748b;
    background: #f8fafc;
    border-bottom: none;
}

.table tbody tr {
    border-color: #f1f5f9;
}

.table tbody tr:hover {
    background: #f8fafc;
}

.btn-light {
    border-radius: 12px;
    font-weight: 600;
}

.modal-content {
    border-radius: 18px;
    border: none;
    box-shadow: 0 20px 50px rgba(0,0,0,.15);
}

.modal-header {
    border-bottom: 1px solid #eef2f7;
    padding: 18px 20px;
}

.modal-title {
    font-weight: 700;
}

.form-control {
    border-radius: 12px;
    border: 1px solid #e2e8f0;
    height: 44px;
}

textarea.form-control {
    height: auto;
}

.badge-soft {
    padding: 6px 10px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 600;
    color: #fff !important;
}

.badge-pending { background: #f59e0b !important; }
.badge-approved { background: #22c55e !important; }
.badge-rejected { background: #ef4444 !important; }
</style>

<div class="body-wrapper">
<div class="container-fluid">

    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h3>Gate Pass Requests</h3>
            <small>Manage all asset exit requests and approvals</small>
        </div>

        <button class="btn btn-light shadow-sm"
                data-bs-toggle="modal"
                data-bs-target="#createModal">
            + New Request
        </button>
    </div>

    <div class="card-box">
        <div class="p-3">
            <table class="table align-middle" id="gatepassTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Requestor</th>
                        <th>Reason</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

</div>
</div>

<div class="modal fade" id="createModal">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">

        <div class="modal-header">
            <h5 class="modal-title">Create Gate Pass Request</h5>
            <button class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <form id="gatepassForm">
        <div class="modal-body p-4">

            <div class="mb-3">
                <label class="form-label fw-semibold">Requestor</label>
                <input class="form-control" name="requestor_name"
                       value="<?= session()->get('logged_in')['full_name'] ?? '' ?>"
                       readonly>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Reason</label>
                <textarea name="reason"
                          class="form-control"
                          rows="4"
                          placeholder="Enter purpose of gate pass..."
                          required></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Devices</label>
                <select name="devices[]"
                        id="deviceSelect"
                        class="form-control"
                        multiple
                        required></select>
            </div>

        </div>

        <div class="modal-footer">
            <button class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Submit Request</button>
        </div>
        </form>

    </div>
  </div>
</div>



<?= $this->endSection() ?>