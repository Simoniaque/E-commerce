<?php
session_start();

include("config.php");
include("functions.php");

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

        <div class="container">
            <div class="row">

                <div class="col-5 border rounded mx-1">
                    <div class="my-2">
                        <p class="display-6 text-center text-black">Images carrousel</p>
                    </div>
                    <div class="form-group mb-2">
                        <label for="imgCarousel1">Image 1</label>
                        <input type="file" class="form-control" id="imgCarousel1" />
                    </div>
                    <button type="button" onclick="uploadCarouselImage('img_carousel_1.jpg')" class="btn btn-secondary"
                        id="btnAddCarousel1">Valider</button>


                    <div class="form-group my-2">
                        <label for="imgCarousel2">Image 2</label>
                        <input type="file" class="form-control" id="imgCarousel2" />

                    </div>
                    <button type="button" onclick="uploadCarouselImage('img_carousel_2.jpg')" class="btn btn-secondary"
                        id="btnAddCarousel2">Valider</button>

                    <div class="form-group mb-2">
                        <label for="imgCarousel3">Image 3</label>
                        <input type="file" class="form-control" id="imgCarousel3" />

                    </div>
                    <button type="button" onclick="uploadCarouselImage('img_carousel_3.jpg')" class="btn btn-secondary"
                        id="btnAddCarousel3">Valider</button>

                </div>



                <div class="col-5 pb-2 border rounded mx-1">
                    <div class="my-2">
                        <p class="display-6 text-center text-black">Ajouter produit</p>
                    </div>
                    <div class="form-group mb-2">
                        <label for="productName">Nom</label>
                        <input type="text" class="form-control" id="productName" />
                    </div>
                    <div class="form-group mb-2">
                        <label for="productImg">Image</label>
                        <input type="file" class="form-control" id="productImg" />
                    </div>
                    <div class="form-group mb-2">
                        <label for="productCategory">Catégorie</label>
                        <select class="form-control" id="productCategory">
                            <?php
                            $result = getCategoriesList($con);

                            while ($row = $result->fetch_assoc()) {
                                $categoryName = $row['nom'];

                                echo "<option>$categoryName</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group mb-2">
                        <label for="productPrice" class="form-label">Prix</label>
                        <div class="input-group">
                            <input type="number" id="productPrice" class="form-control" aria-label="Prix">
                            <span class="input-group-text">€</span>
                        </div>
                    </div>
                    <div class="form-group mb-2">
                        <label for="productStock" class="form-label">Stock</label>
                        <input type="number" id="productStock" class="form-control" aria-label="Stock">
                    </div>
                    <div class="form-group mb-2">
                        <label for="productDescription" class="form-label">Description</label>
                        <input type="textare" id="productDescription" class="form-control">
                    </div>

                    <button type="button" onclick="uploadProduct()" class="btn btn-secondary">Valider</button>

                </div>
            </div>
        </div>
    </main>



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
    <script src="assets/js/uploadImagesAzure.js"></script>
</body>

</html>