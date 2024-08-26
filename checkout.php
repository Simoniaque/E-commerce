<?php
session_start();

include("config.php");
include("functions.php");

$userData = checkLogin($con);
if (!$userData) {
    header("Location: login.php");
    exit();
}

// Traitement de l'ajout d'une adresse
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'add_address') {
    $userId = $_POST['user_id'];
    $adresseComplete = $_POST['adresse_complete'];
    $ville = $_POST['ville'];
    $codePostal = $_POST['code_postal'];
    $pays = $_POST['pays'];

    $result = addUserAddress($con, $userId, $adresseComplete, $ville, $codePostal, $pays);

    if ($result) {
        echo 'success';
    } else {
        echo 'error';
    }
    exit(); // Terminer le script après traitement POST
}

// Traitement de l'ajout d'un moyen de paiement
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'add_payment') {
    $userId = $userData['id'];
    $paymentType = $_POST['payment_type'];

    if ($paymentType == 'card') {
        $cardNumber = $_POST['card_number'];
        $cardName = $_POST['card_name'];
        $expirationDate = $_POST['expiration_date'];
        $cvv = $_POST['cvv'];

        $result = addUserPaymentMethod($con, $userId, 'card', $cardNumber, $cardName, $expirationDate, $cvv, null);

        if ($result) {
            echo 'success';
        } else {
            echo 'error';
        }
    } elseif ($paymentType == 'paypal') {
        $paypalEmail = $_POST['paypal_email'];

        $result = addUserPaymentMethod($con, $userId, 'paypal', null, null, null, null, $paypalEmail);

        if ($result) {
            echo 'success';
        } else {
            echo 'error';
        }
    }
    exit(); // Terminer le script après traitement POST
}

$getCart = getCart($con, $userData['id']);
$cartProducts = [];
$totalPrice = 0;

if ($getCart) {
    $cartProducts = getCartProducts($con, $getCart['id']);
    foreach ($cartProducts as $product) {
        $totalPrice += $product['prix'] * $product['quantite'];
    }
}

// Récupération des adresses de l'utilisateur
$addresses = getUserAddresses($con, $userData['id']);
$paymentMethods = getUserPaymentMethods($con, $userData['id']);

$userID = $_SESSION['user_id'];

// Vérifier si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {

    // Appeler la fonction addOrder pour créer une commande
    $orderID = addOrder($con, $userID);

    $_SESSION['order_message'] = "Merci pour votre commande ! Votre numéro de commande est $orderID. 
