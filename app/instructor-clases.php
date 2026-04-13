<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'config.php';

if ($_SESSION['role'] !== 'Instructor') {
    header('Location: index.php');
    exit();
}

$id_user = $_SESSION['user_id'];

// Consulta modificada
$stmt = $pdo->prepare("
    SELECT c.*, s.start_date, s.end_date,
           (SELECT COUNT(*) FROM gym_member_classes mc WHERE mc.id_class = c.id_class AND mc.enrolled = 1) AS inscritos,
           (SELECT COUNT(*) FROM gym_member_classes mc WHERE mc.id_class = c.id_class AND mc.assisted = 1) AS asistentes,
           (SELECT COUNT(*) FROM gym_member_classes mc WHERE mc.id_class = c.id_class AND mc.absent = 1) AS ausentes
    FROM gym_classes c
    INNER JOIN gym_schedule s ON c.id_class = s.id_class
    WHERE c.id_user = ?
");
$stmt->execute([$id_user]);

$clases = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include 'header.php'; ?>
<div class="container my-5">
    <h1 class="text-center">Your Classes</h1>
    <a href="nueva-clase.php" class="btn btn-primary mb-4">Create Classes</a>
    <div class="row">
        <?php foreach ($clases as $clase): ?>
            <?php
            $is_past = strtotime($clase['end_date']) < time();
            $card_class = $is_past ? 'border-danger' : '';
            ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100 <?php echo $card_class; ?>">
                    <?php if ($clase['img']): ?>
                        <img src="uploads/<?php echo $clase['img']; ?>" class="card-img-top" alt="Clase">
                    <?php endif; ?>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($clase['name']); ?></h5>
                        <p class="card-text"><?php echo htmlspecialchars($clase['description']); ?></p>
                        <p>
                            <strong>Maximum Capacity:</strong> <?php echo $clase['max_capacity']; ?><br>
                            <strong>Enrolled:</strong> <?php echo $clase['inscritos']; ?><br>
                            <strong>Start Date:</strong> <?php echo date('d/m/Y H:i', strtotime($clase['start_date'])); ?><br>
                            <strong>End Date:</strong> <?php echo date('d/m/Y H:i', strtotime($clase['end_date'])); ?>
                        </p>
                        <?php if ($is_past): ?>
                            <p class="text-danger">
                                <strong>Assisted:</strong> <?php echo $clase['asistentes']; ?><br>
                                <strong>Absenented:</strong> <?php echo $clase['ausentes']; ?>
                            </p>
                        <?php endif; ?>
                        <a href="editar-clase.php?id=<?php echo $clase['id_class']; ?>" class="btn btn-primary">Show Details</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include 'footer.php'; ?>
