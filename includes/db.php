<?php
// includes/db.php
// Load environment variables from .env (only if not already loaded)
if (!function_exists('env')) {
    require_once __DIR__ . '/env.php';
}

$host     = env('DB_HOST', 'localhost');
$dbname   = env('DB_NAME', 'al_shifah_db');
$username = env('DB_USER', 'root');
$password = env('DB_PASS', '');

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);

    // Set PDO options
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

} catch (PDOException $e) {
    $debug = env('APP_DEBUG', 'false') === 'true';
    $msg   = $debug
        ? "Database connection failed: " . $e->getMessage()
        : "Database connection failed. Please try again later.";
    die($msg);
}
?>
