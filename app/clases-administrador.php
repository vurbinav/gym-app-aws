<?php
session_start();
include 'config.php';

if ($_SESSION['role'] !== 'Administrator') {
    header('Location: index.php');
    exit();
}

$stmt = $pdo->prepare("
    SELECT c.*, s.start_date, s.end_date, u.nombres AS instructor_name, u.apellidos AS instructor_lastname
    FROM gym_classes c
    INNER JOIN gym_schedule s ON c.id_class = s.id_class
    INNER JOIN gym_users u ON c.id_user = u.id_user
");
$stmt->execute();
$clases = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include 'header.php'; ?>
<div class="container my-5">
    <h1 class="text-center">All Classes</h1>
    <div class="row">
        <?php foreach ($clases as $clase): ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <?php if ($clase['img']): ?>
                        <img src="uploads/<?php echo $clase['img']; ?>" class="card-img-top" alt="Clase">
                    <?php endif; ?>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($clase['name']); ?></h5>
                        <p class="card-text">
                            <strong>Instructor:</strong> <?php echo htmlspecialchars($clase['instructor_name'] . ' ' . $clase['instructor_lastname']); ?><br>
                            <strong>Max Capacity:</strong> <?php echo $clase['max_capacity']; ?><br>
                            <strong>Start Date:</strong> <?php echo date('d/m/Y H:i', strtotime($clase['start_date'])); ?><br>
                            <strong>End Date:</strong> <?php echo date('d/m/Y H:i', strtotime($clase['end_date'])); ?>
                        </p>
                        <a href="detalle-clase.php?id=<?php echo $clase['id_class']; ?>" class="btn btn-primary">See Details</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php include 'footer.php'; ?>
