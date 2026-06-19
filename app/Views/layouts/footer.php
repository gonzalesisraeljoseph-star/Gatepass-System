<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script src="<?= base_url('assets/js/vendor.min.js') ?>"></script>
<script src="<?= base_url('assets/js/bootstrap.bundle.min.js') ?>"></script>

<script src="<?= base_url('assets/js/sweetalert2.all.min.js') ?>"></script>


<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script src="<?= base_url('assets/js/app.init.js') ?>"></script>
<script src="<?= base_url('assets/js/theme.js') ?>"></script>
<script src="<?= base_url('assets/js/app.min.js') ?>"></script>

<script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>

<script>
let table;

// ** Accessories Request **//


$(document).on('hidden.bs.modal', '.modal', function () {
    $('.modal-backdrop').remove();
    $('body').removeClass('modal-open');
});


$(function () {

    table = $('#gatepassTable').DataTable({
        ajax: "<?= base_url('gatepass/list') ?>",
        columns: [
            { data: 'id' },
            { data: 'requestor_name' },
            { data: 'reason' },
            {
                data: 'status',
                render: renderGatepassStatus
            },
            { data: 'created_at' }
        ]
    });

    loadDevices();

    $('#gatepassForm').on('submit', function (e) {
        e.preventDefault();

        $.ajax({
            url: "<?= base_url('gatepass/store') ?>",
            type: "POST",
            data: $(this).serialize(),
            dataType: "json",

            success: function (res) {

                if (res.status) {

                    const modalEl = document.getElementById('createModal');
                    const modal = bootstrap.Modal.getOrCreateInstance(modalEl);

                    // listen once for proper cleanup
                    modalEl.addEventListener('hidden.bs.modal', function handler() {

                        $('#gatepassForm')[0].reset();
                        $('#deviceSelect').val(null).trigger('change');

                        table.ajax.reload(null, false);

                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: res.message || 'Request submitted successfully',
                            timer: 1500,
                            showConfirmButton: false
                        });

                        // remove listener para di ma-double trigger
                        modalEl.removeEventListener('hidden.bs.modal', handler);
                    });

                    modal.hide();

                } else {
                    Swal.fire('Error', res.message || 'Unable to save request', 'error');
                }
            },

            error: function () {
                Swal.fire('Server Error', 'Check console for details', 'error');
            }
        });
    });

});

// ** Gatepass Request **//

function renderGatepassStatus(status) {

    const map = {
        'Deployed': 'success',
        'Ready to Deploy': 'info',
        'Pending': 'warning',
        'Inactive': 'secondary',
        'Repair': 'danger'
    };

    const color = map[status] || 'secondary';

    return `<span class="badge bg-${color}">${status ?? 'Unknown'}</span>`;
}

function loadDevices() {
    $.get("<?= base_url('api/hardware') ?>", function (res) {

        let options = '';

        if (res.rows) {
            res.rows.forEach(item => {
                options += `
                    <option value="${item.id}">
                        ${item.name} (${item.asset_tag ?? ''})
                    </option>
                `;
            });
        }

        $('#deviceSelect').html(options);
        initSelect2();
    });
}

function initSelect2() {
    $('#deviceSelect').select2({
        placeholder: "Search and select devices",
        width: '100%',
        dropdownParent: $('#createModal')
    });
}
</script>



<script>
let hardwareTable;

$(function () {
    initTable();
    bindEvents();
});

function initTable() {

    hardwareTable = $('#hardwareTable').DataTable({
        ajax: {
            url: "<?= base_url('api/hardware') ?>",
            dataSrc: function (json) {
                updateSummary(json);
                return json.rows ?? [];
            }
        },

        pageLength: 10,
        responsive: true,
        order: [[1, 'asc']],

        columns: [
            { data: 'asset_tag', defaultContent: '-' },
            { data: 'name', defaultContent: '-' },

            {
                data: row => row.category?.name ?? '-'
            },

            {
                data: row => row.model?.name ?? '-'
            },

            { data: 'serial', defaultContent: '-' },

            {
                data: row => renderStatus(row.status_label?.name)
            },

            {
                data: row => row.assigned_to?.name ?? 'Unassigned'
            }
        ]
    });
}

function renderStatus(status) {

    const map = {
        'Deployed': 'success',
        'Ready to Deploy': 'warning',
        'Pending': 'info'
    };

    const badge = map[status] ?? 'secondary';

    return `
        <span class="badge bg-${badge}">
            ${status ?? 'Unknown'}
        </span>
    `;
}

function bindEvents() {

    $('#customSearch').on('keyup', function () {
        hardwareTable.search(this.value).draw();
    });

    $('#refreshTable').on('click', function () {
        hardwareTable.ajax.reload();
    });

    $(document).on('click', '.viewAsset', function () {
        const id = $(this).data('id');
        alert(`Selected Asset ID: ${id}`);
    });
}

function updateSummary(response) {

    let assigned = 0;
    let deployed = 0;

    (response.rows ?? []).forEach(item => {

        if (item.assigned_to) assigned++;

        if (item.status_label?.name === 'Deployed') {
            deployed++;
        }

    });

    $('#assignedAssets').text(assigned);
    $('#deployAssets').text(deployed);
}
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const trigger = document.querySelector('[href="#setupMenu"]');
    const menu = document.querySelector('#setupMenu');

    trigger.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const isOpen = this.getAttribute('aria-expanded') === 'true';
        
        if (isOpen) {
            menu.classList.remove('show');
            this.setAttribute('aria-expanded', 'false');
        } else {
            menu.classList.add('show');
            this.setAttribute('aria-expanded', 'true');
        }
    });
});
</script>

</body>

</html>

