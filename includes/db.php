<?php
// includes/db.php

$host = 'localhost';
$dbname = 'al_shifah_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    
    // Set PDO options
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    
} catch (PDOException $e) {
    // In production, you'd log this and show a generic error
    die("Database connection failed. Please ensure MySQL is running and the database is imported. Error: " . $e->getMessage());
}
?>
