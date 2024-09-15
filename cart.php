<?php
session_start();
include_once "config.php";
include_once "functions.php";
include_once "API/usersRequests.php";
include_once "API/productsRequests.php";
include_once "API/cartsRequests.php";

$user = GetCurrentUser($pdo);
$isLoggedIn = $user != null;
$cartProducts = [];
$totalPrice = 0;

if ($isLoggedIn) {
    $getCart = GetCart($pdo, $user['id']);
    if ($getCart) {
        if(RemoveUnActiveProductsFromCart($pdo, $user['id']) === true){
            DisplayDismissibleWarning("Certains produits de votre panier ne sont plus disponibles et ont été retirés.");
        }

        if(AdaptQuantityInCart($pdo, $user['id']) === true){
            DisplayDismissibleWarning("La quantité de certains produits de votre panier a été ajustée selon nos stocks.");

        }
        $cartProducts = GetCartProducts($pdo, $getCart['id'], 0);
    }
} else {
    // Gestion du panier pour les utilisateurs non connectés
    
    if(RemoveUnActiveProductsFromCookieCart($pdo) === true){
        DisplayDismissibleWarning("Certains produits de votre panier ne sont plus disponibles et ont été retirés.");
    }
    if(AdaptQuantityInCookieCart($pdo) === true){
        DisplayDismissibleWarning("La quantité de certains produits de votre panier a été ajustée selon nos stocks.");

    }


    $cartCookieName = 'cart';
    $cart = isset($_COOKIE[$cartCookieName]) ? json_decode($_COOKIE[$cartCookieName], true) : [];

    if (!empty($cart)) {
        foreach ($cart as $productID => $quantity) {
            // Obtenez les détails du produit à partir de l'ID
            $product = GetProductById($pdo, $productID);
            if ($product) {
                $product['quantite'] = $quantity;
                $cartProducts[] = $product;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Panier</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="assets/css/style.css" />
</head>
<body>
    <main>
        <?php include "header.php"; ?>
        <div class="container h-100 py-5">
            <div class="row d-flex justify-content-center align-items-start h-100">
                <?php if (empty($cartProducts)) : ?>
                    <div class="col-lg-8 text-center">
                        <div class="alert alert-dark" role="alert">
                            Votre panier est vide. <a href="search.php" class="text-dark mt-2">Cliquez ici pour explorer notre catalogue</a>
                        </div>
                    </div>
                <?php else : ?>
                    <div class="col-lg-8 shadow">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th scope="col" class="h5">Panier</th>
                                        <th scope="col">Quantité</th>
                                        <th scope="col">Prix</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($cartProducts as $product) : ?>
                                        <?php
                                        $productID = $product['id'];
                                        $productName = $product['nom'];
                                        $pathProductImg = PATH_PRODUCTS_IMAGES . $productID . ".webp";
                                        $quantity = $product['quantite'];
                                        $maxStock = $product['stock'];
                                        $pricePerUnit = $product['prix'];
                                        $totalPriceForProduct = $pricePerUnit * $quantity;
                                        $totalPrice += $totalPriceForProduct;
                                        ?>
                                        <tr>
                                            <th scope="row">
                                                <div class="d-flex align-items-center">
                                                    <img src="<?php echo $pathProductImg; ?>" class="img-fluid rounded-3" style="width: 120px;" alt="<?php echo $productName; ?>">
                                                    <div class="flex-column ms-4">
                                                        <p class="mb-2"><?php echo $productName; ?></p>
                                                    </div>
                                                </div>
                                            </th>
                                            <td class="align-middle">
                                                <div class="d-flex flex-row">
                                                    <input min="0" max="<?php echo $maxStock; ?>" name="quantity" value="<?php echo $quantity; ?>" type="number" class="mouse-only-number-input form-control form-control-sm" style="width: 50px;" data-product-id="<?php echo $productID; ?>" data-price-per-unit="<?php echo $pricePerUnit; ?>" />
                                                </div>
                                            </td>
                                            <td class="align-middle">
                                                <p class="mb-0" id="<?php echo $productID; ?>" style="font-weight: 500;"><?php echo number_format($totalPriceForProduct, 2, ',', ' '); ?> €</p>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="col-lg-4 mt-sm-5 mt-md-0">
                        <div class="d-flex justify-content-between" style="font-weight: 500;">
                            <p class="mb-2">Sous-total</p>
                            <p class="mb-2 ca"><?php echo number_format($totalPrice, 2, ',', ' ') . ' €'; ?></p>
                        </div>

                        <div class="d-flex justify-content-between" style="font-weight: 500;">
                            <p class="mb-0">Livraison</p>
                            <p class="mb-0">Gratuite</p>
                        </div>

                        <hr class="my-4">

                        <div class="d-flex justify-content-between mb-4" style="font-weight: 500;">
                            <p class="mb-2">Total</p>
                            <p class="mb-2 ca"><?php echo number_format($totalPrice, 2, ',', ' ') . ' €'; ?></p>
                        </div>

                        <?php if ($isLoggedIn) : ?>
                            <a href="checkout.php" class="btn btn-dark btn-block btn-lg">Passer la commande</a>
                        <?php else : ?>
                            <a href="signup.php?redirect_to=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>" class="btn btn-dark btn-block btn-lg">Créer un compte</a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="push"></div>
    </main>

    <?php include "footer.php"; ?>

    <!-- Lien vers le fichier JS -->
    <script src="assets/js/cart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
