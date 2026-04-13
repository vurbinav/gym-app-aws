<?php
include 'config.php';

$stmt = $pdo->prepare("SELECT * FROM gym_plan");
$stmt->execute();
$planes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include 'header.php'; ?>
<div class="container my-5">
    <h1 class="text-center mb-4">Select your plan to start training!</h1>
    <div class="row">
        <?php foreach ($planes as $plan): ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100 <?php echo $plan['plan_type'] === 'Premium' ? 'bg-dark text-white' : ''; ?>">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title text-center"><?php echo strtoupper($plan['plan_type']); ?></h5>
                        
                        <h3 class="card-text text-center">
                            $ <?php echo number_format($plan['price'], 2); ?> *
                        </h3>
                        <p class="text-center"><small>*From</small></p>
                        <ul class="list-group list-group-flush mb-3">
                            <li class="list-group-item <?php echo $plan['plan_type'] === 'Premium' ? 'bg-dark text-white' : ''; ?>">
                                Access to <?php echo $plan['amount']; ?> group classes a month.
                            </li>
                            <li class="list-group-item <?php echo $plan['plan_type'] === 'Premium' ? 'bg-dark text-white' : ''; ?>">
                                Exclusive benefits from <?php echo $plan['plan_type']; ?>.
                                <p class="card-text text-center">
                                    <?php echo $plan['description']; ?>
                                </p>
                            </li>
                        </ul>
                        <a href="selectplan.php?id=<?php echo $plan['id_plan']; ?>" class="btn btn-primary mt-auto">Select</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php include 'footer.php'; ?>
