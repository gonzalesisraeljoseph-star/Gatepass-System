<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<style>
/* ===== BASE ===== */
body{
    background:#f6f8fc;
}

/* ===== HEADER ===== */
.page-header{
    background:#fff;
    color:#0f172a;
    padding:26px 30px;
    border-radius:22px;
    margin-bottom:22px;
    border:1px solid #e2e8f0;
    box-shadow:0 4px 20px rgba(15,23,42,.04);
}

.page-header h3{
    font-weight:700;
    margin-bottom:4px;
    color:#0f172a;
}

.page-header small{
    color:#64748b;
}

/* ===== CARD ===== */
.card-box{
    background:#fff;
    border-radius:22px;
    box-shadow:0 10px 30px rgba(0,0,0,.05);
    overflow:hidden;
}

/* ===== TABLE ===== */
.table thead th{
    font-size:12px;
    text-transform:uppercase;
    letter-spacing:.05em;
    color:#64748b;
    background:#f8fafc;
    border-bottom:none;
}

.table tbody tr{
    border-color:#f1f5f9;
}

.table tbody tr:hover{
    background:#f8fafc;
}
.form-control{
    border-radius:12px;
    border:1px solid #e2e8f0;
    height:44px;
}
.badge-status{
    font-size:11px;
    padding:5px 10px;
    border-radius:8px;
}

