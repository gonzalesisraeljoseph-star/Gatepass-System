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

/* ===== FLOATING-SPECIFIC ===== */
.reason-cell{
    max-width:280px;
    color:#334155;
}
.stuck-note{
    font-size:12px;
    color:#b45309;
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
    background:#7c3aed;
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
    color:#7c3aed;
    border:1px solid #ddd6fe;
    border-radius:10px;
    padding:8px 14px;
    font-size:13px;
    font-weight:600;
    cursor:pointer;
}
</style>

<div class="body-wrapper">
<div class="container-fluid">

    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h3>Floating Requests</h3>
            <small>Requests with no available approver &mdash; need escalation or override</small>
        </div>
    </div>

    <?php if (!$canOverride): ?>
        <div class="alert alert-warning">Your role cannot override requests, so the actions below are hidden.</div>
    <?php endif; ?>
    <?php if (session('error')): ?>
        <div class="alert alert-danger"><?= esc(session('error')) ?></div>
    <?php endif; ?>
    <?php if (session('message')): ?>
        <div class="alert alert-success"><?= esc(session('message')) ?></div>
    <?php endif; ?>

    <div class="card-box">
        <div class="p-3">
            <table class="table align-middle" id="floatingTable">
                <thead class="table-light">
                    <tr>
                        <th>Request #</th>
                        <th>Requestor</th>
                        <th>Reason</th>
                        <th>Status</th>
                        <?php if ($canOverride): ?><th>Override</th><?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($items)): ?>
                        <tr>
                            <td colspan="<?= $canOverride ? 5 : 4 ?>" class="text-center text-muted py-4">No floating requests right now.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($items as $it): ?>
                            <tr>
                                <td>#<?= esc($it['request']['id']) ?></td>
                                <td><?= esc($it['request']['requestor_name']) ?></td>
                                <td class="reason-cell"><?= esc($it['request']['reason']) ?></td>
                                <td>
                                    <span class="badge badge-status bg-warning text-dark">Floating</span>
                                    <div class="stuck-note">stuck at node <?= esc($it['step']['node_id']) ?> - no available approver resolved</div>
                                </td>
                                <?php if ($canOverride): ?>
                                <td>
                                    <form method="post" action="<?= site_url('gatepass/approvals/override') ?>" class="action-form">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="request_id" value="<?= esc($it['request']['id']) ?>">
                                        <input type="text" name="remarks" placeholder="override reason">
                                        <button type="submit" name="decision" value="approved" class="btn-approve">Force Approve</button>
                                        <button type="submit" name="decision" value="rejected" class="btn-reject">Force Reject</button>
                                    </form>
                                </td>
                                <?php endif; ?>
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
