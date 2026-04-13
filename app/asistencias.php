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

$stmt = $pdo->prepare("
    SELECT c.id_class, c.name, s.start_date, s.end_date
    FROM gym_classes c
    INNER JOIN gym_schedule s ON c.id_class = s.id_class
    WHERE c.id_user = ? AND s.end_date >= NOW()
");
$stmt->execute([$id_user]);

$clases = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include 'header.php'; ?>
<div class="container my-5">
    <h1 class="text-center">Current classes for assistance</h1>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Class Name</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($clases as $clase): ?>
                <tr>
                    <td><?php echo htmlspecialchars($clase['name']); ?></td>
                    <td><?php echo date('d/m/Y H:i', strtotime($clase['start_date'])); ?></td>
                    <td><?php echo date('d/m/Y H:i', strtotime($clase['end_date'])); ?></td>
                    <td>
                        <a href="asistencia-clase.php?id=<?php echo $clase['id_class']; ?>" class="btn btn-primary btn-sm">Assistance</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php include 'footer.php'; ?>
