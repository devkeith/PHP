<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>NACIT Student Portal - Create Account</title>
    <link rel="stylesheet" type="text/css" href="style.css?v=1.2">
</head>
<body>

<div class="login-container">

    <form
        class="login-card"
        action="backend.php"
        method="POST"
        id="register_form">

        <h2>Create Student Account</h2>

        <?php if (!empty($_SESSION['error'])): ?>

            <div class="error-banner">
                <?= htmlspecialchars($_SESSION['error']) ?>
            </div>

            <?php unset($_SESSION['error']); ?>

        <?php endif; ?>

        <input type="hidden" name="action" value="register">

        <div class="input-field">

            <label for="reg_fullname">
                Full Name
            </label>

            <input
                type="text"
                id="reg_fullname"
                name="fullname"
                required
                placeholder="John Doe"
                autocomplete="name">

        </div>


        <div class="input-field">

            <label for="reg_email">
                Email Address
            </label>

            <input
                type="email"
                id="reg_email"
                name="email"
                required
                placeholder="johndoe@example.com"
                autocomplete="email">

        </div>


        <div class="input-field">

            <label for="reg_password">
                Password
            </label>

            <input
                type="password"
                id="reg_password"
                name="password"
                required
                minlength="8"
                placeholder="Minimum 8 characters"
                autocomplete="new-password">

        </div>


        <div class="input-field">

            <label for="reg_confirm_password">
                Confirm Password
            </label>

            <input
                type="password"
                id="reg_confirm_password"
                name="confirm_password"
                required
                minlength="8"
                placeholder="Repeat your password"
                autocomplete="new-password">

            <small
                id="password_match_error"
                style="color: red; display: none; margin-top: 5px;">

                Passwords do not match!

            </small>

        </div>


        <button
            type="submit"
            class="submit-btn"
            id="submit_btn">

            Register

        </button>


        <div class="footer-links">

            <a href="index.php">
                Already have an account? Sign In
            </a>

        </div>

    </form>

</div>


<script>

const form =
    document.getElementById('register_form');

const password =
    document.getElementById('reg_password');

const confirmPassword =
    document.getElementById('reg_confirm_password');

const errorText =
    document.getElementById('password_match_error');


function validatePasswords() {

    if (
        password.value !== confirmPassword.value &&
        confirmPassword.value !== ''
    ) {

        errorText.style.display = 'block';

        return false;

    } else {

        errorText.style.display = 'none';

        return true;

    }
}


confirmPassword.addEventListener(
    'input',
    validatePasswords
);

password.addEventListener(
    'input',
    validatePasswords
);


form.addEventListener('submit', function(e) {

    if (!validatePasswords()) {

        e.preventDefault();

        alert(
            'Please ensure your passwords match before submitting.'
        );

    }

});

</script>

</body>
</html>
