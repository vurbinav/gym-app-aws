<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $nombres = $_POST['nombres'];
    $apellidos = $_POST['apellidos'];
    $id_role = $_POST['role'];
    $id_gym = $_POST['gym'];

    $stmt = $pdo->prepare("
        INSERT INTO gym_users (email, password, nombres, apellidos, id_role, id_gym) 
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([$email, $password, $nombres, $apellidos, $id_role, $id_gym]);

    header('Location: usuarios.php');
    exit();
}

$roles_stmt = $pdo->prepare("SELECT id_role, role_name FROM gym_roles");
$roles_stmt->execute();
$roles = $roles_stmt->fetchAll();

$gyms_stmt = $pdo->prepare("SELECT id_gym, name FROM gym");
$gyms_stmt->execute();
$gyms = $gyms_stmt->fetchAll();
?>
<?php include 'header.php'; ?>
<div class="container-fluid">
    <div class="content-header">
        <h1>Add User</h1>
    </div>
    <div class="content">
        <form method="POST">
            <div class="form-group">
                <label for="nombres">Nombres:</label>
                <input type="text" name="nombres" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="apellidos">Apellidos:</label>
                <input type="text" name="apellidos" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" name="email" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="password">Contraseña:</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="role">Rol:</label>
                <select name="role" class="form-control" required>
                    <option value="" disabled selected>Seleccione un rol</option>
                    <?php foreach ($roles as $role): ?>
                        <option value="<?php echo $role['id_role']; ?>">
                            <?php echo $role['role_name']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="gym">Gimnasio:</label>
                <select name="gym" class="form-control" required>
                    <option value="" disabled selected>Seleccione un gimnasio</option>
                    <?php foreach ($gyms as $gym): ?>
                        <option value="<?php echo $gym['id_gym']; ?>">
                            <?php echo $gym['name']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="btn btn-success">Guardar</button>
        </form>
    </div>
</div>
<?php include 'footer.php'; ?>