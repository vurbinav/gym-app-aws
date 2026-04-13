<?php
include 'config.php';

$stmt = $pdo->prepare("
    SELECT 
        u.id_user, 
        u.email, 
        CONCAT(u.nombres, ' ', u.apellidos) AS nombre_completo,
        u.creation_date, 
        g.name AS gym_name, 
        r.role_name AS role_name
    FROM 
        gym_users u
    INNER JOIN gym g ON u.id_gym = g.id_gym
    INNER JOIN gym_roles r ON u.id_role = r.id_role
");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<?php include 'header.php'; ?>
<div class="container-fluid">
    <div class="content-header" style="padding-bottom:18px;">
        <h1>Users</h1>
        <a style="margin-bottom:5px;" href="adduser.php" class="btn btn-primary">Add User</a>
        <input type="text" class="form-control" id="searchInput" placeholder="Search by Email or Full Name" style="width:50%;">
        <br>
    </div>
    <div class="content">
        <table class="table table-bordered" id="usersTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Email</th>
                    <th>Full Name</th>
                    <th>Role</th>
                    <th>Gym</th>
                    <th>Creation Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo $user['id_user']; ?></td>
                        <td><?php echo $user['email']; ?></td>
                        <td><?php echo $user['nombre_completo']; ?></td>
                        <td><?php echo $user['role_name']; ?></td>
                        <td><?php echo $user['gym_name']; ?></td>
                        <td><?php echo $user['creation_date']; ?></td>
                        <td>
                            <a href="edituser.php?id=<?php echo $user['id_user']; ?>" class="btn btn-warning btn-sm">Edit</a>
                            <a href="deleteuser.php?id=<?php echo $user['id_user']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?');">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php include 'footer.php'; ?>

<script>
    document.getElementById('searchInput').addEventListener('keyup', function () {
        const searchTerm = this.value.toLowerCase();
        const rows = document.querySelectorAll('#usersTable tbody tr');

        rows.forEach(row => {
            const email = row.cells[1].textContent.toLowerCase();
            const nombreCompleto = row.cells[2].textContent.toLowerCase();

            if (email.includes(searchTerm) || nombreCompleto.includes(searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
</script>