Le montant total est de €$totalAmount. Votre commande sera livrée $deliveryDate.";

    if ($orderID) {
        // Rediriger vers une page de confirmation de commande
        header("Location: index.php");
        exit();
    } else {
        // Afficher un message d'erreur ou rediriger vers une page d'erreur
        echo "Erreur lors de la création de la commande. Veuillez réessayer.";
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
            $('#newAddressForm').on('submit', function(event) {
                event.preventDefault();

                $.ajax({
                    url: 'checkout.php', // URL du fichier actuel
                    type: 'POST',
                    data: $(this).serialize() + '&action=add_address',
                    success: function(response) {
                        if (response === 'success') {
                            location.reload(); // Recharger la page pour mettre à jour les adresses
                        } else {
                            alert('Erreur lors de l\'ajout de l\'adresse.');
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.error('Erreur AJAX :', textStatus, errorThrown);
                    }
                });
            });
            // Gestion de l'ajout d'un moyen de paiement
            $('#paymentForm').on('submit', function(event) {
                event.preventDefault();

                $.ajax({
                    url: 'checkout.php',
                    type: 'POST',
                    data: $(this).serialize() + '&action=add_payment',
                    success: function(response) {
                        if (response === 'success') {
                            location.reload(); // Recharger la page pour mettre à jour les moyens de paiement
                        } else {
                            alert('Erreur lors de l\'ajout du moyen de paiement.');
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.error('Erreur AJAX :', textStatus, errorThrown);
                    }
                });
            });

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
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Produit</th>
                                    <th>Quantité</th>
                                    <th>Prix</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($cartProducts as $product): ?>
                                    <tr>
                                        <td><?php echo $product['nom']; ?></td>
                                        <td><?php echo $product['quantite']; ?></td>
                                        <td><?php echo number_format($product['prix'] * $product['quantite'], 2) . " €"; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="2" class="text-end">Total :</td>
                                    <td><?php echo number_format($totalPrice, 2) . " €"; ?></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <!-- Sélection des adresses -->
                <div class="col-lg-4">
                    <form action="checkout.php" method="POST">
                        <h4>Adresse de Facturation</h4>
                        <ol>
                            <?php foreach ($addresses as $address): ?>
                                <li>
                                    <input type="radio" name="billing_address" value="<?php echo $address['id']; ?>" id="billing_<?php echo $address['id']; ?>" required>
                                    <label for="billing_<?php echo $address['id']; ?>"><?php echo $address['adresse_complète']; ?></label>
                                </li>
                            <?php endforeach; ?>
                        </ol>

                        <h4>Adresse de Livraison</h4>
                        <ol>
                            <?php foreach ($addresses as $address): ?>
                                <li>
                                    <input type="radio" name="shipping_address" value="<?php echo $address['id']; ?>" id="shipping_<?php echo $address['id']; ?>" required>
                                    <label for="shipping_<?php echo $address['id']; ?>"><?php echo $address['adresse_complète']; ?></label>
                                </li>
                            <?php endforeach; ?>
                        </ol>
                        <button type="button" class="btn btn-link" data-bs-toggle="modal" data-bs-target="#newAddressModal">+ Ajouter une nouvelle adresse</button>
                        <form method="post" action="checkout.php">
                            <button type="submit" name="place_order">Valider la commande</button>
                        </form>
                    </form>
                </div>
            </div>

            <!-- Gestion des moyens de paiement -->
            <div class="row mt-5">
                <div class="col-lg-12">
                    <h4>Moyens de Paiement</h4>
                    <ul>
                        <?php foreach ($paymentMethods as $paymentMethod): ?>
                            <li>
                                <input type="radio" name="payment_method" value="<?php echo $paymentMethod['id']; ?>" id="payment_<?php echo $paymentMethod['id']; ?>" required>
                                <label for="payment_<?php echo $paymentMethod['id']; ?>">
                                    <?php echo $paymentMethod['type'] === 'card' ? 'Carte bancaire' : 'PayPal'; ?> -
                                    <?php echo $paymentMethod['type'] === 'card' ? '**** **** **** ' . substr($paymentMethod['numero_carte'], -4) : $paymentMethod['paypal_email']; ?>
                                </label>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <button type="button" class="btn btn-link" data-bs-toggle="modal" data-bs-target="#newPaymentModal">+ Ajouter un nouveau moyen de paiement</button>
                </div>
            </div>
        </div>

        <!-- Modal pour ajouter une nouvelle adresse -->
        <div class="modal fade" id="newAddressModal" tabindex="-1" aria-labelledby="newAddressModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form id="newAddressForm">
                        <div class="modal-header">
                            <h5 class="modal-title" id="newAddressModalLabel">Nouvelle Adresse</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="adresse_complete" class="form-label">Adresse complète</label>
                                <input type="text" class="form-control" id="adresse_complete" name="adresse_complete" required>
                            </div>
                            <div class="mb-3">
                                <label for="ville" class="form-label">Ville</label>
                                <input type="text" class="form-control" id="ville" name="ville" required>
                            </div>
                            <div class="mb-3">
                                <label for="code_postal" class="form-label">Code Postal</label>
                                <input type="text" class="form-control" id="code_postal" name="code_postal" required>
                            </div>
                            <div class="mb-3">
                                <label for="pays" class="form-label">Pays</label>
                                <input type="text" class="form-control" id="pays" name="pays" required>
                            </div>
                            <input type="hidden" name="user_id" value="<?php echo $userData['id']; ?>">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                            <button type="submit" class="btn btn-primary">Ajouter l'adresse</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal pour ajouter un nouveau moyen de paiement -->
        <div class="modal fade" id="newPaymentModal" tabindex="-1" aria-labelledby="newPaymentModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form id="paymentForm">
                        <div class="modal-header">
                            <h5 class="modal-title" id="newPaymentModalLabel">Nouveau Moyen de Paiement</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="payment_type" class="form-label">Type de Paiement</label>
                                <select class="form-select" id="payment_type" name="payment_type" required>
                                    <option value="">Sélectionner...</option>
                                    <option value="card">Carte Bancaire</option>
                                    <option value="paypal">PayPal</option>
                                </select>
                            </div>
                            <div id="card_info" style="display:none;">
                                <div class="mb-3">
                                    <label for="card_number" class="form-label">Numéro de Carte</label>
                                    <input type="text" class="form-control" id="card_number" name="card_number">
                                </div>
                                <div class="mb-3">
                                    <label for="card_name" class="form-label">Nom sur la Carte</label>
                                    <input type="text" class="form-control" id="card_name" name="card_name">
                                </div>
                                <div class="mb-3">
                                    <label for="expiration_date" class="form-label">Date d'Expiration</label>
                                    <input type="month" class="form-control" id="expiration_date" name="expiration_date">
                                </div>
                                <div class="mb-3">
                                    <label for="cvv" class="form-label">CVV</label>
                                    <input type="text" class="form-control" id="cvv" name="cvv">
                                </div>
                            </div>
                            <div id="paypal_info" style="display:none;">
                                <div class="mb-3">
                                    <label for="paypal_email" class="form-label">Email PayPal</label>
                                    <input type="email" class="form-control" id="paypal_email" name="paypal_email">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                            <button type="submit" class="btn btn-primary">Ajouter le moyen de paiement</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="push"></div>
    </main>

    <?php include "footer.php"; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>