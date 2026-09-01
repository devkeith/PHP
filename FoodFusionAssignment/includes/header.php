<?php
/**
 * includes/header.php
 * Shared navigation bar for every page on the site.
 * Expects $pageTitle to be set by the including page (optional).
 */
$pageTitle = $pageTitle ?? 'FoodFusion — Cook. Share. Belong.';
$isLoggedIn = isset($_SESSION['user_id']); // used once login is wired up
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($pageTitle) ?></title>
<meta name="description" content="FoodFusion is a home for home cooks — recipes, culinary tips and a community built around real kitchens.">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400;0,9..144,500;0,9..144,600;1,9..144,500&family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<a class="skip-link" href="#main-content">Skip to content</a>

<header class="site-header">
    <div class="nav-wrap">
        <a href="index.php" class="logo">Food<span>Fusion</span></a>

        <nav class="main-nav" id="main-nav" aria-label="Primary">
            <ul>
                <li><a href="index.php" class="active">Home</a></li>
                <li><a href="about.php">About Us</a></li>
                <li><a href="recipes.php">Recipes</a></li>
                <li><a href="cookbook.php">Community Cookbook</a></li>
                <li><a href="resources.php">Resources</a></li>
                <li><a href="contact.php">Contact</a></li>
            </ul>
        </nav>

        <div class="nav-actions">
            <?php if ($isLoggedIn): ?>
                <a href="account.php" class="btn btn-ghost">My Account</a>
                <a href="logout.php" class="btn btn-primary">Log Out</a>
            <?php else: ?>
                <a href="login.php" class="btn btn-ghost">Log In</a>
                <button type="button" class="btn btn-primary" id="openJoinModal">Join Us</button>
            <?php endif; ?>
        </div>

        <button type="button" class="nav-toggle" id="navToggle" aria-expanded="false" aria-controls="main-nav" aria-label="Toggle menu">
            <span></span><span></span><span></span>
        </button>
    </div>
</header>
