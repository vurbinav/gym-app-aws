<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'config.php';

if ($_SESSION['role'] !== 'Instructor') {
    header('Location: index.php');
    exit();
}

if (!isset($_GET['id'])) {
    header('Location: asistencias.php');
    exit();
}

$id_class = $_GET['id'];

$stmt = $pdo->prepare("
    SELECT u.id_user, u.nombres, u.apellidos, u.email, mc.assisted, mc.absent
    FROM gym_member_classes mc
    INNER JOIN gym_users u ON mc.id_user = u.id_user
    WHERE mc.id_class = ?
");
$stmt->execute([$id_class]);

$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pdo->beginTransaction();
    try {
        foreach ($_POST['asistencia'] as $id_user => $asistencia) {
            if ($asistencia === 'si') {
                $update_stmt = $pdo->prepare("
                    UPDATE gym_member_classes
                    SET assisted = 1, absent = 0
                    WHERE id_class = ? AND id_user = ?
                ");
            } else {
                $update_stmt = $pdo->prepare("
                    UPDATE gym_member_classes
                    SET assisted = 0, absent = 1
                    WHERE id_class = ? AND id_user = ?
                ");
            }
            $update_stmt->execute([$id_class, $id_user]);
        }

        $pdo->commit();
        header('Location: asistencias.php');
        exit();
    } catch (Exception $e) {
        $pdo->rollBack();
        die("Error: " . $e->getMessage());
    }
}
?>

<?php include 'header.php'; ?>
<div class="container my-5">
    <h1 class="text-center">Assistance per Class</h1>

    <?php if (empty($usuarios)): ?>
        <p class="text-center text-danger">No users signed up to this class yet.</p>
        <div class="text-center">
            <a href="asistencias.php" class="btn btn-secondary">Back</a>
        </div>
    <?php else: ?>
        <form method="POST">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Assisted</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($usuarios as $usuario): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($usuario['nombres'] . ' ' . $usuario['apellidos']); ?></td>
                            <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                            <td>
                                <div class="form-check form-check-inline">
                                    <input type="radio" name="asistencia[<?php echo $usuario['id_user']; ?>]" value="si" class="form-check-input" id="asistio-<?php echo $usuario['id_user']; ?>" <?php echo $usuario['assisted'] ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="asistio-<?php echo $usuario['id_user']; ?>">Yes</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input type="radio" name="asistencia[<?php echo $usuario['id_user']; ?>]" value="no" class="form-check-input" id="no-asistio-<?php echo $usuario['id_user']; ?>" <?php echo $usuario['absent'] ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="no-asistio-<?php echo $usuario['id_user']; ?>">No</label>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <button type="submit" class="btn btn-success">Save Assistance</button>
            <a href="asistencias.php" class="btn btn-secondary">Back</a>
        </form>
    <?php endif; ?>
</div>
<?php include 'footer.php'; ?>
