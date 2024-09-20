<?php
session_start();

include_once "../config.php";
include_once "../API/usersRequests.php";

$user = GetCurrentUser($pdo);

if($user === false){
    header('Location: ../index.php');
    exit;
}

if($user['est_admin'] == 0){
    header('Location: ../index.php');
    exit;
}

// Récupération de la période
$period = isset($_POST['period']) ? $_POST['period'] : 'daily';

// Fonction pour récupérer les ventes par jour ou par semaine
function getSalesData($pdo, $period) {
    $interval = $period === 'weekly' ? '5 WEEK' : '7 DAY';
    $groupBy = $period === 'weekly' ? 'WEEK(date_creation,3)' : 'DATE(date_creation)';
    
    $sql = "SELECT $groupBy as period, SUM(prix_total) as total_sales
            FROM commandes
            WHERE date_creation >= (CURDATE() - INTERVAL $interval)
            AND est_actif = 1
            GROUP BY period";
    
    $stmt = $pdo->prepare($sql);
    if ($stmt->execute()) {
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        echo "<script>console.error('Erreur lors de la récupération des ventes.');</script>";
        return [];
    }
}

// Fonction pour récupérer les paniers moyens par catégories
function getAverageBasketByCategory($pdo, $period) {
    $interval = $period === 'weekly' ? '5 WEEK' : '7 DAY';
    $groupBy = $period === 'weekly' ? 'WEEK(c.date_creation,3)' : 'DATE(c.date_creation)';
    
    $sql = "SELECT cat.nom as category, $groupBy as period, AVG(c.prix_total) as average_basket
            FROM commandes c
            JOIN details_commandes dc ON c.id = dc.commande_id
            JOIN produits p ON dc.produit_id = p.id
            JOIN categories cat ON p.categorie_id = cat.id
            WHERE c.date_creation >= (CURDATE() - INTERVAL $interval)
            AND c.est_actif = 1
            GROUP BY cat.nom, period";
    
    $stmt = $pdo->prepare($sql);
    if ($stmt->execute()) {
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        echo "<script>console.error('Erreur lors de la récupération des paniers moyens par catégorie.');</script>";
        return [];
    }
}

// Fonction pour récupérer le volume de ventes par catégorie
function getSalesByCategory($pdo, $period) {
    $interval = $period === 'weekly' ? '5 WEEK' : '7 DAY';
    
    $sql = "SELECT cat.nom as category, SUM(dc.quantite) as total_quantity
            FROM commandes c
            JOIN details_commandes dc ON c.id = dc.commande_id
            JOIN produits p ON dc.produit_id = p.id
            JOIN categories cat ON p.categorie_id = cat.id
            WHERE c.date_creation >= (CURDATE() - INTERVAL $interval)
            AND c.est_actif = 1
            GROUP BY cat.nom";
    
    $stmt = $pdo->prepare($sql);
    if ($stmt->execute()) {
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        echo "<script>console.error('Erreur lors de la récupération des ventes par catégorie.');</script>";
        return [];
    }
}

// Récupération des données pour les graphiques
$salesData = getSalesData($pdo, $period);
$averageBasketData = getAverageBasketByCategory($pdo, $period);
$salesByCategory = getSalesByCategory($pdo, $period);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="icon" href="../assets/img/logo-black.png" type="image/x-icon">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<div class="container-fluid p-0">
        <div class="row g-0">
            <div class="col-md-2 p-0">
                <?php include 'navbar.php'; ?>
            </div>

            <div class="col-md-10 p-0">
                <?php include 'header.php'; ?>

                <div class="container mt-4">
                    <form method="POST" class="mb-4">
                        <select name="period" onchange="this.form.submit()">
                            <option value="daily" <?php echo $period === 'daily' ? 'selected' : ''; ?>>7 derniers jours</option>
                            <option value="weekly" <?php echo $period === 'weekly' ? 'selected' : ''; ?>>5 dernières semaines</option>
                        </select>
                    </form>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card shadow-sm border-0 mb-4">
                                <div class="card-header bg-dark text-white">
                                    <h5 class="card-title">Ventes totales sur les 7 derniers jours</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="salesChart"></canvas>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card shadow-sm border-0 mb-4">
                                <div class="card-header bg-dark text-white">
                                    <h5 class="card-title">Paniers moyens par catégories</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="basketChart"></canvas>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card shadow-sm border-0 mb-4">
                                <div class="card-header bg-dark text-white">
                                    <h5 class="card-title">Volume de ventes par catégorie</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="categoryChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Données pour le graphique des ventes
        const salesData = <?php echo json_encode($salesData); ?>;
        const salesLabels = salesData.map(item => item.period);
        const salesTotals = salesData.map(item => item.total_sales);

        // Données pour le graphique des paniers moyens
        const basketData = <?php echo json_encode($averageBasketData); ?>;
        const basketLabels = basketData.map(item => item.category);
        const basketTotals = basketData.map(item => item.average_basket);

        // Données pour le graphique des ventes par catégorie
        const categoryData = <?php echo json_encode($salesByCategory); ?>;
        const categoryLabels = categoryData.map(item => item.category);
        const categoryTotals = categoryData.map(item => item.total_quantity);

        // Graphique des ventes
        new Chart(document.getElementById('salesChart'), {
            type: 'bar',
            data: {
                labels: salesLabels,
                datasets: [{
                    label: 'Total Ventes en €',
                    data: salesTotals,
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            }
        });

        // Graphique des paniers moyens
        new Chart(document.getElementById('basketChart'), {
            type: 'bar',
            data: {
                labels: basketLabels,
                datasets: [{
                    label: 'Panier Moyen en €',
                    data: basketTotals,
                    backgroundColor: 'rgba(153, 102, 255, 0.2)',
                    borderColor: 'rgba(153, 102, 255, 1)',
                    borderWidth: 1
                }]
            }
        });

        // Graphique des ventes par catégorie
        new Chart(document.getElementById('categoryChart'), {
            type: 'pie',
            data: {
                labels: categoryLabels,
                datasets: [{
                    label: 'Volume Ventes',
                    data: categoryTotals,
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(255, 206, 86, 0.2)',
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(153, 102, 255, 0.2)',
                        'rgba(255, 159, 64, 0.2)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)'
                    ],
                    borderWidth: 1
                }]
            }
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>