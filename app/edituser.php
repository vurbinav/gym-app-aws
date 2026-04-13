<?php
include 'config.php';

if (!isset($_GET['id'])) {
    header('Location: usuarios.php');
    exit();
}

$id_user = $_GET['id'];

$stmt = $pdo->prepare("
    SELECT 
        u.id_user, 
        u.nombres, 
        u.apellidos, 
        u.id_role, 
        u.id_gym 
    FROM 
        gym_users u 
    WHERE u.id_user = ?
");
$stmt->execute([$id_user]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header('Location: usuarios.php');
    exit();
}

$roles_stmt = $pdo->prepare("SELECT id_role, role_name FROM gym_roles");
$roles_stmt->execute();
$roles = $roles_stmt->fetchAll(PDO::FETCH_ASSOC);

$gyms_stmt = $pdo->prepare("SELECT id_gym, name FROM gym");
$gyms_stmt->execute();
$gyms = $gyms_stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $nombres = $_POST['nombres'];
    $apellidos = $_POST['apellidos'];
    $id_role = $_POST['role'];
    $id_gym = $_POST['gym'];

    $stmt = $pdo->prepare("
        UPDATE gym_users 
        SET nombres = ?, apellidos = ?, id_role = ?, id_gym = ? 
        WHERE id_user = ?
    ");
    $stmt->execute([$nombres, $apellidos, $id_role, $id_gym, $id_user]);

    header('Location: usuarios.php');
    exit();
}
?>
<?php include 'header.php'; ?>

<div class="container-fluid">
    <div class="content-header">
        <h1>Edit User</h1>
    </div>
    <div class="content">
        <form method="POST">
            <div class="form-group">
                <label for="nombres">Names:</label>
                <input type="text" name="nombres" class="form-control" value="<?php echo htmlspecialchars($user['nombres'] ?? ''); ?>" required>
            </div>

            <div class="form-group">
                <label for="apellidos">ALast Names:</label>
                <input type="text" name="apellidos" class="form-control" value="<?php echo htmlspecialchars($user['apellidos'] ?? ''); ?>" required>
            </div>

            <div class="form-group">
                <label for="role">Role:</label>
                <select name="role" class="form-control" required>
                    <?php foreach ($roles as $role): ?>
                        <option value="<?php echo $role['id_role']; ?>" 
                            <?php echo (isset($user['id_role']) && $role['id_role'] == $user['id_role']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($role['role_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="gym">Gym:</label>
                <select name="gym" class="form-control" required>
                    <?php foreach ($gyms as $gym): ?>
                        <option value="<?php echo $gym['id_gym']; ?>" 
                            <?php echo (isset($user['id_gym']) && $gym['id_gym'] == $user['id_gym']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($gym['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="btn btn-success">Save Changes</button>
            <a href="usuarios.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>

<?php include 'footer.php'; ?>
