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
        <form class="login-card" action="register-backend.php" method="POST" id="register_form">
            <h2>Create Student Account</h2>
            
            <?php 
            session_start();
            if(!empty($_SESSION['error'])): 
            ?>
                <div class="error-banner"><?= htmlspecialchars($_SESSION['error']) ?></div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <?php if(!empty($_SESSION['success'])): ?>
                <div class="success-banner" style="color: green; background: #e6f4ea; padding: 10px; margin-bottom: 15px; border-radius: 4px;">
                    <?= htmlspecialchars($_SESSION['success']) ?>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

            <!-- Full Name -->
            <div class="input-field">
                <label for="reg_fullname">Full Name</label>
                <input type="text" id="reg_fullname" name="fullname" required placeholder="John Doe">
            </div>

            <!-- Student ID -->
            <div class="input-field">
                <label for="reg_studentid">Student ID Number</label>
                <input type="text" id="reg_studentid" name="student_id" required placeholder="e.g., NACIT/2026/001">
            </div>

            <!-- Email Address -->
            <div class="input-field">
                <label for="reg_email">Email Address</label>
                <input type="email" id="reg_email" name="email" required placeholder="johndoe@example.com">
            </div>

            <!-- Password Input -->
            <div class="input-field">
                <label for="reg_password">Password</label>
                <input type="password" id="reg_password" name="password" required placeholder="Minimum 8 characters">
            </div>

            <!-- Confirm Password Input -->
            <div class="input-field">
                <label for="reg_confirm_password">Confirm Password</label>
                <input type="password" id="reg_confirm_password" name="confirm_password" required placeholder="Repeat your password">
                <small id="password_match_error" style="color: red; display: none; margin-top: 5px;">Passwords do not match!</small>
            </div>

            <button type="submit" class="submit-btn" id="submit_btn" href="index.php">Register</button>

            <div class="footer-links">
                <a href="index.php">Already have an account? Sign In</a>
            </div>
        </form>
    </div>

    <!-- Client-side Password Match Validation -->
    <script>
        const form = document.getElementById('register_form');
        const password = document.getElementById('reg_password');
        const confirmPassword = document.getElementById('reg_confirm_password');
        const errorText = document.getElementById('password_match_error');

        function validatePasswords() {
            if (password.value !== confirmPassword.value && confirmPassword.value !== '') {
                errorText.style.display = 'block';
                return false;
            } else {
                errorText.style.display = 'none';
                return true;
            }
        }

        confirmPassword.addEventListener('input', validatePasswords);
        password.addEventListener('input', validatePasswords);

        form.addEventListener('submit', function(e) {
            if (!validatePasswords()) {
                e.preventDefault(); // Stop form submission if passwords don't match
                alert('Please ensure your passwords match before submitting.');
            }
        });
    </script>
</body>
</html>
