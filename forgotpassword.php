<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>NACIT Student Portal - Reset Password</title>
    <link rel="stylesheet" type="text/css" href="style.css?v=1.2">
</head>
<body>

<div class="login-container">

    <form
        class="login-card"
        action="backend.php"
        method="POST"
        id="reset_form">

        <h2>Account Recovery</h2>

        <p class="form-subtitle">
            Complete the verification question to reset your password.
        </p>


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


        <input
            type="hidden"
            name="action"
            value="reset_password">


        <div class="input-field">

            <label for="identity">
                Student ID or Email Address
            </label>

            <input
                type="text"
                id="identity"
                name="identity"
                required
                placeholder="NACIT/2026/001 or email@example.com"
                autocomplete="username">

        </div>


        <div class="input-field">

            <label>
                Security Question
            </label>

            <div class="code-box">

                <span class="code-comment">
                    // Solve this basic expression
                </span>
                <br>

                <span class="code-keyword">
                    let
                </span>

                result =
                <span class="code-number">10</span>
                +
                <span class="code-number">5</span>;

                <br>

                <span class="code-keyword">
                    console
                </span>.<span class="code-function">
                    log
                </span>(result);

                <span class="code-comment">
                    // Output: ___ ?
                </span>

            </div>

            <br>

            <input
                type="text"
                id="challenge_answer"
                name="challenge_answer"
                required
                placeholder="Type the final number answer"
                autocomplete="off">

            <small
                id="challenge_error"
                class="validation-error">
            </small>

        </div>


        <div
            id="password_reset_section"
            class="hidden-reset-fields">

            <div class="input-field">

                <label for="new_password">
                    New Password
                </label>

                <input
                    type="password"
                    id="new_password"
                    name="new_password"
                    minlength="8"
                    placeholder="Minimum 8 characters"
                    autocomplete="new-password">

            </div>


            <div class="input-field">

                <label for="confirm_new_password">
                    Confirm New Password
                </label>

                <input
                    type="password"
                    id="confirm_new_password"
                    name="confirm_new_password"
                    minlength="8"
                    placeholder="Repeat new password"
                    autocomplete="new-password">

                <small
                    id="password_match_error"
                    class="validation-error">
                </small>

            </div>

        </div>


        <button
            type="button"
            class="submit-btn"
            id="action_btn">

            Verify Answer

        </button>


        <div class="footer-links">

            <a href="index.php">
                Back to Sign In
            </a>

        </div>

    </form>

</div>


<script>

const actionBtn =
    document.getElementById('action_btn');

const challengeInput =
    document.getElementById('challenge_answer');

const challengeError =
    document.getElementById('challenge_error');

const passwordSection =
    document.getElementById('password_reset_section');

const form =
    document.getElementById('reset_form');

const newPassword =
    document.getElementById('new_password');

const confirmNewPassword =
    document.getElementById('confirm_new_password');

const matchError =
    document.getElementById('password_match_error');

let isChallengePassed = false;


actionBtn.addEventListener('click', function() {

    if (!isChallengePassed) {

        const answer =
            challengeInput.value.trim();

        if (answer === '15') {

            isChallengePassed = true;

            challengeError.innerText = '';

            challengeError.classList.remove('visible');

            challengeInput.classList.remove(
                'invalid-input'
            );

            challengeInput.classList.add(
                'valid-input'
            );

            challengeInput.readOnly = true;

            passwordSection.classList.add(
                'visible'
            );

            newPassword.required = true;

            confirmNewPassword.required = true;

            actionBtn.innerText =
                'Reset Password';

            actionBtn.type = 'submit';

        } else {

            challengeError.innerText =
                'Incorrect answer. Please try again!';

            challengeError.classList.add(
                'visible'
            );

            challengeInput.classList.add(
                'invalid-input'
            );

        }

    }

});


function verifyMatchingPasswords() {

    if (confirmNewPassword.value !== '') {

        if (
            newPassword.value !==
            confirmNewPassword.value
        ) {

            matchError.innerText =
                'Passwords do not match!';

            matchError.classList.add(
                'visible'
            );

            return false;

        } else {

            matchError.innerText = '';

            matchError.classList.remove(
                'visible'
            );

            return true;

        }

    }

    return false;
}


newPassword.addEventListener(
    'input',
    verifyMatchingPasswords
);

confirmNewPassword.addEventListener(
    'input',
    verifyMatchingPasswords
);


form.addEventListener('submit', function(e) {

    if (!isChallengePassed) {

        e.preventDefault();

        alert(
            'You must solve the security question first.'
        );

    } else if (
        newPassword.value !==
        confirmNewPassword.value
    ) {

        e.preventDefault();

        matchError.innerText =
            'Please ensure your new passwords match before submitting.';

        matchError.classList.add(
            'visible'
        );

    }

});

</script>

</body>
</html>
