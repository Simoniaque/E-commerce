<?php
session_start();

include "config.php";
include "functions.php";

if (!isset($_GET['id'])) {
    $products = getAllProducts($con);
} else {
    $idsCategories = $_GET['id'];
    $products = getProductsByCategories($con, $idsCategories);
}

if (isset($_GET['order'])) {
    $order = $_GET['order'];
    switch ($order) {
        case 0:
            $products = orderProductsByDate($products);
            break;
        case 1:
            $products = orderProductsByPrice($products);
            break;
        case 2:
            $products = orderProductsByDate($products);
            $products = array_reverse($products);
            break;
    }
} else {
    $products = orderProductsByDate($products);
}


$minPrice = "";
if (isset($_GET['minPrice'])) {
    $minPrice = $_GET['minPrice'];
    $products = filterProductsByMinPrice($products, $minPrice);
}


$maxPrice = "";
if (isset($_GET['maxPrice'])) {
    $maxPrice = $_GET['maxPrice'];
    $products = filterProductsByMaxPrice($products, $maxPrice);
}


?>

<!--Todo : Récuperer les infos depuis la bdd et créer la page dynamiquement-->
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

    <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">

    <script src="assets/js/addToCart.js"></script>

    <script>
        function AppliquerFiltres() {
            var url = "allproducts.php?";
            var checkboxes = document.querySelectorAll('input[name="selectedCategories"]');
            var checkboxArray = Array.from(checkboxes);

            var selectedCategories = checkboxArray
                .filter(checkbox => checkbox.checked);

            selectedCategories.forEach(element => {
                url = url + "id[]=" + element.getAttribute("categoryID") + "&";
            });

            var selectedOrder = document.querySelector('input[name="sortingMethod"]:checked');
            url = url + "order=" + selectedOrder.id + "&";

            var minPrice = document.getElementById("minPrice").value;
            var maxPrice = document.getElementById("maxPrice").value;

            if (minPrice != "") {
                url = url + "minPrice=" + minPrice + "&";
            }

            if (maxPrice != "") {
                url = url + "maxPrice=" + maxPrice;
            }

            window.location.assign(url);
        }
    </script>
</head>

<body>

    <?php include "header.php"; ?>

    <main>
        <div class="row mx-1 mt-4">


            <aside class="col-sm-12 col-md-4 col-lg-3 col-xl-2">
                <div class="card">
                    <article class="filter-group">
                        <header class="card-header">
                            <h6 class="title">Ordre de tri</h6>
                        </header>
                        <div class="filter-content collapse show" id="collapse_1">
                            <div class="card-body">
                                <div>
                                    <input type="radio" id="0" name="sortingMethod" checked="on" />
                                    <label>Les plus récents</label>
                                </div>
                                <div>
                                    <input type="radio" id="1" name="sortingMethod" />
                                    <label>Prix Croissant</label>
                                </div>
                                <div>
                                    <input type="radio" id="2" name="sortingMethod" />
                                    <label>Prix Décroissant</label>
                                </div>
                            </div>
                        </div>
                    </article>

                    <article class="filter-group">
                        <header class="card-header">
                            <h6 class="title">Catégories</h6>
                        </header>
                        <div class="filter-content collapse show" id="collapse_2">
                            <div class="card-body">
                                <?php
                                $catergories = getCategoriesList($con);

                                foreach ($catergories as $category) {
                                    $categoryID = $category['id'];
                                    $categoryName = $category['nom'];
                                    $checked = "";

                                    if (isset($_GET['id'])) {
                                        if (in_array($category['id'], $idsCategories)) {
                                            $checked = "checked";
                                        }
                                    }

                                    echo "<label class='custom-control custom-checkbox'>
                                    <input type='checkbox'class='custom-control-input' $checked name='selectedCategories' categoryID='$categoryID'>
                                    <div class='custom-control-label'>$categoryName
                                    </div>
                                </label>";
                                }

                                ?>
                            </div>
                        </div>
                    </article>

                    <article class="filter-group">
                        <header class="card-header">
                            <h6 class="title">Prix</h6>
                        </header>
                        <div class="filter-content collapse show" id="collapse_3">
                            <div class="card-body">
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label>Min</label>
                                        <?php
                                        echo "
                                        <input class='form-control' placeholder='€' type='number' id='minPrice' value='$minPrice'>
                                    </div>
                                    <div class='form-group text-right col-md-6'>
                                        <label>Max</label>
                                        <input class='form-control' placeholder='€' type='number' id='maxPrice' value='$maxPrice'>"; ?>
                                    </div>
                                </div>
                                <button class="btn btn-block btn-dark" onclick="AppliquerFiltres()">Appliquer</button>
                            </div>
                        </div>
                    </article>
            </aside>


            <div class="col-sm-12 col-md-8 col-lg-9 col-xl-10 row">
                <div class="row gx-4 row-cols-2 row-cols-md-3 row-cols-lg-4 mt-5">
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
                        echo "<div class='col-2 mb-5'>
                            <div class='card h-100 mx-2 border-0 shadow'>
                                <div class='bg-image'>
                                    <a href='product.php?id=$productID'><img src='$pathProductImg' class='w-100' /></a>
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

        <div class="push"></div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    <?php include "footer.php"; ?>

</body>

</html>