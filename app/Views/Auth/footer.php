
<script src="<?= base_url(); ?>assets/js/vendor.min.js"></script>
<script src="<?= base_url(); ?>assets/js/app.min.js"></script>
<script src="<?= base_url(); ?>assets/js/sweetalert2.all.min.js"></script>
<script src="<?= base_url(); ?>assets/js/auth.js"></script>

<script>

document.addEventListener('DOMContentLoaded', function () {

    const togglePassword = document.querySelector('.toggle-password');
    const passwordField = document.querySelector('#password');
    const icon = togglePassword.querySelector('i');

    togglePassword.addEventListener('click', function () {

        const type = passwordField.getAttribute('type') === 'password'
            ? 'text'
            : 'password';

        passwordField.setAttribute('type', type);

        if(type === 'password'){
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        }else{
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }

    });

});

</script>

</body>
</html>