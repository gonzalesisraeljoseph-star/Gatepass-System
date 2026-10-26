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

    $('#newRequestButton').on('click', function () {
        const modalSelector = $('#accountabilityTab').hasClass('active') ? '#accountabilityModal' : '#createModal';
        const modalElement = document.querySelector(modalSelector);
        if (modalElement) bootstrap.Modal.getOrCreateInstance(modalElement).show();
    });

    if ($('#gatepassTable').length) {
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
    }

    if ($('#accountabilityTable').length) {
        $('#accountabilityTable').DataTable({
            ajax: "<?= base_url('accountability/list') ?>",
            autoWidth: false,
            columns: [
                { data: 'document_number', defaultContent: '' },
                { data: 'requestor_name' },
                { data: 'form_date', defaultContent: '' },
                { data: null, orderable: false, searchable: false, render: function (data, type, row) {
                    return `<a class="btn btn-sm btn-outline-primary" href="<?= base_url('accountability') ?>/${row.id}/pdf">Download PDF</a>`;
                } }
                <?php if ($canManageAccountability ?? false): ?>,
                { data: null, orderable: false, searchable: false, render: (data, type, row) => `<button type="button" class="btn btn-sm btn-outline-secondary edit-accountability" data-id="${row.id}">Edit</button>` }
                <?php endif; ?>
            ]
        });
        document.querySelector('[data-bs-target="#accountabilityTab"]')?.addEventListener('shown.bs.tab', function () {
            $('#accountabilityTable').DataTable().columns.adjust().draw(false);
        });
    }

    // The hardware endpoint requires an employee mapping. Only request
    // hardware on the gatepass form page when its selector is present.
    if ($('#deviceSelect').length) {
        loadDevices();
    }

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

            error: function (xhr) {
                Swal.fire('Unable to submit', xhr.responseJSON?.message || 'The gate pass request could not be saved. Please try again.', 'error');
            }
        });
    });

    let equipmentIndex = 0;
    function addEquipmentRow() {
        const i = equipmentIndex++;
        const employee = $('#accountabilityForm [name="requestor_name"]').val() || '';
        $('#equipmentRows').append(`<tr>
            <td class="equipment-number"></td>
            <td><input class="form-control" name="items[${i}][description]" required></td>
            <td><input class="form-control" name="items[${i}][model_brand]"></td>
            <td><input class="form-control" name="items[${i}][serial_number]"></td>
            <td><input class="form-control" name="items[${i}][employee]" value="${$('<div>').text(employee).html()}" required></td>
            <td><input class="form-control" name="items[${i}][remarks]"></td>
            <td><input class="form-control" type="date" name="items[${i}][item_date]" value="<?= date('Y-m-d') ?>"></td>
            <td><button class="btn btn-sm btn-outline-danger remove-equipment" type="button" aria-label="Remove item">×</button></td>
        </tr>`);
        $('#equipmentRows tr').each(function (index) { $(this).find('.equipment-number').text(index + 1); });
        const body = document.querySelector('#accountabilityForm .accountability-form-body');
        const lastRow = document.querySelector('#equipmentRows tr:last-child');
        if (body && lastRow) lastRow.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
    }
    if ($('#equipmentRows').length) addEquipmentRow();
    $('#addEquipmentRow').on('click', addEquipmentRow);
    $('#equipmentRows').on('click', '.remove-equipment', function () {
        $(this).closest('tr').remove();
        $('#equipmentRows tr').each(function (index) { $(this).find('.equipment-number').text(index + 1); });
    });

    $('#accountabilityForm').on('submit', function (e) {
        e.preventDefault();
        const form = this;
        const body = new FormData(form);
        $.ajax({
            url: "<?= base_url('accountability/store') ?>",
            type: 'POST', data: body, dataType: 'json', processData: false, contentType: false,
            success: function (res) {
                if (!res.status) return Swal.fire('Error', res.message || 'Unable to save request', 'error');
                const modalEl = document.getElementById('accountabilityModal');
                modalEl.addEventListener('hidden.bs.modal', function handler() {
                    $('#accountabilityForm')[0].reset();
                    $('#equipmentRows').empty(); equipmentIndex = 0; addEquipmentRow();
                    $('#accountabilityTable').DataTable().ajax.reload(null, false);
                    Swal.fire({icon:'success', title:'Submitted', text:res.message, timer:1800, showConfirmButton:false});
                    modalEl.removeEventListener('hidden.bs.modal', handler);
                });
                bootstrap.Modal.getOrCreateInstance(modalEl).hide();
            },
            error: function (xhr) {
                const message = xhr.responseJSON?.message || 'Unable to submit the accountability form.';
                Swal.fire('Error', message, 'error');
            }
        });
    });

    function addAdminEquipmentRow(item = {}) {
        const i = $('#adminEquipmentRows tr').length;
        const esc = value => $('<div>').text(value || '').html();
        $('#adminEquipmentRows').append(`<tr>
            <td class="admin-equipment-number"></td>
            <td><input class="form-control" name="items[${i}][description]" value="${esc(item.description)}" required></td>
            <td><input class="form-control" name="items[${i}][model_brand]" value="${esc(item.model_brand)}"></td>
            <td><input class="form-control" name="items[${i}][serial_number]" value="${esc(item.serial_number)}"></td>
            <td><input class="form-control" name="items[${i}][employee]" value="${esc(item.employee)}" required></td>
            <td><input class="form-control" name="items[${i}][remarks]" value="${esc(item.remarks)}"></td>
            <td><input class="form-control" type="date" name="items[${i}][item_date]" value="${esc(item.item_date)}"></td>
            <td><button class="btn btn-sm btn-outline-danger remove-admin-equipment" type="button">×</button></td>
        </tr>`);
        $('#adminEquipmentRows tr').each((index, row) => $(row).find('.admin-equipment-number').text(index + 1));
    }
    $('#addAdminEquipmentRow').on('click', () => addAdminEquipmentRow());
    $('#adminEquipmentRows').on('click', '.remove-admin-equipment', function () {
        $(this).closest('tr').remove();
        $('#adminEquipmentRows tr').each((index, row) => $(row).find('.admin-equipment-number').text(index + 1));
    });
    $('#accountabilityTable').on('click', '.edit-accountability', function () {
        const requestId = $(this).data('id');
        $.getJSON(`<?= base_url('accountability/admin') ?>/${requestId}`, function (res) {
            if (!res.status) return Swal.fire('Unable to load document', res.message, 'error');
            const form = $('#editAccountabilityForm')[0];
            form.reset();
            $('#adminEquipmentRows').empty();
            form.elements.request_id.value = requestId;
            ['requestor_name','department','form_date','mobile','email','address','signature_name','verified_by','approved_by'].forEach(field => {
                form.elements[field].value = res.request[field] || '';
            });
            (res.request.items || []).forEach(item => addAdminEquipmentRow(item));
            if (!(res.request.items || []).length) addAdminEquipmentRow();
            bootstrap.Modal.getOrCreateInstance(document.getElementById('editAccountabilityModal')).show();
        }).fail(xhr => Swal.fire('Unable to load document', xhr.responseJSON?.message || 'Please try again.', 'error'));
    });
    $('#editAccountabilityForm').on('submit', function (e) {
        e.preventDefault();
        const requestId = this.elements.request_id.value;
        $.post(`<?= base_url('accountability/admin') ?>/${requestId}`, $(this).serialize(), function (res) {
            if (!res.status) return Swal.fire('Unable to save', res.message || 'Please try again.', 'error');
            bootstrap.Modal.getOrCreateInstance(document.getElementById('editAccountabilityModal')).hide();
            $('#accountabilityTable').DataTable().ajax.reload(null, false);
            Swal.fire({icon:'success', title:'Saved', text:res.message, timer:1600, showConfirmButton:false});
        }, 'json').fail(xhr => Swal.fire('Unable to save', xhr.responseJSON?.message || 'Please try again.', 'error'));
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

        if (res.message && (!res.rows || res.rows.length === 0)) {
            console.warn('loadDevices:', res.message);
        }


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
// Generalized dropdown-toggle for the sidebar: works for however many
// modules have sub_modules (the DB decides that, not a fixed id like
// #setupMenu), instead of hardcoding one module's collapse id.
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.sidebar-menu [data-bs-toggle="dropdown"]').forEach(function (trigger) {

        const targetSelector = trigger.getAttribute('href');
        const menu = targetSelector ? document.querySelector(targetSelector) : null;
        if (!menu) return;

        trigger.addEventListener('click', function (e) {
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
});
</script>

</div> <!-- /.page-wrapper (opened in sidebar.php) -->
</div> <!-- /#main-wrapper (opened in header.php) -->

</body>

</html>
