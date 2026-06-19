
<body>

<div class="container-fluid p-0 vh-100">
    <div class="row g-0 h-100">

        <div class="col-12 login-side d-flex align-items-center justify-content-center">

            <div class="card login-card">

                <div class="card-body p-5">

                    <div class="text-center mb-4">

                        <div class="logo-circle mb-4">
                            <i class="bi bi-shield-lock-fill text-white fs-1"></i>
                        </div>

                        <h2 class="fw-bold">
                            Welcome Back
                        </h2>

                        <p class="text-muted">
                            Sign in to continue
                        </p>

                    </div>

                    <form id="formAuthentication">


                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Username
                            </label>

                            <input
                                    type="text"
                                    name="username"
                                    id="username"
                                    class="form-control"
                                    placeholder="Enter your username"
                                    required
                            >
                        </div>

                        <div class="mb-4">

                            <label class="form-label fw-semibold">
                                Password
                            </label>

                            <div class="input-group">

                                <input
                                    type="password"
                                    id="password"
                                    name="password"
                                    class="form-control"
                                    placeholder="Enter your password">

                                <span
                                    class="input-group-text bg-white password-toggle"
                                    onclick="togglePassword()">

                                    <i class="bi bi-eye" id="eyeIcon"></i>

                                </span>

                            </div>

                        </div>


                        <button type="submit" class="btn btn-primary btn-login login-btn w-100">
                            <i class="bi bi-box-arrow-in-right me-2"></i>
                            Sign In
                        </button>

                    </form>

                    <hr class="my-4">

                    <div class="text-center text-muted">
                        © 2026 Gate Pass Management System
                    </div>

                </div>

            </div>

        </div>

    </div>
</div>

<script>
function togglePassword() {

    const password =
        document.getElementById('password');

    const eye =
        document.getElementById('eyeIcon');

    if(password.type === 'password') {
        password.type = 'text';
        eye.classList.remove('bi-eye');
        eye.classList.add('bi-eye-slash');
    } else {
        password.type = 'password';
        eye.classList.remove('bi-eye-slash');
        eye.classList.add('bi-eye');
    }
}
</script>

</body>
</html>