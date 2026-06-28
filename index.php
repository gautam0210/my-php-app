<?php
echo "<h1>Nginx + PHP-FPM 7.3 + MySQL 8.0 stack is running!</h1>";

$host = 'mysql';
$db   = 'appdb';
$user = 'appuser';
$pass = 'apppassword';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    echo "<p>Connected to MySQL successfully.</p>";
} catch (PDOException $e) {
    echo "<p>Connection failed: " . $e->getMessage() . "</p>";
}
