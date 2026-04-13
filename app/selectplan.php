<?php
include 'config.php';

if (!isset($_GET['id'])) {
    header('Location: planes.php');
    exit();
}

$id_plan = $_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM gym_plan WHERE id_plan = ?");
$stmt->execute([$id_plan]);
$plan = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$plan) {
    header('Location: planes.php');
    exit();
}
?>

<?php include 'header.php'; ?>
<div class="container my-5">
    <h1 class="text-center">Register for plan: <?php echo htmlspecialchars($plan['plan_type']); ?></h1>
    <form method="POST" action="registermember.php">
        <input type="hidden" name="membership_plan" value="<?php echo $plan['id_plan']; ?>">

        <div class="form-group">
            <label for="direccion">Address:</label>
            <input type="text" name="direccion" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="phone">Phone:</label>
            <input type="text" name="phone" class="form-control" maxlength="10" required>
        </div>

        <div class="form-group">
            <label for="ntarjeta">Card Number:</label>
            <input type="text" name="ntarjeta" class="form-control" maxlength="16" required>
        </div>

        <div class="form-group">
            <label for="fvencimiento">Expiration Date (MM/YY):</label>
            <input type="text" name="fvencimiento" class="form-control" maxlength="5" required>
        </div>

        <div class="form-group">
            <label for="cvv">CVV:</label>
            <input type="text" name="cvv" class="form-control" maxlength="3" required>
        </div>

        <div class="form-group">
            <label for="cobroautomatico">Automatic Payment:</label>
            <select name="cobroautomatico" class="form-control" required>
                <option value="1">Yes</option>
                <option value="0">No</option>
            </select>
        </div>

        <button type="submit" class="btn btn-success">Register</button>
    </form>
</div>
<?php include 'footer.php'; ?>