/* ===== INBOX-SPECIFIC ===== */
.reason-cell{
    max-width:320px;
    color:#334155;
}
.action-form{
    display:flex;
    gap:8px;
    align-items:center;
    flex-wrap:wrap;
}
.action-form input[type="text"]{
    border-radius:10px;
    border:1px solid #e2e8f0;
    height:36px;
    padding:0 10px;
    font-size:13px;
    flex:1;
    min-width:140px;
}
.btn-approve{
    background:#16a34a;
    color:#fff;
    border:none;
    border-radius:10px;
    padding:8px 14px;
    font-size:13px;
    font-weight:600;
    cursor:pointer;
}
.btn-reject{
    background:#fff;
    color:#dc2626;
    border:1px solid #fecaca;
    border-radius:10px;
    padding:8px 14px;
    font-size:13px;
    font-weight:600;
    cursor:pointer;
}
.request-row--gatepass td { background-color:#eff6ff; }
.request-row--accountability td { background-color:#f5f3ff; }
.request-row--other td { background-color:#f8fafc; }
.request-type-badge { display:inline-block; border-radius:999px; padding:5px 10px; font-size:12px; font-weight:700; white-space:nowrap; }
.request-type-badge--gatepass { background:#dbeafe; color:#1d4ed8; }
.request-type-badge--accountability { background:#ede9fe; color:#6d28d9; }
.request-type-badge--other { background:#e2e8f0; color:#334155; }
.decision-badge { display:inline-block; border-radius:999px; padding:5px 10px; font-size:12px; font-weight:700; }
.decision-badge--pending { background:#fef3c7; color:#92400e; }
.decision-badge--approved { background:#dcfce7; color:#166534; }
.decision-badge--rejected { background:#fee2e2; color:#991b1b; }
</style>

<div class="body-wrapper">
<div class="container-fluid">

    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h3>My Approvals</h3>
            <small>Review requests by approval status</small>
        </div>
    </div>

    <ul class="nav nav-tabs mb-3" role="tablist" aria-label="Approval views">
        <li class="nav-item"><a class="nav-link <?= ($activeFilter ?? 'pending') === 'pending' ? 'active' : '' ?>" href="<?= site_url('approvals?filter=pending') ?>" <?= ($activeFilter ?? 'pending') === 'pending' ? 'aria-current="page"' : '' ?>>Pending</a></li>
        <li class="nav-item"><a class="nav-link <?= ($activeFilter ?? '') === 'approved' ? 'active' : '' ?>" href="<?= site_url('approvals?filter=approved') ?>" <?= ($activeFilter ?? '') === 'approved' ? 'aria-current="page"' : '' ?>>Approve</a></li>
        <li class="nav-item"><a class="nav-link <?= ($activeFilter ?? '') === 'all' ? 'active' : '' ?>" href="<?= site_url('approvals?filter=all') ?>" <?= ($activeFilter ?? '') === 'all' ? 'aria-current="page"' : '' ?>>All</a></li>
    </ul>

    <?php if (session('error')): ?>
        <div class="alert alert-danger"><?= esc(session('error')) ?></div>
    <?php endif; ?>
    <?php if (session('message')): ?>
        <div class="alert alert-success"><?= esc(session('message')) ?></div>
    <?php endif; ?>

    <div class="card-box">
        <div class="p-3">
            <table class="table align-middle" id="approvalsTable">
                <thead class="table-light">
                    <tr>
                        <th>Request #</th>
                        <th>Requestor</th>
                        <th>Type</th>
                        <th>Reason</th>
                        <th>Decision</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($items)): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No requests match this view.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($items as $it): ?>
                            <?php
                                $requestType = $it['request']['request_type'] ?? 'gatepass';
                                $typeClass = $requestType === 'accountability' ? 'accountability' : ($requestType === 'gatepass' ? 'gatepass' : 'other');
                                $typeLabel = $requestType === 'accountability' ? 'Accountability' : ($requestType === 'gatepass' ? 'Gate Pass' : ucwords(str_replace('_', ' ', $requestType)));
                                $decision = $it['step']['status'] ?? 'pending';
                                $decisionClass = in_array($decision, ['approved', 'overridden'], true) ? 'approved' : ($decision === 'rejected' ? 'rejected' : 'pending');
                            ?>
                            <tr class="request-row--<?= esc($typeClass) ?>">
                                <td>#<?= esc($it['request']['id']) ?></td>
                                <td><?= esc($it['request']['requestor_name']) ?></td>
                                <td><span class="request-type-badge request-type-badge--<?= esc($typeClass) ?>"><?= esc($typeLabel) ?></span></td>
                                <td class="reason-cell"><?= esc($it['request']['reason']) ?></td>
                                <td><span class="decision-badge decision-badge--<?= esc($decisionClass) ?>"><?= esc(ucfirst($decisionClass)) ?></span></td>
                                <td>
                                    <?php if (($it['request']['request_type'] ?? '') === 'accountability'): ?>
                                        <details class="mb-2"><summary class="small text-primary" role="button">Review form details</summary>
                                            <div class="small mt-2"><strong>Document No.:</strong> <?= esc($it['request']['document_number'] ?? '') ?> · <strong>Date Issued:</strong> <?= esc($it['request']['form_date'] ?? '') ?> · <strong>Department:</strong> <?= esc($it['request']['department'] ?? '') ?> · <strong>Mobile:</strong> <?= esc($it['request']['mobile'] ?? '') ?><br><strong>Email:</strong> <?= esc($it['request']['email'] ?? '') ?><br><strong>Address:</strong> <?= esc($it['request']['address'] ?? '') ?><br><strong>Signature over printed name:</strong> <?= esc($it['request']['signature_name'] ?? '') ?><br><strong>Verified by:</strong> <?= esc($it['request']['verified_by'] ?? '') ?> · <strong>Approved by:</strong> <?= esc($it['request']['approved_by'] ?? '') ?></div>
                                            <div class="table-responsive mt-2"><table class="table table-sm table-bordered"><thead><tr><th>Item no.</th><th>Description</th><th>Model / Brand</th><th>Serial Number</th><th>Employee</th><th>Remarks</th><th>Date Issued</th></tr></thead><tbody>
                                            <?php foreach (($it['request']['equipment'] ?? []) as $itemIndex => $equipment): ?><tr><td><?= esc($itemIndex + 1) ?></td><td><?= esc($equipment['description']) ?></td><td><?= esc($equipment['model_brand']) ?></td><td><?= esc($equipment['serial_number']) ?></td><td><?= esc($equipment['employee']) ?></td><td><?= esc($equipment['remarks']) ?></td><td><?= esc($equipment['item_date']) ?></td></tr><?php endforeach; ?>
                                            </tbody></table></div>
                                        </details>
                                    <?php endif; ?>
                                    <?php if ($decision === 'pending'): ?>
                                        <form method="post" action="<?= site_url('gatepass/approvals/act') ?>" class="action-form">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="request_id" value="<?= esc($it['request']['id']) ?>">
                                            <input type="text" name="remarks" placeholder="remarks (optional)">
                                            <button type="submit" name="decision" value="approved" class="btn-approve">Approve</button>
                                            <button type="submit" name="decision" value="rejected" class="btn-reject">Reject</button>
                                        </form>
                                    <?php else: ?>
                                        <span class="text-muted small"><?= $decision === 'overridden' ? 'Overridden' : 'Action recorded' ?> <?= esc($it['step']['acted_at'] ?? '') ?></span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>
</div>

<?= $this->endSection() ?>
