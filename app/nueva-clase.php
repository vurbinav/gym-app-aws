<?php
session_start();
include 'config.php';

if ($_SESSION['role'] !== 'Instructor') {
    header('Location: index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $max_capacity = $_POST['max_capacity'];
    $description = $_POST['description'];
    $date = $_POST['date'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $id_user = $_SESSION['user_id'];

    $start_date = $date . ' ' . $start_time;
    $end_date = $date . ' ' . $end_time;

    $img = null;
    if (isset($_FILES['img']) && $_FILES['img']['error'] === UPLOAD_ERR_OK) {
        $img_name = time() . '_' . $_FILES['img']['name'];
        move_uploaded_file($_FILES['img']['tmp_name'], 'uploads/' . $img_name);
        $img = $img_name;
    }

    $pdo->beginTransaction();
    try {
        $stmt = $pdo->prepare("
            INSERT INTO gym_classes (name, max_capacity, description, img, id_user) 
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([$name, $max_capacity, $description, $img, $id_user]);

        $id_class = $pdo->lastInsertId();

        $schedule_stmt = $pdo->prepare("
            INSERT INTO gym_schedule (id_instructor, id_class, start_date, end_date) 
            VALUES (?, ?, ?, ?)
        ");
        $schedule_stmt->execute([$id_user, $id_class, $start_date, $end_date]);

        $pdo->commit();

        header('Location: instructor-clases.php');
        exit();
    } catch (Exception $e) {
        $pdo->rollBack();
        die("Error: " . $e->getMessage());
    }
}
?>

<?php include 'header.php'; ?>
<div class="container my-5">
    <h1 class="text-center">Create Class</h1>

    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="name">Class Name:</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="max_capacity">Maximum Capacity:</label>
            <input type="number" name="max_capacity" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="description">Description:</label>
            <textarea name="description" class="form-control" rows="4" required></textarea>
        </div>
        <div class="form-group">
            <label for="img">Image:</label>
            <input type="file" name="img" class="form-control">
        </div>
        <div class="form-group">
            <label for="date">Class Date:</label>
            <input type="date" name="date" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="start_time">Start Time:</label>
            <select name="start_time" class="form-control" required>
                <?php for ($hour = 7; $hour <= 22; $hour++): ?>
                    <?php $time = sprintf('%02d:00:00', $hour); ?>
                    <option value="<?php echo $time; ?>"><?php echo date('g A', strtotime($time)); ?></option>
                <?php endfor; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="end_time">End Time:</label>
            <select name="end_time" class="form-control" required>
                <?php for ($hour = 8; $hour <= 23; $hour++): ?>
                    <?php $time = sprintf('%02d:00:00', $hour); ?>
                    <option value="<?php echo $time; ?>"><?php echo date('g A', strtotime($time)); ?></option>
                <?php endfor; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-success">Create Class</button>
        <a href="instructor-clases.php" class="btn btn-secondary">Back</a>
    </form>
</div>
<?php include 'footer.php'; ?>
