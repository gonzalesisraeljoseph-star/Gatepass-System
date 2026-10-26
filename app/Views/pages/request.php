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
            <h3>Requests</h3>
            <small>Submit and track gate pass and IT accountability requests</small>
        </div>

        <?php if ($canCreateRequest ?? false) : ?>
            <button class="btn btn-primary" type="button" id="newRequestButton">+ New Request</button>
        <?php endif; ?>
    </div>

    <?php if (! ($canCreateRequest ?? false)) : ?>
        <div class="alert alert-info">You can review requests, but this account needs an employee profile mapping to submit a new request.</div>
    <?php endif; ?>

    <ul class="nav nav-tabs mb-3" role="tablist">
        <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#gatepassTab" type="button">Gate Pass</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#accountabilityTab" type="button">Accountability</button></li>
    </ul>

    <div class="tab-content">
    <div class="tab-pane fade show active" id="gatepassTab">
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
    <div class="tab-pane fade" id="accountabilityTab">
    <div class="card-box"><div class="p-3">
        <table class="table align-middle" id="accountabilityTable">
            <thead><tr><th>Document No.</th><th>Accountable Personnel</th><th>Date Issued</th><th>Download</th><?php if ($canManageAccountability ?? false): ?><th>Edit</th><?php endif; ?></tr></thead>
        </table>
    </div></div>
    </div>
    </div>

</div>
</div>

<?php if ($canCreateRequest ?? false) : ?>
<div class="modal fade" id="createModal">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">

        <div class="modal-header">
            <h5 class="modal-title">Create Gate Pass</h5>
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
<?php endif; ?>

<?php if ($canCreateRequest ?? false) : ?>
<div class="modal fade" id="accountabilityModal" tabindex="-1">
  <div class="modal-dialog modal-xl modal-dialog-scrollable accountability-modal-dialog"><div class="modal-content">
    <div class="modal-header"><div><h5 class="modal-title">IT Accountability Form</h5><small class="text-muted">Record accountable employee details and IT equipment units</small></div><button class="btn-close" data-bs-dismiss="modal"></button></div>
    <form id="accountabilityForm"><div class="modal-body p-4 accountability-form-body">
      <?php $assignedAccountabilityTemplate = ($accountabilityTemplates ?? [])[0] ?? ['template_key' => 'itaf', 'template_name' => 'IT Accountability Form']; ?>
      <input type="hidden" name="accountability_template_key" value="<?= esc($assignedAccountabilityTemplate['template_key']) ?>">
      <div class="mb-3"><label class="form-label fw-semibold">Assigned document format</label><div class="form-control bg-light" aria-readonly="true"><?= esc($assignedAccountabilityTemplate['template_name']) ?></div><small class="text-muted">This format is assigned by an administrator in Settings → Document settings.</small></div>
      <h6 class="fw-bold mb-3">Employee / Accountable Personnel</h6>
      <div class="row g-3 mb-4">
        <input type="hidden" name="requestor_name" value="<?= esc(session()->get('logged_in')['full_name'] ?? '') ?>">
        <div class="col-md-3"><label class="form-label">Date</label><input class="form-control" type="date" name="form_date" value="<?= date('Y-m-d') ?>" required></div>
        <div class="col-md-3"><label class="form-label">Department</label><input class="form-control" name="department" required></div>
        <div class="col-md-4"><label class="form-label">Mobile</label><input class="form-control" name="mobile" type="tel"></div>
        <div class="col-md-4"><label class="form-label">Email</label><input class="form-control" name="email" type="email"></div>
        <div class="col-md-4"><label class="form-label">Address</label><input class="form-control" name="address"></div>
      </div>
      <div class="d-flex justify-content-between align-items-center mb-2"><h6 class="fw-bold mb-0">IT Equipment Units</h6><button class="btn btn-sm btn-outline-primary" type="button" id="addEquipmentRow">+ Add item</button></div>
      <div class="table-responsive"><table class="table table-bordered align-middle"><thead><tr><th>Item No.</th><th>Description</th><th>Model / Brand</th><th>Serial Number</th><th>Employee</th><th>Remarks</th><th>Date Issued</th><th></th></tr></thead><tbody id="equipmentRows"></tbody></table></div>
    </div><div class="modal-footer"><button class="btn btn-light" type="button" data-bs-dismiss="modal">Cancel</button><button class="btn btn-primary" type="submit">Submit for Approval</button></div></form>
  </div></div>
</div>
<?php endif; ?>

<?php if ($canManageAccountability ?? false): ?>
<div class="modal fade" id="editAccountabilityModal" tabindex="-1">
  <div class="modal-dialog modal-xl modal-dialog-scrollable accountability-modal-dialog"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title">Edit Accountability Document</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
    <form id="editAccountabilityForm"><input type="hidden" name="request_id"><div class="modal-body p-4">
      <div class="row g-3 mb-4">
        <div class="col-md-6"><label class="form-label">Accountable Personnel</label><input class="form-control" name="requestor_name" required></div>
        <div class="col-md-3"><label class="form-label">Department</label><input class="form-control" name="department" required></div>
        <div class="col-md-3"><label class="form-label">Date Issued</label><input class="form-control" type="date" name="form_date" required></div>
        <div class="col-md-4"><label class="form-label">Mobile</label><input class="form-control" name="mobile"></div>
        <div class="col-md-4"><label class="form-label">Email</label><input class="form-control" type="email" name="email"></div>
        <div class="col-md-4"><label class="form-label">Address</label><input class="form-control" name="address"></div>
        <div class="col-md-4"><label class="form-label">Signature over Printed Name</label><input class="form-control" name="signature_name"></div>
        <div class="col-md-4"><label class="form-label">Verified by (IT Specialist)</label><input class="form-control" name="verified_by"></div>
        <div class="col-md-4"><label class="form-label">Approved by (Supervisor / President)</label><input class="form-control" name="approved_by"></div>
      </div>
      <div class="d-flex justify-content-between align-items-center mb-2"><h6 class="fw-bold mb-0">Equipment Units</h6><button class="btn btn-sm btn-outline-primary" type="button" id="addAdminEquipmentRow">+ Add item</button></div>
      <div class="table-responsive"><table class="table table-bordered align-middle"><thead><tr><th>Item No.</th><th>Description</th><th>Model / Brand</th><th>Serial Number</th><th>Employee</th><th>Remarks</th><th>Date Issued</th><th></th></tr></thead><tbody id="adminEquipmentRows"></tbody></table></div>
    </div><div class="modal-footer"><button class="btn btn-light" type="button" data-bs-dismiss="modal">Cancel</button><button class="btn btn-primary" type="submit">Save Document</button></div></form>
  </div></div>
</div>
<?php endif; ?>



<?= $this->endSection() ?>
