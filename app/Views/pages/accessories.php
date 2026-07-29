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
</style>


<div class="body-wrapper">
<div class="container-fluid">

    <div class="page-header d-flex justify-content-between align-items-center">

       <div>
                <h3>Accessories Inventory</h3>
                <small>
                    Company assigned accessories and hardware assets
                </small>
            </div>


    </div>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= esc($error) ?></div>
    <?php endif; ?>

    <div class="card-box">

        <div class="p-3">

           <table class="table align-middle" id="hardwareTable">
                        <thead class="table-light">
                            <tr>
                                <th>Asset Tag</th>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Model</th>
                                <th>Serial</th>
                                <th>Status</th>
                                <th>Assigned To</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($hardware)): ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">No accessories assigned.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($hardware as $item): ?>
                                    <tr>
                                        <td><?= esc($item['asset_tag']) ?></td>
                                        <td><?= esc($item['name']) ?></td>
                                        <td><?= esc($item['category']) ?></td>
                                        <td><?= esc($item['model']) ?></td>
                                        <td><?= esc($item['serial']) ?></td>
                                        <td>
                                            <span class="badge badge-status bg-<?= $item['status_meta'] === 'deployed' ? 'success' : 'secondary' ?>">
                                                <?= esc($item['status']) ?>
                                            </span>
                                        </td>
                                        <td><?= esc($item['assigned_to']) ?></td>
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