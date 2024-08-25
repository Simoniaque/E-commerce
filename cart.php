<?php
session_start();
include("config.php");
include("functions.php");

$userData = checkLogin($con);
$isLoggedIn = $userData != null;
$cartProducts = [];
$totalPrice = 0;

if ($isLoggedIn) {
    $getCart = getCart($con, $userData['id']);
    if ($getCart) {
        $cartProducts = getCartProducts($con, $getCart['id']);
    }
} else {
    // Gestion du panier pour les utilisateurs non connectés
    $cartCookieName = 'cart';
    $cart = isset($_COOKIE[$cartCookieName]) ? json_decode($_COOKIE[$cartCookieName], true) : [];

    if (!empty($cart)) {
        foreach ($cart as $productID => $quantity) {
            // Obtenez les détails du produit à partir de l'ID
            $product = getProductById($con, $productID);
            if ($product) {
                $product['quantite'] = $quantity;
                $cartProducts[] = $product;
            }
        }
    }
}

if (!empty($cartProducts)) {
    foreach ($cartProducts as $product) {
        $productID = $product['id'];
        $quantity = $product['quantite'];
        $pricePerUnit = $product['prix'];
        $totalPriceForProduct = $pricePerUnit * $quantity;
        $totalPrice += $totalPriceForProduct;
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="assets/css/style.css" />
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const inputs = document.querySelectorAll('input[name="quantity"]');

            inputs.forEach(input => {
                input.addEventListener('change', function() {
                    const quantity = parseInt(this.value, 10);
                    const productID = this.dataset.productId;
                    const pricePerUnit = parseFloat(this.dataset.pricePerUnit);
                    const maxStock = parseInt(this.max, 10);

                    if (isNaN(quantity) || quantity < 0 || quantity > maxStock) {
                        this.value = maxStock; // Réinitialiser à maxStock si invalide
                        return;
                    }

                    // Calculer le prix total pour le produit
                    const totalPriceForProduct = quantity * pricePerUnit;

                    // Mettre à jour le prix dans le tableau
                    const priceElement = document.querySelector(`p[id="${productID}"]`);
                    if (priceElement) {
                        priceElement.textContent = totalPriceForProduct.toFixed(2) + ' €';
                    } else {
                        console.error(`Element de prix pour productID ${productID} non trouvé.`);
                    }

                    // Supprimer la ligne du produit si la quantité est 0
                    if (quantity === 0) {
                        const row = this.closest('tr');
                        if (row) {
                            row.remove();
                            // Mettre à jour les totaux après la suppression de la ligne
                            updateTotals();
                            // Envoyer une requête AJAX pour supprimer le produit du panier
                            const xhr = new XMLHttpRequest();
                            xhr.open('POST', 'cart_manager.php', true);
                            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                            xhr.send('productID=' + encodeURIComponent(productID) + '&quantity=0&action=update');
                            if (document.querySelector('tbody').children.length === 0) {
                                document.getElementById('checkoutButton').disabled = true;

                            }
                        }
                    } else {
                        // Mettre à jour les totaux si la quantité n'est pas 0
                        updateTotals();

                        // Envoyer une requête AJAX pour mettre à jour la quantité du panier
                        const xhr = new XMLHttpRequest();
                        xhr.open('POST', 'cart_manager.php', true);
                        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                        xhr.onreadystatechange = function() {
                            if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
                                // Optionnellement, vous pouvez gérer la réponse du serveur ici
                            }
                        };
                        xhr.send('productID=' + encodeURIComponent(productID) + '&quantity=' + encodeURIComponent(quantity) + '&action=update');
                    }
                });
            });

            function updateTotals() {
                const priceElements = document.querySelectorAll('p[id]');
                let totalPrice = 0;

                priceElements.forEach(priceElement => {
                    const priceText = priceElement.textContent.replace(' €', '');
                    const price = parseFloat(priceText);
                    if (!isNaN(price)) {
                        totalPrice += price;
                    }
                });

                const totalElements = document.querySelectorAll('.ca');
                totalElements.forEach(totalElement => {
                    totalElement.textContent = totalPrice.toFixed(2) + ' €';
                });
            }
        });
    </script>
</head>

<body>
    <main>
        <?php include "header.php"; ?>
        <div class="container h-100 py-5">
            <div class="row d-flex justify-content-center align-items-start h-100">
                <?php if (empty($cartProducts)) : ?>
                    <!-- Message pour panier vide -->
                    <div class="col-lg-8 text-center">
                        <div class="alert alert-dark" role="alert">
                            Votre panier est vide. <a href="search.php" class="text-dark mt-2">Cliquez ici pour explorer notre catalogue</a>
                        </div>
                    </div>
                <?php else : ?>
                    <!-- Cadre panier -->
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

                    <!-- Cadre total -->
                    <div class="col-lg-4 mt-sm-5 mt-md-0">
                        <?php echo "
                            <div class='d-flex justify-content-between' style='font-weight: 500;'>
                                <p class='mb-2'>Sous-total</p>
                                <p class='mb-2 ca'>" . number_format($totalPrice, 2, ',', ' ') . " €</p>
                            </div>";
                        ?>

                        <div class="d-flex justify-content-between" style="font-weight: 500;">
                            <p class="mb-0">Livraison</p>
                            <p class="mb-0">Gratuite</p>
                        </div>

                        <hr class="my-4">

                        <?php echo "
                            <div class='d-flex justify-content-between mb-4' style='font-weight: 500;'>
                                <p class='mb-2'>Total</p>
                                <p class='mb-2 ca'>" . number_format($totalPrice, 2, ',', ' ') . " €</p>
                            </div>";
                        ?>

                        <button type="button" id="checkoutButton" class="btn btn-dark btn-block btn-lg">
                            Passer la commande
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="push"></div>
    </main>

    <?php include "footer.php"; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>