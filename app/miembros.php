<?php
include 'config.php';

$stmt = $pdo->prepare("
    SELECT 
        m.id_member, 
        u.email, 
        u.nombres, 
        u.apellidos, 
        m.direccion, 
        m.phone, 
        m.ntarjeta, 
        m.fvencimiento, 
        m.cvv, 
        m.cobroautomatico, 
        m.assistance, 
        m.absences
    FROM 
        gym_members m
    INNER JOIN gym_users u ON m.id_user = u.id_user
    INNER JOIN gym_roles r ON u.id_role = r.id_role
    WHERE 
        r.role_name = 'User'
");
$stmt->execute();
$members = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include 'header.php'; ?>
<div class="container-fluid">
    <div class="content-header">
        <h1>Memebr List</h1>
        <input type="text" class="form-control" id="searchInput" placeholder="Buscar por nombre o email" style="width:20%;">
        <br>
    </div>
    <div class="content">
        <table class="table table-bordered" id="membersTable">
            <thead>
                <tr>
                    <th>Member ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Address</th>
                    <th>Phone</th>
                    <th>Automatic Payment</th>
                    <th>Attended</th>
                    <th>Not attended</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($members as $member): ?>
                    <tr>
                        <td><?php echo $member['id_member']; ?></td>
                        <td><?php echo $member['nombres'] . ' ' . $member['apellidos']; ?></td>
                        <td><?php echo $member['email']; ?></td>
                        <td><?php echo $member['direccion']; ?></td>
                        <td><?php echo $member['phone']; ?></td>
                        <td><?php echo $member['cobroautomatico'] ? 'Sí' : 'No'; ?></td>
                        <td><?php echo $member['assistance']; ?></td>
                        <td><?php echo $member['absences']; ?></td>
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
        const rows = document.querySelectorAll('#membersTable tbody tr');

        rows.forEach(row => {
            const name = row.cells[1].textContent.toLowerCase();
            const email = row.cells[2].textContent.toLowerCase();

            if (name.includes(searchTerm) || email.includes(searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
</script>
