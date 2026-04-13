<?php
$host = 'gym-app-db.c746awusyj9q.us-east-2.rds.amazonaws.com';
$dbname = 'gym';
$username = 'admin';
$password = 'GymApp2026!';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error in connecting to database: " . $e->getMessage());
}
?>