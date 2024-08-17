<?php
session_start();
include "config.php";
include "functions.php";

if (!isset($_GET['id'])) {
    show_404();
    exit();
}

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
    <script>
        //Empêcher les utilisateurs de saisir une valeur à la main (potentiellement négative)
        window.onload = () => {
            const mouseOnlyNumberInputField = document.getElementById("mouse-only-number-input");
            mouseOnlyNumberInputField.addEventListener("keypress", (event) => {
                event.preventDefault();
            });
        }
    </script>

    <link rel="stylesheet" href="assets/css/style.css" />
</head>

<body>

    <main>
        <?php include "header.php";

        $product = getProductById($con, $_GET['id']);
        if (!$product) {
            show_404();
            exit();
        }

        $productID = $product['id'];
        $name = $product['nom'];
        $price = $product['prix'];
        $description = $product['description'];
        $materials = $product['materiaux'];
        $stock = $product['stock'];

        $pathImage = PATH_PRODUCTS_IMAGES . $productID . '.webp';

        $addToCartButton = "<a href='#' class='btn btn-lg disabled'>Stock Epuisé</a>";

        if ($stock > 0) {
            $addToCartButton = "<a href='#' class='btn btn-dark shadow-0'>Ajouter au panier</a>";
        }


        echo "<div class='container pt-5'>
            <div class='row gx-5 '>
                <aside class='col-lg-6'>
                    <div class='rounded-4 mb-3 d-flex justify-content-center'>
                    <img style='max-width: 100%; max-height: 60vh; margin: auto;' class='rounded-4 fit' src='$pathImage' />
                    </div>
                </aside>
                <div class='col-lg-6'>
                    <div class='ps-lg-3'>
                        <h3 class='title text-dark'>$name</h3>
                        <h5>Catégorie</h5>

                        <div class='mb-3'>
                            <span class='h5'>$price €</span>
                        </div>

                        <p>
                            $description
                        </p>

                        <div class='row'>
                            <dt class='col-3'>Matériaux</dt>
                            <dd class='col-9'>$materials</dd>
                        </div>

                        <hr />

                        <div class='row mb-4'>
                            <div class='col-md-4 col-6 mb-3'>
                                <label class='mb-2 d-block'>Quantité</label>
                                <div class='input-group mb-3' style='width: 170px;'>
                                    <input type='number' class='form-control text-center border border-secondary'
                                        id='mouse-only-number-input' value='1' min='1' max='$stock' />
                                </div>
                            </div>
                        </div>
                        
                        $addToCartButton
                    </div>
                </div>
            </div>
        </div>";

        //Ajouter 6 produits aléatoire de la même catégorie
        $categoryID = $product['categorie_id'];

        $products = getProductsByCategory($con, $categoryID);

        $products = array_filter($products, function ($product) use ($productID) {
            return $product['id'] != $productID;
        });

        $nbRandomProducts = 6;
        if ($nbRandomProducts > count($products)) {
            $nbRandomProducts = count($products);

        }

        $randomProductKeys = array_rand($products, $nbRandomProducts);
        
        $productsToDisplay;
        //Attribue à productsToDisplay les produits à afficher (les produits selon les clés aléatoires)
        if(is_array($randomProductKeys)){
            foreach ($randomProductKeys as $productKey) {
                $productsToDisplay[] = $products[$productKey];
            }
        } else {
            $productsToDisplay[] = $products[$randomProductKeys];
        }
        ?>

        <section class="py-5">
            <?php
            if (is_null($randomProductKeys)) {
                exit();
            }
            ?>
            <h2 class="mx-5">Produits Similaires</h2>
            <hr />
            <div class="container px-4 px-lg-5 mt-5">
                <div class="row gx-4 gx-lg-5 row-cols-2 row-cols-md-3 row-cols-xl-4 justify-content-center">
                    <?php

                    foreach ($productsToDisplay as $product) {
                        $productID = $product['id'];
                        $productName = $product['nom'];
                        $productPrice = $product['prix'];

                        $pathProductImg = PATH_PRODUCTS_IMAGES . $productID . ".webp";

                        echo "<div class='col mb-5'>
                                <div class='card h-100'>
                                    <a href='product.php?id=$productID' style='text-decoration:none;' class='text-black'><img class='card-img-top' height='400' src='$pathProductImg' alt='produit' />
                                        <div class='card-body p-4'>
                                            <div class='text-center'>
                                                <h5 class='fw-bolder'>$productName</h5>
                                                    $productPrice €
                                            </div>
                                        </div>
                                    </a>
                                    <div class='card-footer p-4 pt-0 border-top-0 bg-transparent'>
                                        <div class='text-center'><a class='btn btn-dark mt-auto' href='#'>Ajouter au panier</a></div>
                                    </div>
                                </div>
                            </div>";
                    }
                    ?>
                </div>
            </div>
            <hr />
        </section>







        <div class='push'></div>
    </main>

    <?php include "footer.php"; ?>

</body>

</html>