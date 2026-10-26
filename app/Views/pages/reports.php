<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="body-wrapper">
  <div class="container-fluid">
    <div class="page-header d-flex justify-content-between align-items-center bg-white rounded-4 p-4 mb-4">
      <div>
        <h3 class="mb-1">Gate Pass Reports</h3>
        <small class="text-muted">Request totals and status history<?= $isAdmin ? ' across all users' : '' ?>.</small>
      </div>
      <span class="badge bg-primary-subtle text-primary fs-6"><?= count($requests) ?> requests</span>
    </div>

    <div class="row g-3 mb-4">
      <div class="col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4"><div class="card-body">
          <div class="text-muted">Total requests</div><div class="fs-3 fw-bold"><?= count($requests) ?></div>
        </div></div>
      </div>
      <?php foreach ($statusCounts as $status => $count) : ?>
        <div class="col-sm-6 col-lg-3">
          <div class="card border-0 shadow-sm rounded-4"><div class="card-body">
            <div class="text-muted"><?= esc($status) ?></div><div class="fs-3 fw-bold"><?= (int) $count ?></div>
          </div></div>
        </div>
      <?php endforeach; ?>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
      <div class="card-body table-responsive">
        <table class="table align-middle">
          <thead><tr><th>ID</th><th>Requestor</th><th>Reason</th><th>Status</th><th>Date</th></tr></thead>
          <tbody>
            <?php if (!$requests) : ?>
              <tr><td colspan="5" class="text-center text-muted py-4">No requests found.</td></tr>
            <?php else : ?>
              <?php foreach ($requests as $request) : ?>
                <tr>
                  <td><?= esc($request['id']) ?></td>
                  <td><?= esc($request['requestor_name'] ?? '') ?></td>
                  <td><?= esc($request['reason'] ?? '') ?></td>
                  <td><?= esc($request['status'] ?? 'Unknown') ?></td>
                  <td><?= esc($request['created_at'] ?? '') ?></td>
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
