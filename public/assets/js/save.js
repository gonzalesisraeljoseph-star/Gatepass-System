/* ==========================================================================
   User Management - table, create/edit modal, delete modal, confirm-save modal
   Expects a global `USER_MGMT_CONFIG` object (set inline in the view) with:
     { listUrl, saveUrl, deleteUrl }
   ========================================================================== */

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

function escapeHtml(str) {
    const div = document.createElement('div');
    div.textContent = str ?? '';
    return div.innerHTML;
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
            url: USER_MGMT_CONFIG.listUrl,
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
                    return `<span class="badge-soft ${cls}">${escapeHtml(status)}</span>`;
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

    // Instead of saving immediately, validate then show the confirm-save modal.
    $('#userForm').on('submit', function (e) {
        e.preventDefault();

        if (!this.checkValidity()) {
            this.reportValidity();
            return;
        }

        showConfirmSaveModal();
    });

    $('#confirmSaveBtn').on('click', function () {
        bootstrap.Modal.getOrCreateInstance(document.getElementById('confirmSaveModal')).hide();
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

/**
 * Validates the form values and, if everything looks right, builds a
 * human-readable summary and opens the confirm-save modal.
 */
function showConfirmSaveModal() {
    const isEdit = !!document.getElementById('userId').value;

    const employee   = document.getElementById('employee').value.trim();
    const username    = document.getElementById('username').value.trim();
    const email       = document.getElementById('email').value.trim();
    const role        = document.getElementById('role').value;
    const department  = document.getElementById('department').value;
    const status      = document.getElementById('status').value;
    const password    = document.getElementById('password').value;

    if (!role) {
        alert('Please select a role.');
        return;
    }
    if (!department) {
        alert('Please select a department.');
        return;
    }
    if (!isEdit && !password) {
        alert('Password is required for new users.');
        return;
    }

    const rows = [
        ['Employee', employee],
        ['Username', username],
        ['Email', email || '(none)'],
        ['Role', role],
        ['Department', department],
        ['Status', status],
        ['Password', password ? 'Will be updated' : 'Unchanged']
    ];

    const summaryHtml = rows.map(([label, value]) =>
        `<li class="mb-1"><strong>${escapeHtml(label)}:</strong> ${escapeHtml(value)}</li>`
    ).join('');

    document.getElementById('confirmSaveSummary').innerHTML = summaryHtml;

    const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('confirmSaveModal'));
    modal.show();
}

function saveUser() {
    const form = document.getElementById('userForm');
    const formData = new FormData(form);

    fetch(USER_MGMT_CONFIG.saveUrl, {
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
    fetch(USER_MGMT_CONFIG.deleteUrl + '/' + id, {
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