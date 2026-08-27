<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>NACIT Student Portal - Login</title>
    <link rel="stylesheet" type="text/css" href="style.css?v=1.2">
</head>
<body>

<div class="login-container">

    <form class="login-card" action="backend.php" method="POST">

        <h2>NACIT Portal Login</h2>

        <?php if (!empty($_SESSION['error'])): ?>
            <div class="error-banner">
                <?= htmlspecialchars($_SESSION['error']) ?>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <?php if (!empty($_SESSION['success'])): ?>
            <div class="success-banner">
                <?= htmlspecialchars($_SESSION['success']) ?>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <input type="hidden" name="action" value="login">

        <div class="input-field">
            <label for="identity">Student ID or Email Address</label>
            <input
                type="text"
                id="identity"
                name="identity"
                required
                placeholder="NACIT/2026/001 or email@example.com"
                autocomplete="username">
        </div>

        <div class="input-field">

            <div class="password-label-row">
                <label for="login_password">Password</label>
                <a href="forgotpassword.php" class="forgot-link">
                    Forgot password?
                </a>
            </div>

            <div class="password-wrapper">

                <input
                    type="password"
                    id="login_password"
                    name="password"
                    required
                    placeholder="••••••••"
                    autocomplete="current-password">

                <button
                    type="button"
                    id="toggle_password_btn"
                    class="toggle-password"
                    aria-label="Show password">

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="1.5"
                        stroke="currentColor"
                        class="eye-icon">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />

                    </svg>

                </button>

            </div>

        </div>

        <button type="submit" class="submit-btn">
            Sign In
        </button>

        <div class="footer-links">
            <a href="register.php">
                New student? Register here
            </a>
        </div>

    </form>

</div>

<script>
document
    .getElementById('toggle_password_btn')
    .addEventListener('click', function () {

        const passwordInput =
            document.getElementById('login_password');

        const type =
            passwordInput.getAttribute('type') === 'password'
                ? 'text'
                : 'password';

        passwordInput.setAttribute('type', type);

        this.classList.toggle('active');

    });
</script>

</body>
</html>
