<?php
session_start();

include("config.php");
include("functions.php");

$userData = checkLogin($con);

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>New Vet</title>
    <link rel="icon" type="image/x-icon" href="assets/img/logo-black.png" />

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <link rel="stylesheet" href="assets/css/style.css" />
</head>

<body>
    <main>
        <?php include "header.php"; ?>


        <div id="carouselExampleAutoplaying" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img src="https://imgproduitnewvet.blob.core.windows.net/imagescarousel/img_carousel_1.jpg"
                        class="d-block w-100" alt="pic1">
                </div>
                <div class="carousel-item">
                    <img src="https://imgproduitnewvet.blob.core.windows.net/imagescarousel/img_carousel_2.jpg"
                        class="d-block w-100" alt="pic2">
                </div>
                <div class="carousel-item">
                    <img src="https://imgproduitnewvet.blob.core.windows.net/imagescarousel/img_carousel_3.jpg"
                        class="d-block w-100" alt="pic3">
                </div>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleAutoplaying"
                data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleAutoplaying"
                data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>

        <div class="bg-dark py-5">
            <div class="container px-4 px-lg-5 my-5">
                <div class="text-center text-white">
                    <h1 class="display-4 fw-bolder">New Vet</h1>
                    <p class="lead fw-normal text-white-50 mb-0">Révélez votre style, réinventez-vous !</p>
                </div>
            </div>
        </div>

        <br />
        <br />
        <h2 class="text-center text-decoration-underline">Les Highlanders du moment</h2>

        <section class="py-5">
            <div class="container mt-5">
                <div class="row gx-4 row-cols-2 row-cols-md-3 row-cols-lg-4 justify-content-center mt-5">
                    <?php
                    $highlightCategories = getHighlightCategories($con);
                    foreach ($highlightCategories as $category) {
                        $categoryID = $category['id'];
                        $categoryName = $category['nom'];
                        $pathCategoryImg = PATH_CATEGORY_IMAGES . $categoryID . ".png";
                        echo "<div class='col mb-5'>
                        <div class='card h-100 mx-2 border-0 shadow rounded-0'>
                            <div class='bg-image'>
                                <a href='categories.php?id=$categoryID'><img src='$pathCategoryImg' class='w-100' /></a>
                            </div>
                            <div class='card-body'>
                                <a href='categories.php?id=$categoryID' class='text-decoration-none text-black text-center'>
                                    <h4 class='card-title mb-3'>$categoryName</h4>
                                </a>
                            </div>
                        </div>
                    </div>";
                    }
                    ?>
                </div>


                <hr />
                <div class="row gx-4 row-cols-2 row-cols-md-3 row-cols-lg-4 justify-content-center mt-5">
                    <?php
                    $highlightProducts = getHighlightProducts($con);
                    foreach ($highlightProducts as $product) {
                        $productID = $product['id'];
                        $productName = $product['nom'];
                        $productPrice = $product['prix'];
                        $pathProductImg = PATH_PRODUCTS_IMAGES . $productID . ".webp";
                        echo "<div class='col mb-5'>
                        <div class='card h-100 mx-2 border-0 shadow'>
                            <div class='bg-image'>
                                <a href='product.php?id=$productID'><img src='$pathProductImg' class='w-100' /></a>
                            </div>
                            <div class='card-body'>
                                <a href='product.php?id=$productID' class='text-reset'>
                                    <h5 class='card-title mb-3'>$productName</h5>
                                </a>
                                <h6 class='mb-3'>$productPrice €</h6>
                                <a href='#' class='btn btn-dark btn-sm'>Ajouter au panier</a>
                            </div>
                        </div>
                    </div>";
                    }
                    ?>
                </div>
            </div>
        </section>
        <div class="push"></div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>

    <script src="assets/js/uploadImagesAzure.js"></script>

    <script>
        // Fonction pour afficher ou masquer l'indicateur de chargement
        function toggleLoadingIndicator(show) {
            const indicator = document.getElementById('loadingIndicator');
            indicator.style.display = show ? 'block' : 'none';
        }
    </script>

    <?php include "footer.php"; ?>
</body>

</html>