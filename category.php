<?php

session_start();

include_once "config.php";
include_once "functions.php";
include_once "API/categoriesRequests.php";
include_once "API/productsRequests.php";

if (!isset($_GET['id'])) {
    
    show_404();
    exit;
}

$categoryID = $_GET['id'];
$category = GetCategoryByID($pdo, $categoryID);

if (!$category) {
    show_404();
    exit;
}

$categoryName = $category['nom'];
$products = array();
$products = GetProductsByCategory($pdo, $categoryID);

//check if $products is empty
if ($products) {

    usort($products, function ($a, $b) {
        if ($a['en_priorite'] == 1 && $b['en_priorite'] != 1) {
            return -1;
        } elseif ($a['en_priorite'] != 1 && $b['en_priorite'] == 1) {
            return 1;
        } else {
            return 0;
        }
    });

    usort($products, function ($a, $b) {
        if ($a['stock'] <= 0 && $b['stock'] > 0) {
            return 1;
        } elseif ($a['stock'] > 0 && $b['stock'] <= 0) {
            return -1;
        } else {
            return 0;
        }
    });
}



?>

<!DOCTYPE html>

<head>
    <html lang="fr">
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <?php echo "<title>$categoryName</title>" ?>
    <link rel="icon" type="image/x-icon" href="assets/img/logo-black.png" />

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <link rel="stylesheet" href="assets/css/style.css" />
    <link rel="stylesheet" href="assets/css/category.css" />

    <script src="assets/js/addToCart.js"></script>
</head>

<body>

    <main>
        <?php include "header.php"; ?>

        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="image-container shadow">
                        <?php 
                        $imgPath = PATH_CATEGORY_IMAGES . $categoryID . '.png';
                        echo "
                        <img src='$imgPath' class='img-fluid' />
                        <h1 class='overlay-text'>$categoryName</h1>";
                        ?>
                    </div>
                    <p class="description"><?php echo $category['description']; ?></p>
                </div>
            </div>

            <div class="col-sm-12 col-md-8 col-lg-9 col-xl-10 row">
                <div class="row gx-4 row-cols-2 row-cols-md-3 row-cols-lg-4 mt-5">
                    <?php
                    if($products){
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
                    }else{
                        echo "<script>console.log('Aucun Produit Trouvé')</script>";
                    }
                    ?>


                </div>
            </div>
        </div>

        <div class="push"></div>

    </main>

    <?php include "footer.php"; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>