<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>NACIT Student Portal - Reset Challenge</title>
    <link rel="stylesheet" type="text/css" href="style.css?v=1.2">
</head>
<body>
    <div class="login-container">
        <form class="login-card" action="reset-backend.php" method="POST" id="reset_form">
            <h2>Account Recovery</h2>
            <p class="form-subtitle">Complete the human verification question to reset your password.</p>
            
            <?php 
            session_start();
            if(!empty($_SESSION['error'])): 
            ?>
                <div class="error-banner"><?= htmlspecialchars($_SESSION['error']) ?></div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <br>
            
            <div class="input-field">
                <label>Student ID or Email Address</label>
				<input type="text" id="student_id email_address" name="password" required placeholder="student id/emal">
            </div>

            <!-- Step 2: Simplified Challenge Block -->
            <div class="input-field">
                <label>Security Question</label>

                <div class="code-box">
                    <span class="code-comment">// Solve this basic expression</span><br>
                    <span class="code-keyword">let</span> result = <span class="code-number">10</span> + <span class="code-number">5</span>;<br>
                    <span class="code-keyword">console</span>.<span class="code-function">log</span>(result); <span class="code-comment">// Output: ___ ?</span>
                </div>
                <br>
                <input type="text" id="challenge_answer" name="challenge_answer" required placeholder="Type the final number answer" autocomplete="off">
                <!-- Message container starts completely empty -->
                <small id="challenge_error" class="validation-error"></small>
            </div>

            <!-- Step 3: Hidden Reset Password Fields (Revealed only when challenge is passed) -->
            <div id="password_reset_section" class="hidden-reset-fields">
                <div class="input-field">
                    <label for="new_password">New Password</label>
                    <input type="password" id="new_password" name="new_password" placeholder="Minimum 8 characters">
                </div>
                <div class="input-field">
                    <label for="confirm_new_password">Confirm New Password</label>
                    <input type="password" id="confirm_new_password" name="confirm_new_password" placeholder="Repeat new password">
                    <!-- Message container starts completely empty -->
                    <small id="password_match_error" class="validation-error"></small>
                </div>
            </div>

            <!-- Action Button -->
            <button type="button" class="submit-btn" id="action_btn" href="index.php">Verify Answer</button>

            <div class="footer-links">
                <a href="index.php">Back to Sign In</a>
            </div>
        </form>
    </div>

    <!-- Frontend Challenge Validation & Security Check -->
    <script>
        const actionBtn = document.getElementById('action_btn');
        const challengeInput = document.getElementById('challenge_answer');
        const challengeError = document.getElementById('challenge_error');
        const passwordSection = document.getElementById('password_reset_section');
        const form = document.getElementById('reset_form');
        
        const newPassword = document.getElementById('new_password');
        const confirmNewPassword = document.getElementById('confirm_new_password');
        const matchError = document.getElementById('password_match_error');

        let isChallengePassed = false;

        // Challenge validation upon clicking verification button
        actionBtn.addEventListener('click', function(e) {
            if (!isChallengePassed) {
                const answer = challengeInput.value.trim();
                
                if (answer === '15') {
                    // Reset errors and lock correct challenge input
                    isChallengePassed = true;
                    challengeError.innerText = ''; 
                    challengeError.classList.remove('visible');
                    challengeInput.classList.remove('invalid-input');
                    challengeInput.classList.add('valid-input');
                    challengeInput.readOnly = true;
                    
                    // Display hidden password section safely
                    passwordSection.classList.add('visible');
                    newPassword.required = true;
                    confirmNewPassword.required = true;
                    
                    // Transform action button into a standard submission button
                    actionBtn.innerText = 'Reset Password';
                    actionBtn.type = 'submit';
                } else {
                    // Inject message and style instantly when validation fails
                    challengeError.innerText = 'Incorrect answer. Please try again!';
                    challengeError.classList.add('visible');
                    challengeInput.classList.add('invalid-input');
                }
            }
        });

        // Real-time password checking logic via text input entry
        function verifyMatchingPasswords() {
            // Only perform validations once the user starts typing inside the repeat field
            if (confirmNewPassword.value !== '') {
                if (newPassword.value !== confirmNewPassword.value) {
                    matchError.innerText = 'Passwords do not match!';
                    matchError.classList.add('visible');
                    return false;
                } else {
                    // Clear message text out of view if passwords successfully match up
                    matchError.innerText = '';
                    matchError.classList.remove('visible');
                    return true;
                }
            }
            return false;
        }

        newPassword.addEventListener('input', verifyMatchingPasswords);
        confirmNewPassword.addEventListener('input', verifyMatchingPasswords);

        // Final safety net form submission gate
        form.addEventListener('submit', function(e) {
            if (!isChallengePassed) {
                e.preventDefault();
                alert('You must solve the security question first.');
            } else if (newPassword.value !== confirmNewPassword.value) {
                e.preventDefault();
                matchError.innerText = 'Please ensure your new passwords match before submitting.';
                matchError.classList.add('visible');
            }
        });
    </script>
</body>
</html>
