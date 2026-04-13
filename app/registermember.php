<?php
include 'config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $direccion = $_POST['direccion'];
    $membership_plan = $_POST['membership_plan'];
    $phone = $_POST['phone'];
    $ntarjeta = $_POST['ntarjeta'];
    $fvencimiento = $_POST['fvencimiento'];
    $cvv = $_POST['cvv'];
    $cobroautomatico = $_POST['cobroautomatico'];
    $id_user = $_SESSION['user_id'];

    $stmt = $pdo->prepare("
        INSERT INTO gym_members (direccion, membership_plan, phone, ntarjeta, fvencimiento, cvv, cobroautomatico, id_user) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([$direccion, $membership_plan, $phone, $ntarjeta, $fvencimiento, $cvv, $cobroautomatico, $id_user]);

    header('Location: confirmacion.php');
    exit();
}
?>
