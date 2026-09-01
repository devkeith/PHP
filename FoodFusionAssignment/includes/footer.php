<?php
/**
 * includes/footer.php
 * Shared footer for every page on the site.
 */
?>
<footer class="site-footer">
    <div class="footer-top">
        <div class="footer-brand">
            <a href="index.php" class="logo">Food<span>Fusion</span></a>
            <p>A home for home cooks. Recipes, techniques and a community built around real kitchens, not perfect ones.</p>
            <ul class="social-links" aria-label="FoodFusion on social media">
                <li><a href="#" aria-label="FoodFusion on Instagram">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2.2c3.2 0 3.6 0 4.85.07 1.17.05 1.97.24 2.43.4a4.9 4.9 0 0 1 1.77 1.15 4.9 4.9 0 0 1 1.15 1.77c.16.46.35 1.26.4 2.43.07 1.25.07 1.65.07 4.85s0 3.6-.07 4.85c-.05 1.17-.24 1.97-.4 2.43a4.9 4.9 0 0 1-1.15 1.77 4.9 4.9 0 0 1-1.77 1.15c-.46.16-1.26.35-2.43.4-1.25.07-1.65.07-4.85.07s-3.6 0-4.85-.07c-1.17-.05-1.97-.24-2.43-.4a4.9 4.9 0 0 1-1.77-1.15 4.9 4.9 0 0 1-1.15-1.77c-.16-.46-.35-1.26-.4-2.43C2.2 15.6 2.2 15.2 2.2 12s0-3.6.07-4.85c.05-1.17.24-1.97.4-2.43A4.9 4.9 0 0 1 3.82 3 4.9 4.9 0 0 1 5.59 1.8c.46-.16 1.26-.35 2.43-.4C9.27 1.33 9.67 1.33 12 1.33Zm0 3.16A5.64 5.64 0 1 0 17.64 11 5.64 5.64 0 0 0 12 5.36Zm0 9.3A3.66 3.66 0 1 1 15.66 11 3.66 3.66 0 0 1 12 14.66Zm5.86-9.53a1.32 1.32 0 1 1-1.32-1.32 1.32 1.32 0 0 1 1.32 1.32Z"/></svg>
                </a></li>
                <li><a href="#" aria-label="FoodFusion on Facebook">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M13.5 22v-8.4h2.8l.42-3.3h-3.22V8.1c0-.96.27-1.6 1.65-1.6h1.76V3.55A23.6 23.6 0 0 0 14.4 3.4c-2.5 0-4.2 1.53-4.2 4.34v2.56H7.4v3.3h2.8V22Z"/></svg>
                </a></li>
                <li><a href="#" aria-label="FoodFusion on Pinterest">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2a10 10 0 0 0-3.65 19.3c-.05-.8-.1-2.02.02-2.9.11-.78 1.14-5.24 1.14-5.24s-.29-.58-.29-1.44c0-1.35.78-2.35 1.75-2.35.83 0 1.23.62 1.23 1.36 0 .83-.53 2.08-.8 3.23-.23.97.48 1.75 1.43 1.75 1.72 0 2.95-2.21 2.95-4.83 0-2-1.35-3.5-3.8-3.5-2.77 0-4.5 2.07-4.5 4.38 0 .8.24 1.36.6 1.8.17.2.19.28.13.51-.05.17-.15.6-.2.77-.06.24-.26.33-.48.24-1.34-.55-1.96-2.02-1.96-3.67 0-2.73 2.3-6 6.86-6 3.66 0 6.07 2.65 6.07 5.5 0 3.76-2.1 6.58-5.2 6.58-1.04 0-2.02-.56-2.36-1.2l-.66 2.58c-.2.75-.6 1.68-.94 2.28A10 10 0 1 0 12 2Z"/></svg>
                </a></li>
                <li><a href="#" aria-label="FoodFusion on TikTok">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M16.5 2h-3v13.6a2.6 2.6 0 1 1-1.9-2.5V9.9a5.9 5.9 0 1 0 4.9 5.8V8.3a7.6 7.6 0 0 0 4.5 1.5V6.6a4.6 4.6 0 0 1-4.5-4.6Z"/></svg>
                </a></li>
            </ul>
        </div>

        <nav class="footer-col" aria-label="Explore">
            <h3>Explore</h3>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="about.php">About Us</a></li>
                <li><a href="recipes.php">Recipe Collection</a></li>
                <li><a href="cookbook.php">Community Cookbook</a></li>
            </ul>
        </nav>

        <nav class="footer-col" aria-label="Resources">
            <h3>Resources</h3>
            <ul>
                <li><a href="resources.php#culinary">Culinary Resources</a></li>
                <li><a href="resources.php#educational">Educational Resources</a></li>
                <li><a href="contact.php">Contact Us</a></li>
            </ul>
        </nav>

        <nav class="footer-col" aria-label="Legal">
            <h3>Legal</h3>
            <ul>
                <li><a href="privacy-policy.php">Privacy Policy</a></li>
                <li><a href="cookie-policy.php">Cookie Policy</a></li>
                <li><a href="terms.php">Terms of Use</a></li>
            </ul>
        </nav>
    </div>

    <div class="footer-bottom">
        <p>&copy; <?= date('Y') ?> FoodFusion. All rights reserved.</p>
    </div>
</footer>

<!-- Join Us modal (shared across the site) -->
<div class="modal-overlay" id="joinModalOverlay">
    <div class="modal" role="dialog" aria-modal="true" aria-labelledby="joinModalTitle">
        <button type="button" class="modal-close" id="closeJoinModal" aria-label="Close">&times;</button>
        <h2 id="joinModalTitle">Join FoodFusion</h2>
        <p class="modal-sub">Create an account to save recipes, post to the Community Cookbook and get culinary tips.</p>

        <form id="joinForm" action="register.php" method="POST" novalidate>
            <div class="form-row">
                <div class="form-field">
                    <label for="firstName">First name</label>
                    <input type="text" id="firstName" name="first_name" autocomplete="given-name" required>
                </div>
                <div class="form-field">
                    <label for="lastName">Last name</label>
                    <input type="text" id="lastName" name="last_name" autocomplete="family-name" required>
                </div>
            </div>
            <div class="form-field">
                <label for="joinEmail">Email address</label>
                <input type="email" id="joinEmail" name="email" autocomplete="email" required>
            </div>
            <div class="form-field">
                <label for="joinPassword">Password</label>
                <input type="password" id="joinPassword" name="password" autocomplete="new-password" minlength="8" required>
                <span class="field-hint">At least 8 characters.</span>
            </div>
            <p class="form-error" id="joinFormError" role="alert" hidden></p>
            <button type="submit" class="btn btn-primary btn-block">Create account</button>
        </form>

        <p class="modal-footnote">Already have an account? <a href="login.php">Log in</a></p>
    </div>
</div>

<!-- Cookie consent banner -->
<div class="cookie-banner" id="cookieBanner" role="dialog" aria-live="polite" aria-label="Cookie notice">
    <p>We use cookies to keep you signed in and to understand how people use FoodFusion. Read our <a href="cookie-policy.php">Cookie Policy</a>.</p>
    <div class="cookie-actions">
        <button type="button" class="btn btn-ghost" id="cookieDecline">Decline</button>
        <button type="button" class="btn btn-primary" id="cookieAccept">Accept</button>
    </div>
</div>

<script src="assets/js/script.js"></script>
</body>
</html>
