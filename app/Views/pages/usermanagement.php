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

.badge-active { background: #22c55e !important; }
.badge-inactive { background: #ef4444 !important; }
</style>

<div class="body-wrapper">
<div class="container-fluid">

    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h3>User Management</h3>
            <small>Manage all users, roles, and their access</small>
        </div>

        <button class="btn btn-light shadow-sm"
                data-bs-toggle="modal"
                data-bs-target="#createModal"
                onclick="openCreateModal()">
            + New User
        </button>
    </div>

    <div class="card-box">
        <div class="p-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div>
                    Show
                    <select id="rowsPerPage" class="form-select form-select-sm d-inline-block" style="width: auto;">
                        <option value="5">5</option>
                        <option value="10" selected>10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                        <option value="all">All</option>
                    </select>
                    entries
                </div>
                <div id="tableInfo" class="text-muted small"></div>
            </div>

            <table class="table align-middle" id="usersTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Employee</th>
                        <th>Department</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                           <?php if (! empty($users)) : ?>
                        <?php foreach ($users as $user) : ?>
                            <tr>
                                <td><?= esc($user['id']) ?></td>
                                <td><?= esc($user['username']) ?></td>
                                <td><?= esc($user['department']) ?></td>
                                <td><?= esc($user['role']) ?></td>
                                <td>
                                    <?php $status = $user['status'] ?? 'active'; ?>
                                    <?php if ($status === 'active') : ?>
                                        <span class="badge-soft badge-active">Active</span>
                                    <?php else : ?>
                                        <span class="badge-soft badge-inactive">Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td><button type="button" class="btn btn-sm btn-light" onclick='openEditModal(<?= json_encode($user, JSON_HEX_APOS | JSON_HEX_QUOT) ?>)'>Edit</button></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="5" style="text-align: center;">No users found.</td>
                        </tr>
                    <?php endif; ?>

                </tbody>
            </table>
        </div>
    </div>

</div>
</div>
<!--Modal-->
<div class="modal fade" id="createModal">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">

        <div class="modal-header">
            <h5 class="modal-title" id="userModalTitle">Create User</h5>
            <button class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <form id="userForm">
        <div class="modal-body p-4">

            <input type="hidden" name="id" id="userId">

            <div class="mb-3">
                <label class="form-label fw-semibold">Employee Name</label>
                <input class="form-control" name="employee" id="employee" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Username</label>
                <input class="form-control" name="username" id="username" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Email</label>
                <input type="email" class="form-control" name="email" id="email">
            </div>
            <!--Password-->
            <div class="mb-3" id="passwordGroup">
                <label class="form-label fw-semibold">Password</label>
                <input type="password" class="form-control" name="password" id="password">
                <small class="text-muted" id="passwordHint">Leave blank to keep current password</small>
            </div>
            <!--ROLE-->
            <div class="mb-3">
                <label class="form-label fw-semibold">Role</label>
                <select class="form-control" name="role" id="role" required>
                    <option value="">Select role...</option>
                    <?php if (! empty($roles)) : ?>
                        <?php foreach ($roles as $role) : ?>
                            <option value="<?= esc($role['name'] ?? '') ?>">
                                <?= esc($role['name'] ?? '') ?>
                            </option>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <option value="admin">Admin</option>
                        <option value="manager">IT</option>
                        <option value="employee">Employee</option>
                        <option value="checker">Checker</option>
                        <option value="teamlead">Teamlead</option>
                        <option value="Supervisor">Supervisor</option>
                    <?php endif; ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Department</label>
                <select class="form-control" name="department" id="department" required>
                    <option value="">Select Department...</option>
                    <?php if (! empty($departments)) : ?>
                        <?php foreach ($departments as $department) : ?>
                            <option value="<?= esc($department['name'] ?? $department['department_name'] ?? '') ?>">
                                <?= esc($department['name'] ?? $department['department_name'] ?? '') ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Status</label>
                <select class="form-control" name="status" id="status">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>

        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Save User</button>
        </div>
        </form>

    </div>
  </div>
</div>

<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Delete User</h5>
        <button class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p>Are you sure you want to remove <strong id="deleteUserName"></strong>?</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
      </div>
    </div>
  </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
let usersTable;
let selectedUserId = null;

function updateTableInfo() {
    if (!usersTable) {
        return;
    }

    const info = usersTable.page.info();
    const rowsSelect = document.getElementById('rowsPerPage');
    const tableInfo = document.getElementById('tableInfo');

    if (!rowsSelect || !tableInfo) {
        return;
    }

    const total = info.recordsTotal ?? 0;
    const start = total === 0 ? 0 : info.start + 1;
    const end = info.end || 0;

    tableInfo.textContent = total === 0
        ? 'No entries'
        : `Showing ${start} to ${end} of ${total} entries`;

    rowsSelect.value = info.length === -1 ? 'all' : String(info.length);
}

$(document).ready(function () {
    const rowsSelect = document.getElementById('rowsPerPage');

    // Prevent "Cannot reinitialise DataTable" errors if a global script
    // (e.g. in layouts/main) also auto-inits tables with class "table".
    if ($.fn.dataTable.isDataTable('#usersTable')) {
        $('#usersTable').DataTable().destroy();
    }

    usersTable = $('#usersTable').DataTable({
        destroy: true,
        ajax: {
            url: "<?= base_url('user-management/list') ?>",
            dataSrc: 'data'
        },
        paging: true,
        pageLength: 10,
        lengthChange: false,
        info: false,
        columns: [
            { data: 'id' },
            { data: 'employee' },
            { data: 'department' },
            { data: 'role' },
            {
                data: 'status',
                render: function (status) {
                    const cls = status === 'active' ? 'badge-active' : 'badge-inactive';
                    return `<span class="badge-soft ${cls}">${status ?? ''}</span>`;
                }
            },
            {
                data: null,
                orderable: false,
                render: function (row) {
                    const name = (row.employee ?? row.username ?? 'this user').replace(/'/g, "\\'");
                    return `
                        <button class="btn btn-sm btn-light" onclick='openEditModal(${JSON.stringify(row)})'>Edit</button>
                        <button class="btn btn-sm btn-light text-danger" onclick="openDeleteModal(${row.id}, '${name}')">Delete</button>
                    `;
                }
            }
        ]
    });

    if (rowsSelect) {
        rowsSelect.addEventListener('change', function () {
            const value = this.value;
            usersTable.page.len(value === 'all' ? -1 : Number(value)).draw();
        });
    }

    usersTable.on('draw', updateTableInfo);
    updateTableInfo();

    $('#userForm').on('submit', function (e) {
        e.preventDefault();
        saveUser();
    });

    $('#confirmDeleteBtn').on('click', function () {
        if (selectedUserId) {
            deleteUser(selectedUserId);
        }
    });
});

function openCreateModal() {
    document.getElementById('userModalTitle').innerText = 'Create User';
    document.getElementById('userForm').reset();
    document.getElementById('userId').value = '';
    document.getElementById('passwordHint').style.display = 'none';
    document.getElementById('passwordGroup').style.display = 'block';
}

function openEditModal(row) {
    document.getElementById('userModalTitle').innerText = 'Edit User';
    document.getElementById('userId').value = row.id ?? '';
    document.getElementById('employee').value = row.employee ?? '';
    document.getElementById('username').value = row.username ?? '';
    document.getElementById('email').value = row.email ?? '';
    document.getElementById('department').value = row.department ?? '';
    document.getElementById('role').value = row.role ?? '';
    document.getElementById('status').value = row.status ?? 'active';
    document.getElementById('password').value = '';
    document.getElementById('passwordHint').style.display = 'block';
    document.getElementById('passwordGroup').style.display = 'block';

    const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('createModal'));
    modal.show();
}

function saveUser() {
    const form = document.getElementById('userForm');
    const formData = new FormData(form);

    fetch("<?= base_url('usermanagement/saveUser') ?>", {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(res => {
        if (res.success) {
            bootstrap.Modal.getOrCreateInstance(document.getElementById('createModal')).hide();
            usersTable.ajax.reload();
        } else {
            alert(res.message ?? 'Something went wrong.');
        }
    })
    .catch(() => alert('Request failed.'));
}

function openDeleteModal(id, name) {
    selectedUserId = id;
    document.getElementById('deleteUserName').textContent = name;
    const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('deleteModal'));
    modal.show();
}

function deleteUser(id) {
    fetch("<?= base_url('usermanagement/deleteUser') ?>/" + id, {
        method: 'POST'
    })
    .then(res => res.json())
    .then(res => {
        if (res.success) {
            bootstrap.Modal.getOrCreateInstance(document.getElementById('deleteModal')).hide();
            usersTable.ajax.reload();
        } else {
            alert(res.message ?? 'Delete failed.');
        }
    })
    .catch(() => alert('Delete request failed.'));
}
</script>
<?= $this->endSection() ?>