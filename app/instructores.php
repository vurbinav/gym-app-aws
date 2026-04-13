<?php include 'config.php';
$records_per_page = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $records_per_page;

$total_stmt = $pdo->prepare("
    SELECT COUNT(*) AS total 
    FROM gym_users u
    INNER JOIN gym_roles r ON u.id_role = r.id_role
    WHERE r.role_name = 'Instructor'
");
$total_stmt->execute();
$total_records = $total_stmt->fetch()['total'];
$total_pages = ceil($total_records / $records_per_page);

$stmt = $pdo->prepare("
    SELECT 
        u.id_user, 
        u.email, 
        u.creation_date, 
        CONCAT(u.nombres, ' ', u.apellidos) AS nombre_completo, 
        g.name AS gym_name
    FROM 
        gym_users u
    INNER JOIN gym g ON u.id_gym = g.id_gym
    INNER JOIN gym_roles r ON u.id_role = r.id_role
    WHERE 
        r.role_name = 'Instructor'
    LIMIT $records_per_page OFFSET $offset
");

$stmt->execute();
$instructors = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<?php include 'header.php'; ?>

<div class="container-fluid">
    <div class="content-header" style="padding-bottom:10px">
        <h1>Instructores</h1>
    </div>
    <div class="content">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Email</th>
                    <th>Full Name</th>
                    <th>Gym</th>
                    <th>Creation Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($instructors as $instructor): ?>
                    <tr>
                        <td><?php echo $instructor['email']; ?></td>
                        <td><?php echo $instructor['nombre_completo']; ?></td>
                        <td><?php echo $instructor['gym_name']; ?></td>
                        <td><?php echo $instructor['creation_date']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <nav>
            <ul class="pagination">
                <li class="page-item <?php if ($page <= 1) echo 'disabled'; ?>">
                    <a class="page-link" href="?page=<?php echo $page - 1; ?>">Previous</a>
                </li>
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?php if ($i == $page) echo 'active'; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?php if ($page >= $total_pages) echo 'disabled'; ?>">
                    <a class="page-link" href="?page=<?php echo $page + 1; ?>">Next</a>
                </li>
            </ul>
        </nav>
    </div>
</div>

<?php include 'footer.php'; ?>