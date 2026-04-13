<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'config.php';

if ($_SESSION['role'] !== 'Instructor') {
    header('Location: index.php');
    exit();
}

$id_user = $_SESSION['user_id'];

$current_date = new DateTime();
$week_start = clone $current_date;
$week_start->modify('last monday');
$week_end = clone $week_start;
$week_end->modify('next sunday');

$week_label = "Schedule for the week (" . $week_start->format('m/d/Y') . ") to (" . $week_end->format('m/d/Y') . ")";

$stmt = $pdo->prepare("
    SELECT c.name, s.start_date, s.end_date
    FROM gym_classes c
    INNER JOIN gym_schedule s ON c.id_class = s.id_class
    WHERE c.id_user = ?
");
$stmt->execute([$id_user]);

$clases = $stmt->fetchAll(PDO::FETCH_ASSOC);

$schedule = [];
foreach ($clases as $clase) {
    $start_datetime = new DateTime($clase['start_date']);
    $end_datetime = new DateTime($clase['end_date']);
    $day_of_week = $start_datetime->format('l');
    $start_hour = $start_datetime->format('H:i');
    $end_hour = $end_datetime->format('H:i');

    if (!isset($schedule[$day_of_week])) {
        $schedule[$day_of_week] = [];
    }
    $schedule[$day_of_week][] = [
        'name' => $clase['name'],
        'start_hour' => $start_hour,
        'end_hour' => $end_hour,
        'color' => sprintf('#%06X', mt_rand(0, 0xFFFFFF))
    ];
}
?>

<?php include 'header.php'; ?>
<div class="container my-5">
    <h1 class="text-center">Class Schedule</h1>
    <h5 class="text-center text-muted mb-4"><?php echo $week_label; ?></h5>
    <div class="schedule">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Time</th>
                    <th>Sunday</th>
                    <th>Monday</th>
                    <th>Tuesday</th>
                    <th>Wednesday</th>
                    <th>Thursday</th>
                    <th>Friday</th>
                    <th>Saturday</th>
                </tr>
            </thead>
            <tbody>
                <?php for ($hour = 7; $hour <= 22; $hour++): ?>
                    <tr>
                        <td><?php echo sprintf('%02d:00', $hour); ?> - <?php echo sprintf('%02d:00', $hour + 1); ?></td>
                        <?php foreach (['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'] as $day): ?>
                            <td>
                                <?php if (isset($schedule[$day])): ?>
                                    <?php foreach ($schedule[$day] as $class): ?>
                                        <?php
                                        $class_start = (int)explode(':', $class['start_hour'])[0];
                                        $class_end = (int)explode(':', $class['end_hour'])[0];
                                        ?>
                                        <?php if ($hour >= $class_start && $hour < $class_end): ?>
                                            <div style="background-color: <?php echo $class['color']; ?>; color: #000; padding: 5px; border-radius: 4px;">
                                                <?php echo htmlspecialchars($class['name']); ?>
                                            </div>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </td>
                        <?php endforeach; ?>
                    </tr>
                <?php endfor; ?>
            </tbody>
        </table>
    </div>
</div>
<?php include 'footer.php'; ?>
