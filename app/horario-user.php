<?php
session_start();
include 'config.php';

if ($_SESSION['role'] !== 'User') {
    header('Location: index.php');
    exit();
}

$user_id = $_SESSION['user_id'];

$today = new DateTime();
$start_of_week = clone $today;
$start_of_week->modify(('Monday' === $today->format('l')) ? 'this monday' : 'last monday');
$end_of_week = clone $start_of_week;
$end_of_week->modify('+6 days');

$stmt = $pdo->prepare("
    SELECT c.name, s.start_date, s.end_date, c.img
    FROM gym_classes c
    INNER JOIN gym_schedule s ON c.id_class = s.id_class
    INNER JOIN gym_member_classes mc ON c.id_class = mc.id_class
    WHERE mc.id_user = ? AND mc.enrolled = 1
      AND s.start_date >= ? AND s.end_date <= ?
");
$stmt->execute([$user_id, $start_of_week->format('Y-m-d'), $end_of_week->format('Y-m-d')]);
$clases = $stmt->fetchAll(PDO::FETCH_ASSOC);

$horario = [];
for ($hour = 7; $hour <= 22; $hour++) {
    $horario[$hour] = array_fill(0, 7, null);
}

$days_of_week = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
foreach ($clases as $clase) {
    $start_time = new DateTime($clase['start_date']);
    $end_time = new DateTime($clase['end_date']);
    $day_of_week = $start_time->format('w');
    $start_hour = (int) $start_time->format('H');
    $end_hour = (int) $end_time->format('H');

    for ($hour = $start_hour; $hour < $end_hour; $hour++) {
        $horario[$hour][$day_of_week] = $clase['name'];
    }
}
?>

<?php include 'header.php'; ?>
<div class="container my-5">
    <h1 class="text-center">Schedule</h1>
    <p class="text-center">
        Schedule from week 
        <strong><?php echo $start_of_week->format('m/d/Y'); ?></strong> to 
        <strong><?php echo $end_of_week->format('m/d/Y'); ?></strong>
    </p>
    <div class="table-responsive">
        <table class="table table-bordered text-center">
            <thead>
                <tr>
                    <th>Time</th>
                    <?php foreach ($days_of_week as $day): ?>
                        <th><?php echo $day; ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php for ($hour = 7; $hour <= 22; $hour++): ?>
                    <tr>
                        <td><?php echo sprintf('%02d:00', $hour); ?></td>
                        <?php for ($day = 0; $day < 7; $day++): ?>
                            <?php if (isset($horario[$hour][$day])): ?>
                                <td class="bg-info text-dark">
                                    <?php echo htmlspecialchars($horario[$hour][$day]); ?>
                                </td>
                            <?php else: ?>
                                <td></td>
                            <?php endif; ?>
                        <?php endfor; ?>
                    </tr>
                <?php endfor; ?>
            </tbody>
        </table>
    </div>
</div>
<?php include 'footer.php'; ?>
