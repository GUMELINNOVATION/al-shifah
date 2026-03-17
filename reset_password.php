<?php
// reset_password.php
require_once 'includes/db.php';

$username = 'admin';
$password = 'admin123';
$hash = password_hash($password, PASSWORD_DEFAULT);

try {
    // Check if admin exists
    $stmt = $pdo->prepare("SELECT id FROM admins WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user) {
        $stmt = $pdo->prepare("UPDATE admins SET password = ? WHERE id = ?");
        $stmt->execute([$hash, $user['id']]);
        echo "<h1>Success!</h1><p>Admin password has been reset to: <strong>$password</strong></p>";
    } else {
        $stmt = $pdo->prepare("INSERT INTO admins (username, password, email) VALUES (?, ?, ?)");
        $stmt->execute([$username, $hash, 'admin@alshifah.org']);
        echo "<h1>Success!</h1><p>Admin user created with password: <strong>$password</strong></p>";
    }
    echo "<p><a href='login.php'>Go to Login</a></p>";
} catch (PDOException $e) {
    echo "<h1>Error!</h1><p>" . $e->getMessage() . "</p>";
}
?>
