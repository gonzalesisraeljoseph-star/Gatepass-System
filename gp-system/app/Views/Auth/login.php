<div class="login-wrapper">

    <div class="login-card">

        <div class="top-badge">
            <i class="fa-solid fa-shield-halved"></i>
        </div>

        <div class="login-header">
            <h2>Gatepass System</h2>

            <p>
                Secure access portal for KINECT Inc.
            </p>
        </div>

        <form id="formAuthentication">

            <div class="form-group">

                <label class="form-label">
                    Username
                </label>

                <div class="input-wrapper">

                    <i class="fa-regular fa-user left-icon"></i>

                    <input
                        type="text"
                        name="username"
                        id="username"
                        class="form-control-custom"
                        placeholder="Enter your username"
                        required
                    >

                </div>

            </div>

            <div class="form-group">

                <label class="form-label">
                    Password
                </label>

                <div class="input-wrapper">

                    <i class="fa-solid fa-lock left-icon"></i>

                    <input
                        type="password"
                        name="password"
                        id="password"
                        class="form-control-custom"
                        placeholder="Enter your password"
                        required
                    >

                    <button type="button" class="toggle-password">
                        <i class="fa-regular fa-eye-slash"></i>
                    </button>

                </div>

            </div>

            <button type="submit" class="login-btn">
                Login to System
            </button>

        </form>

        <div class="footer-text">
            © <?= date('Y'); ?> KINECT Inc.
        </div>

    </div>

</div>