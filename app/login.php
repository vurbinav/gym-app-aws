<?php
session_start();
include 'config.php';

// Obtener lista de gimnasios para el registro
$stmtGyms = $pdo->prepare("SELECT id_gym, name FROM gym");
$stmtGyms->execute();
$gyms = $stmtGyms->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'login') {
        $email = $_POST['email'];
        $password = $_POST['password'];

        $stmt = $pdo->prepare("SELECT u.id_user, u.email, u.password, r.role_name 
                               FROM gym_users u
                               INNER JOIN gym_roles r ON u.id_role = r.id_role
                               WHERE u.email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id_user'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role_name'];

            header('Location: index.php');
            exit();
        } else {
            $error = "Email or password are incorrect.";
        }
    } elseif (isset($_POST['action']) && $_POST['action'] === 'register') {
        $email = $_POST['email'];
        $password = $_POST['password'];
        $nombres = $_POST['nombres'];
        $apellidos = $_POST['apellidos'];
        $id_gym = $_POST['gym'];

        $stmt = $pdo->prepare("SELECT email FROM gym_users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->rowCount() > 0) {
            $error = "Email address already registered.";
        } else {
            $stmt = $pdo->prepare("SELECT id_role FROM gym_roles WHERE role_name = 'User'");
            $stmt->execute();
            $role = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($role) {
                $role_id = $role['id_role'];

                $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
                $stmt = $pdo->prepare("INSERT INTO gym_users (email, password, nombres, apellidos, id_role, id_gym, creation_date) 
                                       VALUES (?, ?, ?, ?, ?, ?, NOW())");
                $stmt->execute([$email, $hashedPassword, $nombres, $apellidos, $role_id, $id_gym]);

                $success = "User registered succesfully.";
            } else {
                $error = "Error registering user. Role not found.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login y Registration</title>
    <link rel="stylesheet" href="css/sb-admin-2.min.css">
</head>
<body>
<div class="container my-5">
    <h1 class="text-center">Log In</h1>
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    <?php if (isset($success)): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>
    <form method="POST" class="mt-4">
        <input type="hidden" name="action" value="login">
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary btn-block">Log In</button>
    </form>

    <hr>
    <h1 class="text-center">Sign up</h1>
    <form method="POST" class="mt-4">
        <input type="hidden" name="action" value="register">
        <div class="form-group">
            <label for="nombres">Name:</label>
            <input type="text" name="nombres" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="apellidos">Last Name:</label>
            <input type="text" name="apellidos" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="gym">Select your gym:</label>
            <select name="gym" class="form-control" required>
                <option value="" disabled selected>Select a gym</option>
                <?php foreach ($gyms as $gym): ?>
                    <option value="<?php echo $gym['id_gym']; ?>"><?php echo $gym['name']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-success btn-block">Register</button>
    </form>
</div>
</body>
</html>
