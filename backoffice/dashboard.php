<?php
// dashboard.php
include('config.php');

// Exemple : Afficher le nombre total de commandes
$result = $conn->query("SELECT COUNT(*) as total_orders FROM commandes");
$total_orders = $result->fetch_assoc()['total_orders'];

// Exemple : Afficher le chiffre d'affaires total
$result = $conn->query("SELECT SUM(prix_total) as total_revenue FROM commandes");
$total_revenue = $result->fetch_assoc()['total_revenue'];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <link rel="icon" href="../assets/img/logo-black.png" type="image/x-icon">
</head>
<body>
    <div class="container-fluid p-0">
        <div class="row g-0">
            <!-- Sidebar -->
            <div class="col-md-2 p-0">
                <?php include 'navbar.php'; ?>
            </div>

            <!-- Main content area -->
            <div class="col-md-10 p-0">
                <?php include 'header.php'; ?>

                <div class="container mt-4">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card shadow-sm border-0">
                                <div class="card-header bg-dark text-white">
                                    <h5 class="card-title">Statistiques des Commandes</h5>
                                </div>
                                <div class="card-body">
                                    <h4 class="card-text">Total des commandes :</h4>
                                    <p class="card-text"><strong><?php echo $total_orders; ?></strong></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card shadow-sm border-0">
                                <div class="card-header bg-dark text-white">
                                    <h5 class="card-title">Chiffre d'Affaires</h5>
                                </div>
                                <div class="card-body">
                                    <h4 class="card-text">Chiffre d'affaires total :</h4>
                                    <p class="card-text"><strong><?php echo number_format($total_revenue, 2, ',', ' '); ?> €</strong></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
