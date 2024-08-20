<?php
session_start();

include("config.php");
include("functions.php");

$userData = checkLogin($con);
if (!$userData) {
    header("Location: login.php");
    exit();
}

$emptyCart = true;

$getCart = getCart($con, $userData['id']);
if ($getCart) {
    $cartProducts = getCartProducts($con, $getCart['id']);
    if ($cartProducts) {
        $emptyCart = false;
    }
}

$totalPrice = 0;
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
                        this.value = maxStock; // Reset to maxStock if invalid
                        return;
                    }

                    // Calculate total price for the product
                    const totalPriceForProduct = quantity * pricePerUnit;

                    // Update the price in the table
                    const priceElement = document.querySelector(`p[id="${productID}"]`);
                    if (priceElement) {
                        priceElement.textContent = totalPriceForProduct.toFixed(2) + ' €';
                    } else {
                        console.error(`Price element for productID ${productID} not found.`);
                    }

                    // Remove the product row if quantity is 0
                    if (quantity === 0) {
                        const row = this.closest('tr');
                        if (row) {
                            row.remove();
                            // Update totals after row removal
                            updateTotals();
                            // Send AJAX request to remove product from cart
                            const xhr = new XMLHttpRequest();
                            xhr.open('POST', 'cart_manager.php', true);
                            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                            xhr.send('productID=' + encodeURIComponent(productID) + '&quantity=0');
                        }
                    } else {
                        // Update totals if quantity is not 0
                        updateTotals();

                        // Send AJAX request to update cart quantity
                        const xhr = new XMLHttpRequest();
                        xhr.open('POST', 'cart_manager.php', true);
                        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                        xhr.onreadystatechange = function() {
                            if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
                                // Optionally, you can handle the server response here
                            }
                        };
                        xhr.send('productID=' + encodeURIComponent(productID) + '&quantity=' + encodeURIComponent(quantity));
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
                <!-- cadre panier -->
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
                                <?php
                                foreach ($cartProducts as $product) {
                                    $productID = $product['id'];
                                    $productName = $product['nom'];
                                    $pathProductImg = PATH_PRODUCTS_IMAGES . $productID . ".webp";
                                    $quantity = $product['quantite'];
                                    $maxStock = $product['stock'];
                                    $pricePerUnit = $product['prix'];
                                    $totalPriceForProduct = $pricePerUnit * $quantity;
                                    $totalPrice += $totalPriceForProduct;

                                    echo "<tr>
                                                <th scope='row'>
                                                    <div class='d-flex align-items-center'>
                                                        <img src='$pathProductImg' class='img-fluid rounded-3' style='width: 120px;' alt='$productName'>
                                                        <div class='flex-column ms-4'>
                                                            <p class='mb-2'>$productName</p>
                                                        </div>
                                                    </div>
                                                </th>
                                                <td class='align-middle'>
                                                    <div class='d-flex flex-row'>
                                                        <input min='0' max='$maxStock' name='quantity' value='$quantity' type='number' class='mouse-only-number-input form-control form-control-sm' style='width: 50px;' data-product-id='{$product['id']}' data-price-per-unit='$pricePerUnit' />
                                                    </div>
                                                </td>
                                                <td class='align-middle'>
                                                    <p class='mb-0' id='$productID' style='font-weight: 500;'>$totalPriceForProduct €</p>
                                                </td>
                                            </tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>


                <!-- cadre total -->
                <div class="col-lg-4 mt-sm-5 mt-md-0">
                    <?php echo "
                        <div class='d-flex justify-content-between' style='font-weight: 500;'>
                            <p class='mb-2'>Sous-total</p>
                            <p class='mb-2 ca'> $totalPrice €</p>
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
                            <p class='mb-2 ca'> $totalPrice €</p>
                        </div>";
                    ?>

                    <button type="button" class="btn btn-dark btn-block btn-lg">
                        Passer la commande
                    </button>
                </div>
            </div>
        </div>
        <div class="push"></div>
    </main>

    <?php include "footer.php"; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>