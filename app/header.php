<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("
    SELECT COUNT(*) 
    FROM gym_members 
    WHERE id_user = ?
");
$stmt->execute([$user_id]);

$is_member = $stmt->fetchColumn() > 0;

$stmt = $pdo->prepare("SELECT nombres, apellidos FROM gym_users WHERE id_user = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("Error: Usuario no encontrado.");
}

$role = $_SESSION['role'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Gym</title>

    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <link href="css/sb-admin-2.min.css" rel="stylesheet">
</head>
<body id="page-top">

    <div id="wrapper">

        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
        <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php">
            <div class="sidebar-brand-text mx-3">Options</div>
        </a>
        <hr class="sidebar-divider">

        <?php if ($role === 'User'): ?>
            <?php if ($is_member): ?>
                <li class="nav-item">
                    <a class="nav-link" href="clases.php">
                        <i class="fas fa-dumbbell fa-fw"></i>
                        <span>Classes</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="horario-user.php">
                        <i class="fas fa-calendar-alt fa-fw"></i>
                        <span>Schedules</span>
                    </a>
                </li>
            <?php else: ?>
                <li class="nav-item">
                    <a class="nav-link" href="planes.php">
                        <i class="fas fa-clipboard-list fa-fw"></i>
                        <span>Plans</span>
                    </a>
                </li>
            <?php endif; ?>
        <?php elseif ($role === 'Instructor'): ?>
            <li class="nav-item">
                <a class="nav-link" href="instructor-clases.php">
                    <i class="fas fa-dumbbell fa-fw"></i>
                    <span>Classes</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="horario-instructor.php">
                    <i class="fas fa-calendar-alt fa-fw"></i>
                    <span>Schedules</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="asistencias.php">
                    <i class="fas fa-clipboard-check fa-fw"></i>
                    <span>Assistance</span>
                </a>
            </li>

        <?php elseif ($role === 'Administrator'): ?>
            <li class="nav-item">
                <a class="nav-link" href="clases-administrador.php">
                    <i class="fas fa-dumbbell fa-fw"></i>
                    <span>Clases</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="usuarios.php">
                    <i class="fas fa-users fa-fw"></i>
                    <span>Users</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="instructores.php">
                    <i class="fas fa-user-tie fa-fw"></i>
                    <span>Instructors</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="miembros.php">
                    <i class="fas fa-user fa-fw"></i>
                    <span>Members</span>
                </a>
            </li>
        <?php endif; ?>
        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">

                        <!-- Nav Item - Search Dropdown (Visible Only XS) -->
                        <li class="nav-item dropdown no-arrow d-sm-none">
                            <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-search fa-fw"></i>
                            </a>
                            <!-- Dropdown - Messages -->
                            <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in"
                                aria-labelledby="searchDropdown">
                                <form class="form-inline mr-auto w-100 navbar-search">
                                    <div class="input-group">
                                        <input type="text" class="form-control bg-light border-0 small"
                                            placeholder="Search for..." aria-label="Search"
                                            aria-describedby="basic-addon2">
                                        <div class="input-group-append">
                                            <button class="btn btn-primary" type="button">
                                                <i class="fas fa-search fa-sm"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </li>

                        <div class="topbar-divider d-none d-sm-block"></div>

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                                    <?php echo htmlspecialchars($user['nombres'] . ' ' . $user['apellidos']); ?>
                                </span>
                                <img class="img-profile rounded-circle"
                                    src="img/undraw_profile.svg">
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="logout.php">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Logout
                                </a>
                            </div>
                        </li>

                    </ul>
                </nav>