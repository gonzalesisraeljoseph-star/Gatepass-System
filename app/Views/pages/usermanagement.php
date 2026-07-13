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
            <table class="table align-middle" id="usersTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Employee</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Action</th>
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

            <div class="mb-3" id="passwordGroup">
                <label class="form-label fw-semibold">Password</label>
                <input type="password" class="form-control" name="password" id="password">
                <small class="text-muted" id="passwordHint">Leave blank to keep current password</small>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Role</label>
                <select class="form-control" name="role" id="role" required>
                    <option value="">Select role...</option>
                    <option value="admin">Admin</option>
                    <option value="manager">IT</option>
                    <option value="employee">Employee</option>
                </select>
            </div>
            <div class="mb-3">
    <label class="form-label fw-semibold">Department</label>
    <select class="form-control" name="role" id="role" required>
        <option value="">Select Department...</option>
        <option value="accounting">Accounting</option>
        <option value="billing">Billing</option>
        <option value="collection">Collection</option>
        <option value="compliance">Compliance</option>
        <option value="cs_nonvoice">CS- Non Voice</option>
        <option value="cs_voice">CS- Voice</option>
        <option value="hr">HR</option>
        <option value="inbound_sales">Inbound Sales</option>
        <option value="it">IT</option>
        <option value="occupier">Occupier</option>
        <option value="operation">Operation</option>
        <option value="outbound_sales">Outbound Sales</option>
        <option value="pricing">Pricing</option>
        <option value="provisioning">Provisioning</option>
        <option value="retention">Retention</option>
        <option value="tqa">TQA</option>
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

<script>
let usersTable;

$(document).ready(function () {
    usersTable = $('#usersTable').DataTable({
        ajax: {
            url: "<?= base_url('usermanagement/list') ?>",
            dataSrc: 'data'
        },
        columns: [
            { data: 'id' },
            { data: 'employee' },
            { data: 'username' },
            { data: 'email' },
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
                    return `
                        <button class="btn btn-sm btn-light" onclick='editUser(${JSON.stringify(row)})'>Edit</button>
                        <button class="btn btn-sm btn-light text-danger" onclick="deleteUser(${row.id})">Delete</button>
                    `;
                }
            }
        ]
    });

    $('#userForm').on('submit', function (e) {
        e.preventDefault();
        saveUser();
    });
});

function openCreateModal() {
    document.getElementById('userModalTitle').innerText = 'Create User';
    document.getElementById('userForm').reset();
    document.getElementById('userId').value = '';
    document.getElementById('passwordHint').style.display = 'none';
}

function editUser(row) {
    document.getElementById('userModalTitle').innerText = 'Edit User';
    document.getElementById('userId').value = row.id ?? '';
    document.getElementById('employee').value = row.employee ?? '';
    document.getElementById('username').value = row.username ?? '';
    document.getElementById('email').value = row.email ?? '';
    document.getElementById('role').value = row.role ?? '';
    document.getElementById('status').value = row.status ?? 'active';
    document.getElementById('password').value = '';
    document.getElementById('passwordHint').style.display = 'block';

    const modal = new bootstrap.Modal(document.getElementById('createModal'));
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
            bootstrap.Modal.getInstance(document.getElementById('createModal')).hide();
            usersTable.ajax.reload();
        } else {
            alert(res.message ?? 'Something went wrong.');
        }
    })
    .catch(() => alert('Request failed.'));
}

function deleteUser(id) {
    if (!confirm('Are you sure you want to delete this user?')) return;

    fetch("<?= base_url('usermanagement/deleteUser') ?>/" + id, {
        method: 'POST'
    })
    .then(res => res.json())
    .then(res => {
        if (res.success) {
            usersTable.ajax.reload();
        } else {
            alert(res.message ?? 'Delete failed.');
        }
    });
}
</script>

<?= $this->endSection() ?>