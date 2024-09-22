<?php
session_start();

include_once("config.php");
include_once("functions.php");
include_once "API/usersRequests.php";
include_once "API/ordersRequests.php";
include_once "API/cartsRequests.php";

$user = GetCurrentUser($pdo);
if (!$user) {
    header("Location: login.php");
    exit();
}

$userID = $user['id'];

$cart = GetCart($pdo, $user['id']);
$cartProducts = [];
$totalPrice = 0;

if ($cart) {

    if(RemoveUnActiveProductsFromCart($pdo, $user['id'])){
        DisplayDismissibleWarning("Certains produits de votre panier ne sont plus disponibles et ont été retirés.");
    }
    if(AdaptQuantityInCart($pdo, $user['id']) === true){
        DisplayDismissibleWarning("La quantité de certains produits de votre panier a été ajustée selon nos stocks.");

    }
    $cartProducts = GetCartProducts($pdo, $cart['id']);
    foreach ($cartProducts as $product) {
        $totalPrice += ($product['prix'] * $product['quantite']);
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Ajout d'une adresse
    if (isset($_POST['add_address'])) {
        $voie = $_POST['voie'];
        $ville = $_POST['ville'];
        $codePostal = $_POST['code_postal'];
        $pays = $_POST['pays'];

        if (AddUserAddress($pdo, $userID, $voie, $ville, $codePostal, $pays)) {
            DisplayDismissibleSuccess("Adresse ajoutée avec succès");
        } else {
            DisplayDismissibleAlert("Erreur lors de l'ajout de l'adresse");
        }
    }

    // Ajout d'un moyen de paiement
    if (isset($_POST['add_payment'])) {
        $paymentType = $_POST['payment_type'];

        if ($paymentType == 'card') {
            $cardNumber = $_POST['card_number'];
            $cardName = $_POST['card_name'];
            $expirationDate = $_POST['expiration_date'];
            $cvv = $_POST['cvv'];

            if($cardNumber == "" || $cardName == "" || $expirationDate == "" || $cvv == ""){
                DisplayDismissibleAlert("Veuillez remplir tous les champs pour ajouter un moyen de paiement");
            }else{
                
                if (AddUserPaymentMethod($pdo, $userID, 'card', $cardNumber, $cardName, $expirationDate, $cvv, null)) {
                    DisplayDismissibleSuccess("Moyen de paiement ajouté avec succès");
                } else {
                    DisplayDismissibleAlert("Erreur lors de l'ajout du moyen de paiement");
                }
            }

            
        } 
        elseif ($paymentType == 'paypal') {
            $paypalEmail = $_POST['paypal_email'];
            if($paypalEmail == ""){
                DisplayDismissibleAlert("Veuillez remplir tous les champs pour ajouter un moyen de paiement");
            }else{
                if (AddUserPaymentMethod($pdo, $userID, 'paypal', null, null, null, null, $paypalEmail)) {
                    DisplayDismissibleSuccess("Moyen de paiement ajouté avec succès");
                } else {
                    DisplayDismissibleAlert("Erreur lors de l'ajout du moyen de paiement");
                }
            }
        }
    }


    if(isset($_POST['checkout'])){

        echo "<script>document.querySelectorAll('button[type=submit]').forEach(button => button.disabled = true);</script>";

        if(!isset($_POST['billing_address']) || !isset($_POST['shipping_address']) || !isset($_POST['payment_method'])){
            DisplayDismissibleAlert("Veuillez sélectionner une adresse de facturation, une adresse de livraison et un moyen de paiement pour valider la commande");
        }else{
            $billingAddressID = $_POST['billing_address'];
            $shippingAddressID = $_POST['shipping_address'];
            $paymentMethodID = $_POST['payment_method'];
            
            $cartProductsIdAndQuantity = [];
            foreach($cartProducts as $product){
                $cartProductsIdAndQuantity[] = array("id" => $product['id'], "quantity" => $product['quantite']);
            }

            $checkoutResult = CheckoutOrder($pdo, $user['id'], $totalPrice,$billingAddressID, $shippingAddressID, $paymentMethodID, $cartProductsIdAndQuantity, $message);
    
            if($checkoutResult === false){
                DisplayDismissibleAlert($message);
            }else{
                DisplayDismissibleSuccess($message);
                ClearCart($pdo, $user['id']);
            
                echo "<script>setTimeout(function(){window.location.href = 'order.php?id=$checkoutResult';}, 1000);</script>";
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
    <title>Checkout</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/style.css" />
    <link rel="icon" type="image/x-icon" href="assets/img/logo-black.png" />

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">



    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
           
            // Gestion de l'affichage des champs de paiement
            $('#payment_type').on('change', function() {
                const paymentType = $(this).val();
                if (paymentType === 'card') {
                    $('#card_info').show();
                    $('#paypal_info').hide();
                } else if (paymentType === 'paypal') {
                    $('#card_info').hide();
                    $('#paypal_info').show();
                } else {
                    $('#card_info').hide();
                    $('#paypal_info').hide();
                }
            });
        });
    </script>
</head>


<body>
    <main>
        <?php include "header.php"; ?>
        <div class="container py-5">
            <div class="row">
                <!-- Détails de la commande -->
                <div class="col-lg-8">
                    <h4>Détails de la commande</h4>
                    <div class="table-responsive ">
                        <table class="table mb-5">
                            <thead>
                                <tr>
                                    <th>Produit</th>
                                    <th>Quantité</th>
                                    <th>Prix</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                foreach ($cartProducts as $product){
                                    $productName = $product['nom'];
                                    $productQuantity = $product['quantite'];
                                    $productPrice = number_format($product['prix'] * $productQuantity, 2) . " €";

                                    echo "
                                    <tr>
                                        <td>$productName</td>
                                        <td>$productQuantity</td>
                                        <td>$productPrice</td>
                                    </tr>";
                                }?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="2" class="text-end">Total :</td>
                                    <td><?php echo number_format($totalPrice, 2) . " €"; ?></td>
                                </tr>
                            </tfoot>
                        </table>

                        <form method="post" action="checkout.php">
                            <h4>Adresse de Facturation</h4>
                            <ul class="list-unstyled">
                                <?php
                                $addresses = GetUserAddresses($pdo, $user['id']);

                                if ($addresses == false) {
                                    echo "Aucune adresse enregistrée";
                                } else {

                                    foreach ($addresses as $address) {

                                        echo "<li>
                                            <input type='radio' name='billing_address' value='" . $address['id'] . "' id='billing_" . $address['id'] . "' required>
                                            <label for='billing_" . $address['id'] . "'>" . $address['voie'] . "</label>
                                        </li>";
                                    }
                                }
                                ?>
                            </ul>
                            <hr/>
                            <h4>Adresse de Livraison</h4>
                            <ul class="list-unstyled">
                                <?php
                                $addresses = GetUserAddresses($pdo, $user['id']);

                                if ($addresses == false) {
                                    echo "Aucune adresse enregistrée";
                                } else {

                                    foreach ($addresses as $address) {
                                        echo "<li>
                                            <input type='radio' name='shipping_address' value='" . $address['id'] . "' id='shipping_" . $address['id'] . "' required>
                                            <label for='shipping_" . $address['id'] . "'>" . $address['voie'] . "</label>
                                        </li>";
                                    }
                                }
                                ?>
                            </ul>

                            <hr/>
                            <h4>Moyens de Paiement</h4>

                            <ul class="list-unstyled">
                                <?php

                                $paymentMethods = GetUserPaymentMethods($pdo, $user['id']);
                                if ($paymentMethods == false) {
                                    echo "Aucun moyen de paiement enregistré";
                                } else {
                                    foreach ($paymentMethods as $paymentMethod) {
                                        $paymentMethodID = $paymentMethod['id'];
                                        $paymentMethodType = $paymentMethod['type'] === 'card' ? 'Carte bancaire' : 'PayPal';
                                        $paymentMethodDetails = $paymentMethod['type'] === 'card' ? '**** **** **** ' . substr($paymentMethod['numero_carte'], -4) : $paymentMethod['paypal_email'];

                                        echo "
                                    <li>
                                        <input type='radio' name='payment_method' value='$paymentMethodID' id='payment_$paymentMethodID' required>
                                        <label for='payment_$paymentMethodID'>$paymentMethodType - $paymentMethodDetails</label>
                                    </li>";
                                    }
                                }

                                ?>

                            </ul>

                            <button type="submit" name="checkout" class="btn btn-dark mt-5" name="place_order">Valider la commande</button>
                        </form>
                    </div>
                </div>

                <div class="col-lg-4">
                    <form action="checkout.php" method="POST">
                        <button type="button" class="btn btn-dark mt-3" data-bs-toggle="modal" data-bs-target="#addPaymentModal">
                            Ajouter un moyen de paiement
                        </button>

                        <button type="button" class="btn btn-dark mt-3" data-bs-toggle="modal" data-bs-target="#addAddressModal">
                            Ajouter une adresse
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="addAddressModal" tabindex="-1" aria-labelledby="addAddressModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addAddressModalLabel">Ajouter une nouvelle adresse</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="voie" class="form-label">Voie</label>
                                <input type="text" class="form-control" name="voie" required>
                            </div>
                            <div class="mb-3">
                                <label for="ville" class="form-label">Ville</label>
                                <input type="text" class="form-control" name="ville" required>
                            </div>
                            <div class="mb-3">
                                <label for="code_postal" class="form-label">Code Postal</label>
                                <input type="text" class="form-control" name="code_postal" required>
                            </div>
                            <div class="mb-3">
                                <label for="pays" class="form-label">Pays</label>
                                <input type="text" class="form-control" name="pays" required>
                            </div>
                            <button type="submit" name="add_address" class="btn btn-dark">Ajouter une adresse</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modale pour ajouter un moyen de paiement -->
        <div class="modal fade" id="addPaymentModal" tabindex="-1" aria-labelledby="addPaymentModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addPaymentModalLabel">Ajouter un moyen de paiement</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="payment_type" class="form-label">Type de paiement</label>
                                <select class="form-select" name="payment_type" required>
                                    <option value="card">Carte bancaire</option>
                                    <option value="paypal">PayPal</option>
                                </select>
                            </div>

                            <div class="card-info">
                                <div class="mb-3">
                                    <label for="card_number" class="form-label">Numéro de la carte</label>
                                    <input type="text" class="form-control" name="card_number">
                                </div>
                                <div class="mb-3">
                                    <label for="card_name" class="form-label">Nom sur la carte</label>
                                    <input type="text" class="form-control" name="card_name">
                                </div>
                                <div class="mb-3">
                                    <label for="expiration_date" class="form-label">Date d'expiration</label>
                                    <input type="month" class="form-control" name="expiration_date">
                                </div>
                                <div class="mb-3">
                                    <label for="cvv" class="form-label">CVV</label>
                                    <input type="text" class="form-control" name="cvv">
                                </div>
                            </div>

                            <div class="paypal-info" style="display:none;">
                                <div class="mb-3">
                                    <label for="paypal_email" class="form-label">Email PayPal</label>
                                    <input type="email" class="form-control" name="paypal_email">
                                </div>
                            </div>

                            <button type="submit" name="add_payment" class="btn btn-dark">Ajouter un moyen de paiement</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>



        <div class="push"></div>
    </main>

    <?php include "footer.php"; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.querySelector('select[name="payment_type"]').addEventListener('change', function () {
            if (this.value === 'card') {
                document.querySelector('.card-info').style.display = 'block';
                document.querySelector('.paypal-info').style.display = 'none';
            } else {
                document.querySelector('.card-info').style.display = 'none';
                document.querySelector('.paypal-info').style.display = 'block';
            }
        });
    </script>
    <script>
        if ( window.history.replaceState ) {
            window.history.replaceState( null, null, window.location.href );
        }
    </script>
</body>

</html>