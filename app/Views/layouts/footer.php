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