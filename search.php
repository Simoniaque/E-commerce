<?php
session_start();
// Inclure les fonctions de la base de données
include "config.php";
include 'functions.php';

// Initialiser les variables de filtrage
$searchText = '';
$minPrice = 0;
$maxPrice = 500;
$inStock = false;

// Traitement des paramètres GET pour le filtrage
$searchText = isset($_GET['search']) ? $_GET['search'] : '';
$minPrice = isset($_GET['min_price']) ? (float)$_GET['min_price'] : 0;
$maxPrice = isset($_GET['max_price']) ? (float)$_GET['max_price'] : PHP_INT_MAX;
$inStock = isset($_GET['in_stock']) ? true : false;

// Récupérer les produits selon les critères
$products = searchProducts($con, $searchText, $minPrice, $maxPrice, $inStock);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Recherche de Produits</title>
</head>
<body>
    <h1>Recherche de Produits</h1>
    <form method="get" action="">
        <label for="search">Recherche :</label>
        <input type="text" id="search" name="search" value="<?= htmlspecialchars($searchText) ?>"><br>
        
        <label for="min_price">Prix min :</label>
        <input type="number" id="min_price" name="min_price" step="0.01" value="<?= htmlspecialchars($minPrice) ?>"><br>
        
        <label for="max_price">Prix max :</label>
        <input type="number" id="max_price" name="max_price" step="0.01" value="<?= htmlspecialchars($maxPrice) ?>"><br>

        <label>
            <input type="checkbox" name="in_stock" <?= $inStock ? 'checked' : '' ?>>
            Uniquement produits en stock
        </label><br>

        <button type="submit">Rechercher</button>
    </form>

    <?php if ($products): ?>
        <h2>Résultats de la recherche :</h2>
        <ul>
            <?php foreach ($products as $product): ?>
                <li>
                    <strong><?= htmlspecialchars($product['nom']) ?></strong><br>
                    Prix : <?= htmlspecialchars($product['prix']) ?> EUR<br>
                    Description : <?= htmlspecialchars($product['description']) ?><br>
                    Stock : <?= htmlspecialchars($product['stock']) ?><br>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>Aucun produit trouvé.</p>
    <?php endif; ?>
</body>
</html>
