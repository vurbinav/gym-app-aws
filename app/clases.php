<?php
session_start();
include 'config.php';

if ($_SESSION['role'] !== 'User') {
    header('Location: index.php');
    exit();
}

$user_id = $_SESSION['user_id'];

$mis_cursos_stmt = $pdo->prepare("
    SELECT c.*, s.start_date, s.end_date
    FROM gym_classes c
    INNER JOIN gym_schedule s ON c.id_class = s.id_class
    INNER JOIN gym_member_classes mc ON c.id_class = mc.id_class
    WHERE mc.id_user = ? AND mc.enrolled = 1 AND s.end_date >= NOW()
");
$mis_cursos_stmt->execute([$user_id]);
$mis_cursos = $mis_cursos_stmt->fetchAll(PDO::FETCH_ASSOC);

$cursos_disponibles_stmt = $pdo->prepare("
    SELECT c.*, s.start_date, s.end_date,
           (SELECT COUNT(*) FROM gym_member_classes mc WHERE mc.id_class = c.id_class AND mc.enrolled = 1) AS inscritos
    FROM gym_classes c
    INNER JOIN gym_schedule s ON c.id_class = s.id_class
    WHERE s.start_date >= NOW()
      AND c.id_class NOT IN (
          SELECT mc.id_class 
          FROM gym_member_classes mc 
          WHERE mc.id_user = ? AND mc.enrolled = 1
      )
");
$cursos_disponibles_stmt->execute([$user_id]);
$cursos_disponibles = $cursos_disponibles_stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['inscribirse'])) {
        $id_class = $_POST['id_class'];

        $capacidad_stmt = $pdo->prepare("
            SELECT c.max_capacity, 
                   (SELECT COUNT(*) FROM gym_member_classes mc WHERE mc.id_class = c.id_class AND mc.enrolled = 1) AS inscritos
            FROM gym_classes c
            WHERE c.id_class = ?
        ");
        $capacidad_stmt->execute([$id_class]);
        $capacidad = $capacidad_stmt->fetch(PDO::FETCH_ASSOC);

        if ($capacidad['inscritos'] < $capacidad['max_capacity']) {
            $inscribir_stmt = $pdo->prepare("
                INSERT INTO gym_member_classes (id_user, id_class, enrolled) VALUES (?, ?, 1)
            ");
            $inscribir_stmt->execute([$user_id, $id_class]);
        }
    } elseif (isset($_POST['anular'])) {
        $id_class = $_POST['id_class'];

        $anular_stmt = $pdo->prepare("
            DELETE FROM gym_member_classes 
            WHERE id_user = ? AND id_class = ?
        ");
        $anular_stmt->execute([$user_id, $id_class]);
    }

    header('Location: clases.php');
    exit();
}
?>

<?php include 'header.php'; ?>
<div class="container my-5">
    <h1 class="text-center">Classes</h1>

    <h2 class="mt-4">My Classes</h2>
    <?php if (empty($mis_cursos)): ?>
        <p>You are not subscribed to any current classes.</p>
    <?php else: ?>
        <div class="row">
            <?php foreach ($mis_cursos as $curso): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <?php if ($curso['img']): ?>
                            <img src="uploads/<?php echo $curso['img']; ?>" class="card-img-top" alt="Curso">
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($curso['name']); ?></h5>
                            <p>
                                <strong>Start Date:</strong> <?php echo date('d/m/Y H:i', strtotime($curso['start_date'])); ?><br>
                                <strong>End Date:</strong> <?php echo date('d/m/Y H:i', strtotime($curso['end_date'])); ?>
                            </p>
                            <form method="POST" class="d-inline">
                                <input type="hidden" name="id_class" value="<?php echo $curso['id_class']; ?>">
                                <button type="submit" name="anular" class="btn btn-danger">Cancel Registration</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <h2 class="mt-4">Register to Classes</h2>
    <?php if (empty($cursos_disponibles)): ?>
        <p>There are currently no classes available.</p>
    <?php else: ?>
        <div class="row">
            <?php foreach ($cursos_disponibles as $curso): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <?php if ($curso['img']): ?>
                            <img src="uploads/<?php echo $curso['img']; ?>" class="card-img-top" alt="Curso">
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($curso['name']); ?></h5>
                            <p>
                                <strong>Maximum Capacity:</strong> <?php echo $curso['max_capacity']; ?><br>
                                <strong>Amount Joined:</strong> <?php echo $curso['inscritos']; ?><br>
                                <strong>Start Date:</strong> <?php echo date('d/m/Y H:i', strtotime($curso['start_date'])); ?><br>
                                <strong>End Date:</strong> <?php echo date('d/m/Y H:i', strtotime($curso['end_date'])); ?>
                            </p>
                            <?php if ($curso['inscritos'] < $curso['max_capacity']): ?>
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="id_class" value="<?php echo $curso['id_class']; ?>">
                                    <button type="submit" name="inscribirse" class="btn btn-primary">Sign up to this class now!</button>
                                </form>
                            <?php else: ?>
                                <p class="text-danger">Max capacity reached.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
<?php include 'footer.php'; ?>
