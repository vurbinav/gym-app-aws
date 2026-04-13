<?php
session_start();
include 'config.php';

if ($_SESSION['role'] !== 'Administrator') {
    header('Location: index.php');
    exit();
}

if (!isset($_GET['id'])) {
    header('Location: clases.php');
    exit();
}

$id_class = $_GET['id'];

$stmt = $pdo->prepare("
    SELECT c.*, s.start_date, s.end_date, u.nombres AS instructor_name, u.apellidos AS instructor_lastname
    FROM gym_classes c
    INNER JOIN gym_schedule s ON c.id_class = s.id_class
    INNER JOIN gym_users u ON c.id_user = u.id_user
    WHERE c.id_class = ?
");
$stmt->execute([$id_class]);
$clase = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$clase) {
    header('Location: clases.php');
    exit();
}

$members_stmt = $pdo->prepare("
    SELECT u.nombres, u.apellidos, u.email, mc.enrolled, mc.assisted, mc.absent
    FROM gym_member_classes mc
    INNER JOIN gym_users u ON mc.id_user = u.id_user
    WHERE mc.id_class = ?
");
$members_stmt->execute([$id_class]);
$members = $members_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include 'header.php'; ?>
<div class="container my-5">
    <h1 class="text-center">Class Details</h1>
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title"><?php echo htmlspecialchars($clase['name']); ?></h5>
            <p>
                <strong>Instructor:</strong> <?php echo htmlspecialchars($clase['instructor_name'] . ' ' . $clase['instructor_lastname']); ?><br>
                <strong>Max Capacity:</strong> <?php echo $clase['max_capacity']; ?><br>
                <strong>Start Date:</strong> <?php echo date('d/m/Y H:i', strtotime($clase['start_date'])); ?><br>
                <strong>End Date:</strong> <?php echo date('d/m/Y H:i', strtotime($clase['end_date'])); ?>
            </p>
        </div>
    </div>

    <h3 class="mt-5">Registered users</h3>
    <?php if (empty($members)): ?>
        <p>There are no users enrolled in this class.</p>
    <?php else: ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Enrolled</th>
                    <th>Assisted</th>
                    <th>Absence</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($members as $member): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($member['nombres'] . ' ' . $member['apellidos']); ?></td>
                        <td><?php echo htmlspecialchars($member['email']); ?></td>
                        <td><?php echo $member['enrolled'] ? 'Yes' : 'No'; ?></td>
                        <td><?php echo $member['assisted'] ? 'Yes' : 'No'; ?></td>
                        <td><?php echo $member['absent'] ? 'Yes' : 'No'; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
<?php include 'footer.php'; ?>
