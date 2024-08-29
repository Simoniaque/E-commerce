<?php
session_start();
// Inclure les fonctions de la base de données
include "config.php";
include 'functions.php';
include 'searchfunctions.php';

// Initialiser les variables de filtrage
$searchText = '';
$minPrice = 0;
$maxPrice = 500;
$inStock = false;
$sortingMethod = isset($_GET['sortingMethod']) ? (int)$_GET['sortingMethod'] : 4; // Récupérer la méthode de tri

// Traitement des paramètres GET pour le filtrage
$searchText = isset($_GET['search']) ? $_GET['search'] : '';
$minPrice = isset($_GET['min_price']) ? (float)$_GET['min_price'] : 0;
$maxPrice = isset($_GET['max_price']) ? (float)$_GET['max_price'] : 500;
$inStock = isset($_GET['in_stock']) ? true : false;

// Récupérer les produits selon les critères
$products = searchProducts($con, $searchText, $minPrice, $maxPrice, $inStock);

// Trier les produits en fonction de la méthode de tri, sauf pour la pertinence
if ($sortingMethod !== 4) {
    switch ($sortingMethod) {
        case 0:
            // Les plus récents
            usort($products, function($a, $b) {
                return strtotime($b['date_ajout']) <=> strtotime($a['date_ajout']);
            });
            break;
        case 1:
            // Les plus anciens
            usort($products, function($a, $b) {
                return strtotime($a['date_ajout']) <=> strtotime($b['date_ajout']);
            });
            break;
        case 2:
            // Prix Croissant
            usort($products, function($a, $b) {
                return $a['prix'] <=> $b['prix'];
            });
            break;
        case 3:
            // Prix Décroissant
            usort($products, function($a, $b) {
                return $b['prix'] <=> $a['prix'];
            });
            break;
    }
}

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>New Vet</title>
    <link rel="icon" type="image/x-icon" href="assets/img/logo-black.png" />
    <link rel="stylesheet" href="./assets/css/allproducts.css">
    <link rel="stylesheet" href="./assets/css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="assets/js/addToCart.js"></script>
</head>

<body>
    <?php include "header.php"; ?>

    <main>
        <div class="container-fluid mt-4">
            <div class="row">
                <!-- Formulaire de filtrage -->
                <aside class="col-sm-12 col-md-5 col-lg-3 col-xl-2">
                    <form method="get" action="">
                        <div class="card">
                            <article class="filter-group">
                                <header class="card-header">
                                    <h6 class="title">Recherche</h6>
                                </header>
                                <div class="card-body">
                                    <input type="text" class="form-control" id="search" name="search" value="<?= $searchText?>">
                                </div>
                            </article>

                            <article class="filter-group">
                                <header class="card-header">
                                    <h6 class="title">Ordre de tri</h6>
                                </header>
                                <div class="filter-content show">
                                    <div class="card-body">
                                        <div>
                                            <input type="radio" id="4" name="sortingMethod" value="4" <?= $sortingMethod == 4 ? 'checked' : '' ?> />
                                            <label for="4">Pertinence</label>
                                        </div>
                                        <div>
                                            <input type="radio" id="0" name="sortingMethod" value="0" <?= $sortingMethod == 0 ? 'checked' : '' ?> />
                                            <label for="0">Les plus récents</label>
                                        </div>
                                        <div>
                                            <input type="radio" id="1" name="sortingMethod" value="1" <?= $sortingMethod == 1 ? 'checked' : '' ?> />
                                            <label for="1">Les plus anciens</label>
                                        </div>
                                        <div>
                                            <input type="radio" id="2" name="sortingMethod" value="2" <?= $sortingMethod == 2 ? 'checked' : '' ?> />
                                            <label for="2">Prix Croissant</label>
                                        </div>
                                        <div>
                                            <input type="radio" id="3" name="sortingMethod" value="3" <?= $sortingMethod == 3 ? 'checked' : '' ?> />
                                            <label for="3">Prix Décroissant</label>
                                        </div>
                                    </div>
                                </div>
                            </article>

                            <article class="filter-group">
                                <header class="card-header">
                                    <h6 class="title">Prix</h6>
                                </header>
                                <div class="filter-content">
                                    <div class="card-body">
                                        <div class="form-row">
                                            <div class="form-group col-md-6">
                                                <label for="min_price">Prix min</label>
                                                <input type="number" class="form-control" id="min_price" name="min_price" step="0.01" value="<?= $minPrice?>"><br>
                                            </div>
                                            <div class='form-group text-right col-md-6'>
                                                <label for="max_price">Prix max</label>
                                                <input type="number" class='form-control' id="max_price" name="max_price" step="0.01" value="<?= $maxPrice?>"><br>
                                            </div>
                                        </div>
                                        <label>
                                            <input type="checkbox" name="in_stock" <?= $inStock ? 'checked' : '' ?>>
                                            Uniquement produits en stock
                                        </label>
                                        <button class="btn btn-block btn-dark" type="submit">Appliquer</button>
                                    </div>
                                </div>
                            </article>
                        </div>
                    </form>
                </aside>

                <!-- Cartes des produits -->
                <div class="col-sm-12 col-md-7 col-lg-9 col-xl-10">
                    <div class="row">
                        <?php
                        foreach ($products as $product) {
                            $productID = $product["id"];
                            $productName = $product["nom"];
                            $productPrice = $product["prix"];
                            $pathProductImg = PATH_PRODUCTS_IMAGES . $productID . ".webp";
                            $addToCartTag = "<button class='btn btn-dark btn-sm' onclick=\"addToCart('$productID', 1)\">Ajouter au panier</button>";
                            if ($product['stock'] <= 0) {
                                $addToCartTag = "<button class='btn btn-dark btn-sm' disabled>Stock épuisé</button>";
                            }
                            echo "<div class='col-sm-12 col-md-6 col-lg-4 col-xl-3 mb-4 rounded-0'>
                                    <div class='card h-100 border-0 shadow rounded-0'>
                                        <div class='bg-image'>
                                            <a href='product.php?id=$productID'><img src='$pathProductImg' class='card-img-top' alt='$productName' /></a>
                                        </div>
                                        <div class='card-body'>
                                            <a href='product.php?id=$productID' class='text-reset'>
                                                <h5 class='card-title mb-3'>$productName</h5>
                                            </a>
                                            <h6 class='mb-3'>$productPrice €</h6>
                                            $addToCartTag
                                        </div>
                                    </div>
                                </div>";
                        }
                        ?>
                    </div>
                </div>
            </div>

        </div>

        <div class="push"></div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    <?php include "footer.php"; ?>
</body>

</html>

