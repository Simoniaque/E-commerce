<?php
session_start();
include_once "config.php";
include_once "functions.php";
include_once "API/productRequests.php";
include_once "API/categoriesRequests.php";


if (!isset($_GET['id'])) {
    show_404();
    exit();
}

$product = GetProductByID($pdo, $_GET['id']);
if ($product == false) {
    show_404();
    exit();
}

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Produit</title>
    <link rel="icon" type="image/x-icon" href="assets/img/logo-black.png" />

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <script src="assets/js/addToCart.js"></script>

    <script>
        //Empêcher les utilisateurs de saisir une valeur à la main (potentiellement négative)
        window.onload = () => {
            const mouseOnlyNumberInputField = document.getElementById("mouse-only-number-input");
            mouseOnlyNumberInputField.addEventListener("keypress", (event) => {
                event.preventDefault();
            });
        }
    </script>

    <script>
        function addProductToCart(productID) {
            const quantity = document.getElementById("mouse-only-number-input").value;
            addToCart(productID, quantity);
        }
    </script>

    <link rel="stylesheet" href="assets/css/style.css" />
</head>

<body>

    <main>
        <?php include "header.php";

        $productID = $product['id'];
        $name = $product['nom'];
        $price = $product['prix'];
        $description = $product['description'];
        $categoryID = $product['categorie_id'];
        $categoryName = "";
        $category = GetCategoryByID($pdo, $categoryID);
        if($category){
            $categoryName = $category["nom"];	
        }
        $materials = GetProductMaterials($pdo, $productID);
        $materialsString = "";
        if($materials){
            $materialNames = array_column($materials, 'nom');
            $materialsString = implode(" - ", $materialNames );
        }
        $stock = $product['stock'];

        $imagePaths = [
            PATH_PRODUCTS_IMAGES . $productID . '.webp',
            PATH_PRODUCTS_IMAGES . $productID . '_2.webp',
            PATH_PRODUCTS_IMAGES . $productID . '_3.webp',
        ];

        $addToCartButton = "<a href='#' class='btn btn-lg disabled'>Stock Épuisé</a>";

        if ($stock > 0) {
            $addToCartButton = "<a href='' class='btn btn-dark shadow-0' onclick=\"addProductToCart('$productID')\">Ajouter au panier</a>";
        }

        echo "
<div class='container pt-5'>
    <div class='row gx-5'>
        <aside class='col-lg-6'>
            <div id='productCarousel' class='carousel slide' data-bs-ride='carousel' style='height: 50vh;'>
                <div class='carousel-inner' style='height: 100%;'>
                ";
                foreach ($imagePaths as $index => $imagePath) {
                $activeClass = $index === 0 ? 'active' : '';
                echo "
                <div class='carousel-item $activeClass' style='height: 100%;'>
                    <img src='$imagePath' class='d-block w-100 rounded-4' style='height: 100%; object-fit: contain;' alt='Product Image'>
                </div>
                ";
                }
                echo "      
                </div>
                <button class='carousel-control-prev' type='button' data-bs-target='#productCarousel' data-bs-slide='prev'>
                    <span class='carousel-control-prev-icon' aria-hidden='true'></span>
                    <span class='visually-hidden'>Previous</span>
                </button>
                <button class='carousel-control-next' type='button' data-bs-target='#productCarousel' data-bs-slide='next'>
                    <span class='carousel-control-next-icon' aria-hidden='true'></span>
                    <span class='visually-hidden'>Next</span>
                </button>
            </div>
        </aside>
        <div class='col-lg-6'>
            <div class='ps-lg-3'>
                <h3 class='title text-dark'>$name</h3>
                <h5>$categoryName</h5>
                <div class='mb-3'>
                    <span class='h5'>$price €</span>
                </div>
                <p>$description</p>
                <div class='row'>
                    <dt class='col-3'>Matériaux</dt>
                    <dd class='col-9'>$materialsString</dd>
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

        $products = GetProductsByCategory($pdo, $categoryID);
        if($products == false){
            echo "
            <hr/><div class='push'></div>
            </main>";
            include 'footer.php';
            exit();

        }

        $products = array_filter($products, function ($product) use ($productID) {
            return $product['id'] != $productID;
        });

        if (!$products) {
            echo "<div class='push'></div>
            </main>";
            include 'footer.php';
            exit();
        }

        $nbRandomProducts = 6;
        if ($nbRandomProducts > count($products)) {
            $nbRandomProducts = count($products);
        }

        $randomProductKeys = array_rand($products, $nbRandomProducts);

        $productsToDisplay;
        //Attribue à productsToDisplay les produits à afficher (les produits selon les clés aléatoires)
        if (is_array($randomProductKeys)) {
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

                echo "<div class='push'></div>
            </main>
            <?php include 'footer.php'; ?>";

                exit();
            }
            ?>
            <h2 class="mx-5">Produits Similaires</h2>
            <hr />
            <div class="container mt-5">
                <div class="row gx-4 row-cols-2 row-cols-md-3 row-cols-lg-4 justify-content-center mt-5">
                    <?php
                    foreach ($productsToDisplay as $product) {
                        $productID = $product['id'];
                        $productName = $product['nom'];
                        $productPrice = $product['prix'];
                        $pathProductImg = PATH_PRODUCTS_IMAGES . $productID . ".webp";

                        $addToCartTag = "<button class='btn btn-dark btn-sm' onclick=\"addToCart('$productID', 1)\">Ajouter au panier</button>";
                        if ($product['stock'] <= 0) {
                            $addToCartTag = "<button class='btn btn-dark btn-sm' disabled>Stock épuisé</button>";
                        }
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
                                $addToCartTag
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>