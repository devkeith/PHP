<?php
/**
 * config.php
 * Database connection for FoodFusion (database name: foodfusion)
 * Included at the top of every page that needs the database.
 */

declare(strict_types=1);

// Start the session for every page (login state, lockout tracking, etc.)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ---- Database credentials ----
// Update these to match your local / server MySQL setup.
define('DB_HOST', 'localhost');
define('DB_NAME', 'foodfusion');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

$dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (PDOException $e) {
    // In production, log this instead of exposing details to the visitor.
    die('Database connection failed. Please try again later.');
}
