<?php
session_start();

include("config.php");
include("functions.php");
include("mail.php");

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    die;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_SESSION['user_id'];

    // Si la requête est pour supprimer le compte
    $input = json_decode(file_get_contents('php://input'), true);
    if (isset($input['action']) && $input['action'] === 'deleteAccount') {

        $userToDelete = checkLogin($con);
        if (deleteUser($con, $id)) {
            sendAccountDeleteEmail($userToDelete['email'], $userToDelete['nom']);
            session_destroy();
            echo "accountDeleted";
        } else {
            echo "errorDeletingAccount";
        }

        exit;
    }

    // Sinon, c'est une mise à jour des informations de l'utilisateur
    $name = $_POST['name'];
    $email = $_POST['email'];

    if (EditUserInfo($con, $id, $name, $email)) {
        echo "User info updated successfully";
    } else {
        echo "Error updating user info";
    }
    exit;
}

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



// Récupération des adresses de l'utilisateur
$addresses = getUserAddresses($con, $userData['id']);
$paymentMethods = getUserPaymentMethods($con, $userData['id']);

$userID = $_SESSION['user_id'];



if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'delete_address') {
        $addressId = $_POST['address_id'] ?? 0;
        if (deleteUserAddress($con, $userId, $addressId)) {
            echo 'addressDeleted';
        } else {
            echo 'errorDeletingAddress';
        }
        exit;
    }

    if ($action === 'delete_payment') {
        $paymentId = $_POST['payment_id'] ?? 0;
        if (deleteUserPaymentMethod($con, $userId, $paymentId)) {
            echo 'paymentDeleted';
        } else {
            echo 'errorDeletingPayment';
        }
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Mon Compte</title>
    <link rel="icon" type="image/x-icon" href="assets/img/logo-black.png" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <link rel="stylesheet" href="assets/css/style.css" />

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        function showDeleteAccountVerification() {
            let element = document.getElementById("verifyDeleteAccount");
            element.setAttribute("style", "display: inline;");
        }

        function hideDeleteAccountVerification() {
            let element = document.getElementById("verifyDeleteAccount");
            element.setAttribute("style", "display: none;");
        }

        function deleteAccount() {
            fetch('profile.php', {
                    method: 'POST',
                    body: JSON.stringify({
                        action: 'deleteAccount'
                    }),
                    headers: {
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.text())
                .then(data => {
                    console.log(data);
                    if (data === "accountDeleted") {
                        alert("Votre compte a bien été supprimé");
                        window.location.href = 'index.php';
                    } else {
                        alert("Erreur lors de la suppression du compte");
                    }
                })
                .catch(error => {
                    console.error(error);
                    alert("Erreur lors de la suppression du compte");
                });
        }

        function EditUserInfo() {
            const formData = new FormData();
            formData.append('name', document.getElementById("nameInput").value);
            formData.append('email', document.getElementById("emailInput").value);

            fetch('profile.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.text())
                .then(data => {
                    console.log(data);
                    alert("User info updated successfully");
                })
                .catch(error => {
                    console.error(error);
                    alert("Error updating user info");
                });
        }

        function deleteAddress(addressId) {
            fetch('profile.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({
                        action: 'delete_address',
                        address_id: addressId
                    })
                })
                .then(response => response.text())
                .then(data => {
                    if (data === 'addressDeleted') {
                        alert('Adresse supprimée avec succès');
                        location.reload(); // Recharger la page pour mettre à jour la liste des adresses
                    } else {
                        alert('Erreur lors de la suppression de l\'adresse');
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    alert('Erreur lors de la suppression de l\'adresse');
                });
        }

        function deletePayment(paymentId) {
            fetch('profile.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({
                        action: 'delete_payment',
                        payment_id: paymentId
                    })
                })
                .then(response => response.text())
                .then(data => {
                    if (data === 'paymentDeleted') {
                        alert('Moyen de paiement supprimé avec succès');
                        location.reload(); // Recharger la page pour mettre à jour la liste des moyens de paiement
                    } else {
                        alert('Erreur lors de la suppression du moyen de paiement');
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    alert('Erreur lors de la suppression du moyen de paiement');
                });
        }


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

        <?php
        $userData = checkLogin($con);
        $id = $userData['id'];
        $name = $userData['nom'];
        $email = $userData['email'];
        ?>

        <div class='container rounded bg-white mt-5 mb-5'>
            <div class='row'>
                <div class='col-md-6 border-right shadow'>
                    <div class='p-3 py-5'>
                        <div class='d-flex justify-content-between align-items-center mb-3'>
                            <h4 class='text-right'>Informations du compte</h4>
                        </div>
                        <div class='row mt-3'>
                            <div class='col-md-12 mb-3'>
                                <label class='labels'>Nom</label>
                                <input type='text' id='nameInput' class='form-control' value='<?php echo htmlspecialchars($userData['nom']); ?>'>
                            </div>
                            <hr />
                            <div class='col-md-12 mb-3'>
                                <label class='labels'>Adresse Mail</label>
                                <input type='text' id='emailInput' class='form-control' value='<?php echo htmlspecialchars($userData['email']); ?>'>
                            </div>
                            <hr />
                            <div class='col-md-12 mb-3'>
                                <label class='labels'>Mes Adresses</label>
                                <ul>
                                    <?php foreach ($addresses as $address): ?>
                                        <li>
                                            <?php echo htmlspecialchars($address['adresse_complète']); ?>

                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                                <button type="button" class="btn btn-link" data-bs-toggle="modal" data-bs-target="#newAddressModal">+ Ajouter une nouvelle adresse</button>
                            </div>
                            <hr />
                            <div class='col-md-12 mb-3'>
                                <label class='labels'>Mes Moyens de paiement</label>
                                <ul>
                                    <?php foreach ($paymentMethods as $paymentMethod): ?>
                                        <li>
                                            <?php echo $paymentMethod['type'] === 'card' ? 'Carte bancaire' : 'PayPal'; ?> :
                                            <?php echo $paymentMethod['type'] === 'card' ? '**** **** **** ' . substr($paymentMethod['numero_carte'], -4) : htmlspecialchars($paymentMethod['paypal_email']); ?>

                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                                <button type="button" class="btn btn-link" data-bs-toggle="modal" data-bs-target="#newPaymentModal">+ Ajouter un nouveau moyen de paiement</button>
                            </div>
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
                <div class='col-md-6'>
                    <div class='p-3 py-5'>
                        <a href='myorders.php'><button class='btn btn-dark col-12 mb-3'>Mes Commandes</button></a>
                        <a href='resetpassword.php?email=<?php echo urlencode($userData['email']); ?>'><button class='btn btn-dark col-12 mb-3'>Réinitialiser mon mot de passe</button></a>
                        <button class='btn btn-danger col-12 mb-3' onclick='showDeleteAccountVerification()'>Supprimer mon compte</button>
                        <span id='verifyDeleteAccount' class='m-2' style='display: none;'>
                            <h5 class='mb-3'>Êtes-vous sûr de vouloir supprimer votre compte ?</h5>
                            <button class='btn btn-danger col-4 mb-3 me-3' onclick='deleteAccount()'>Oui</button>
                            <button class='btn btn-dark col-4 mb-3' onclick='hideDeleteAccountVerification()'>Non</button>
                        </span>
                        <?php

                        if ($userData['est_admin'] == 1) {
                            echo "<a href='backoffice/dashboard.php'><button class='btn btn-dark col-12 mb-3'>Panneau d'administration</button></a>";
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="push"></div>
    </main>
    <?php include "footer.php"; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>