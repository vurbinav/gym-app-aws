<?php
include 'config.php';

if (!isset($_GET['id'])) {
    header('Location: users.php');
    exit();
}

$id_user = $_GET['id'];

$stmt = $pdo->prepare("DELETE FROM gym_users WHERE id_user = ?");
$stmt->execute([$id_user]);

header('Location: usuarios.php');
exit();
?>