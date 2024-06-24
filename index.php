<?php
session_start();

include ("config.php");
include ("functions.php");

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
</head>

<body>
    <?php include "header.php"; ?>

    <div id="carouselExampleAutoplaying" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="https://imgproduitnewvet.blob.core.windows.net/imagescarousel/img_carousel_1.jpg" class="d-block w-100" alt="pic1">
            </div>
            <div class="carousel-item">
                <img src="https://imgproduitnewvet.blob.core.windows.net/imagescarousel/img_carousel_2.jpg" class="d-block w-100" alt="pic2">
            </div>
            <div class="carousel-item">
                <img src="https://imgproduitnewvet.blob.core.windows.net/imagescarousel/img_carousel_3.jpg" class="d-block w-100" alt="pic3">
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
                <p class="lead fw-normal text-white-50 mb-0">C'est la fête à la maison!</p>
            </div>
        </div>
    </div>

    <section class="py-5">
        <div class="container px-4 px-lg-5 mt-5">
            <div class="row gx-4 gx-lg-5 row-cols-2 row-cols-md-3 row-cols-xl-4 justify-content-center">
                <?php
                $sqlrequest = "SELECT * FROM produits;";
                $result = $con->query($sqlrequest);

                while ($row = $result->fetch_assoc()) {
                    $productID = $row['id'];
                    $productName = $row['nom'];
                    $productPrice = $row['prix'];

                    $pathProductImg = PATH_PRODUCTS_IMAGES . $productID . ".webp";

                    echo
                        "<div class='col mb-5'>
                        <div class='card h-100'>
                            <img class='card-img-top height='768' width='768' src='$pathProductImg' alt='produit' />
                            <div class='card-body p-4'>
                                <div class='text-center'>
                                    <h5 class='fw-bolder'>$productName</h5>
                                    $25.00
                                </div>
                            </div>
                            <div class='card-footer p-4 pt-0 border-top-0 bg-transparent'>
                                <div class='text-center'><a class='btn btn-outline-dark mt-auto' href='#'>Ajouter au panier</a></div>
                            </div>
                        </div>
                    </div>";
                }
                ?>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  
    <script src="assets/js/uploadImagesAzure.js"></script>

    <script>
        // Fonction pour afficher ou masquer l'indicateur de chargement
        function toggleLoadingIndicator(show) {
            const indicator = document.getElementById('loadingIndicator');
            indicator.style.display = show ? 'block' : 'none';
        }
    </script>
</body>

</html>