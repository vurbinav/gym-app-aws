<?php
session_start();
include 'config.php';

if ($_SESSION['role'] !== 'Instructor') {
    header('Location: index.php');
    exit();
}

if (!isset($_GET['id'])) {
    header('Location: instructor-clases.php');
    exit();
}

$id_class = $_GET['id'];
$id_user = $_SESSION['user_id'];

$stmt = $pdo->prepare("
    SELECT c.*, s.start_date, s.end_date
    FROM gym_classes c
    INNER JOIN gym_schedule s ON c.id_class = s.id_class
    WHERE c.id_class = ? AND c.id_user = ?
");
$stmt->execute([$id_class, $id_user]);
$clase = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$clase) {
    header('Location: instructor-clases.php');
    exit();
}

$is_past = strtotime($clase['end_date']) < time();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$is_past) {
    $name = $_POST['name'];
    $max_capacity = $_POST['max_capacity'];
    $description = $_POST['description'];
    $date = $_POST['date'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];

    $start_date = $date . ' ' . $start_time;
    $end_date = $date . ' ' . $end_time;

    if (strtotime($end_date) <= strtotime($start_date)) {
        die("La hora de término debe ser posterior a la hora de inicio.");
    }

    $img = $clase['img'];
    if (isset($_FILES['img']) && $_FILES['img']['error'] === UPLOAD_ERR_OK) {
        $img_name = time() . '_' . $_FILES['img']['name'];
        move_uploaded_file($_FILES['img']['tmp_name'], 'uploads/' . $img_name);
        $img = $img_name;
    }

    $pdo->beginTransaction();
    try {
        $stmt = $pdo->prepare("
            UPDATE gym_classes 
            SET name = ?, max_capacity = ?, description = ?, img = ? 
            WHERE id_class = ? AND id_user = ?
        ");
        $stmt->execute([$name, $max_capacity, $description, $img, $id_class, $id_user]);

        $schedule_stmt = $pdo->prepare("
            UPDATE gym_schedule 
            SET start_date = ?, end_date = ? 
            WHERE id_class = ?
        ");
        $schedule_stmt->execute([$start_date, $end_date, $id_class]);

        $pdo->commit();
        header('Location: instructor-clases.php');
        exit();
    } catch (Exception $e) {
        $pdo->rollBack();
        die("Error: " . $e->getMessage());
    }
}

if ($is_past) {
    $members_stmt = $pdo->prepare("
        SELECT u.nombres, u.apellidos, u.email,
               CASE 
                   WHEN mc.assisted = 1 THEN 'Asistió'
                   WHEN mc.absent = 1 THEN 'Faltó'
                   ELSE 'Sin información'
               END AS estado_asistencia
        FROM gym_member_classes mc
        INNER JOIN gym_users u ON mc.id_user = u.id_user
        WHERE mc.id_class = ?
    ");
    $members_stmt->execute([$id_class]);
    $members = $members_stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $members_stmt = $pdo->prepare("
    SELECT 
        u.nombres, 
        u.apellidos, 
        u.email, 
        p.plan_type
    FROM 
        gym_member_classes mc
    INNER JOIN 
        gym_users u ON mc.id_user = u.id_user
    INNER JOIN 
        gym_plan p ON u.id_role = p.id_plan -- Relacionar según el rol del usuario
    WHERE 
        mc.id_class = ? 
        AND mc.enrolled = 1;
    ");
    $members_stmt->execute([$id_class]);
    $members = $members_stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<?php include 'header.php'; ?>
<div class="container my-5">
    <?php if ($is_past): ?>
        <h1 class="text-center">Class Details</h1>
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title"><?php echo htmlspecialchars($clase['name']); ?></h5>
                <p class="card-text"><?php echo htmlspecialchars($clase['description']); ?></p>
                <p>
                    <strong>Start Date:</strong> <?php echo date('d/m/Y H:i', strtotime($clase['start_date'])); ?><br>
                    <strong>End Date:</strong> <?php echo date('d/m/Y H:i', strtotime($clase['end_date'])); ?>
                </p>
            </div>
        </div>

        <h3 class="mt-5">Enrolled Users</h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Assistance</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($members as $member): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($member['nombres'] . ' ' . $member['apellidos']); ?></td>
                        <td><?php echo htmlspecialchars($member['email']); ?></td>
                        <td><?php echo htmlspecialchars($member['estado_asistencia']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <a href="instructor-clases.php" class="btn btn-secondary">Volver</a>
    <?php else: ?>
        <h1 class="text-center">Edit Class</h1>
        <div class="card mb-4">
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="name">Class Name:</label>
                        <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($clase['name']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="max_capacity">Max Capacity:</label>
                        <input type="number" name="max_capacity" class="form-control" value="<?php echo $clase['max_capacity']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="description">Description:</label>
                        <textarea name="description" class="form-control" rows="4" required><?php echo htmlspecialchars($clase['description']); ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="img">Image ((leave empty to not change)):</label>
                        <input type="file" name="img" class="form-control">
                        <?php if ($clase['img']): ?>
                            <img src="uploads/<?php echo $clase['img']; ?>" class="img-thumbnail mt-2" alt="Clase" width="150">
                        <?php endif; ?>
                    </div>
                    <div class="form-group">
                        <label for="date">Class Dates:</label>
                        <input type="date" name="date" class="form-control" value="<?php echo date('Y-m-d', strtotime($clase['start_date'])); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="start_time">Start Time:</label>
                        <select name="start_time" class="form-control" required>
                            <?php for ($hour = 7; $hour <= 22; $hour++): ?>
                                <?php $time = sprintf('%02d:00:00', $hour); ?>
                                <option value="<?php echo $time; ?>" <?php echo ($time === date('H:i:s', strtotime($clase['start_date']))) ? 'selected' : ''; ?>>
                                    <?php echo date('g A', strtotime($time)); ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="end_time">End Time:</label>
                        <select name="end_time" class="form-control" required>
                            <?php for ($hour = 8; $hour <= 23; $hour++): ?>
                                <?php $time = sprintf('%02d:00:00', $hour); ?>
                                <option value="<?php echo $time; ?>" <?php echo ($time === date('H:i:s', strtotime($clase['end_date']))) ? 'selected' : ''; ?>>
                                    <?php echo date('g A', strtotime($time)); ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-success">Update</button>
                    <a href="instructor-clases.php" class="btn btn-secondary">Back</a>
                </form>
            </div>
            <h3 class="mt-5">Enrolled Users</h3>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Plan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($members as $member): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($member['nombres'] . ' ' . $member['apellidos']); ?></td>
                            <td><?php echo htmlspecialchars($member['email']); ?></td>
                            <td><?php echo htmlspecialchars($member['plan_type']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
<?php include 'footer.php'; ?>