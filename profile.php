<?php
session_start();

include_once "config.php";
include_once "functions.php";
include_once "mail.php";
include_once "API/usersRequests.php";


if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    die;
}

$userID = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Si la requête est pour supprimer le compte
    if (isset($_POST['action']) && $_POST['action'] === 'deleteAccount') {

        $userToDelete = GetCurrentUser($pdo);
        if (DeactivateUser($pdo, $userID)) {
            sendAccountDeleteEmail($userToDelete['email'], $userToDelete['nom']);
            $alertMessage = "Compte supprimé avec succès.";
            session_destroy();
            
            $encodedMessage = urlencode("Compte supprimé avec succès.");
            header("Location: index.php?message=" . $encodedMessage);
            exit();
        } else {
            DisplayDismissibleAlert("Erreur lors de la suppression du compte");
        }
    }

    // Mise à jour des informations de l'utilisateur
    if (isset($_POST['update_info'])) {
        $name = $_POST['name'];
        $email = $_POST['email'];

        if (UpdateUserInfo($pdo, $userID, $name, $email)) {
            DisplayDismissibleSuccess("Informations mises à jour avec succès");
        } else {
            DisplayDismissibleAlert("Erreur lors de la mise à jour des informations");
        }
    }

    // Ajout d'une adresse
    if (isset($_POST['add_address'])) {
        $adresseComplete = $_POST['adresse_complete'];
        $ville = $_POST['ville'];
        $codePostal = $_POST['code_postal'];
        $pays = $_POST['pays'];

        if (AddUserAddress($pdo, $userID, $adresseComplete, $ville, $codePostal, $pays)) {
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

    // Suppression d'une adresse
    if (isset($_POST['delete_address'])) {
        $addressId = $_POST['address_id'];
        if (DeactivateUserAddress($pdo, $userID, $addressId)) {
            DisplayDismissibleSuccess("Adresse supprimée avec succès.");
        } else {
            DisplayDismissibleAlert("Erreur lors de la suppression de l'adresse");
        }
    }

    // Suppression d'un moyen de paiement
    if (isset($_POST['delete_payment'])) {
        $paymentId = $_POST['payment_id'];
        if (DeactivateUserPaymentMethod($pdo, $userID, $paymentId)) {
            DisplayDismissibleSuccess("Moyen de paiement supprimé avec succès.");
        } else {
            DisplayDismissibleAlert("Erreur lors de la suppression du moyen de paiement");
        }
    }
}


$user = GetCurrentUser($pdo);
if (!$user) {
    header("Location: login.php");
    exit();
}

// Récupération des adresses et moyens de paiement de l'utilisateur
$addresses = GetUserAddresses($pdo, $user['id']);
if(!$addresses){
    $addresses = array();
}

$paymentMethods = GetUserPaymentMethods($pdo, $user['id']);
if(!$paymentMethods){
    $paymentMethods = array();
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
</head>

<body>
    <main>
        <?php include "header.php"; ?>

        <div class='container rounded bg-white mt-5 mb-5'>
            <div class='row'>
                <div class='col-md-6 border-right shadow'>
                    <div class='p-3 py-5'>
                        <div class='d-flex justify-content-between align-items-center mb-3'>
                            <h4 class='text-right'>Informations du compte</h4>
                        </div>
                        <form method="POST" action="">
                            <div class='row mt-3'>
                                <div class='col-md-12 mb-3'>
                                    <label class='labels'>Nom</label>
                                    <input type='text' name='name' class='form-control' value='<?php echo $user['nom']; ?>'>
                                </div>
                                <div class='col-md-12 mb-3'>
                                    <label class='labels'>Adresse Mail</label>
                                    <input type='text' name='email' class='form-control' value='<?php echo $user['email']; ?>'>
                                </div>
                                <div class='col-md-12 mb-3'>
                                    <button type='submit' name='update_info' class='btn btn-primary'>Mettre à jour</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class='col-md-6'>
                    <div class='p-3 py-5'>
                        <a href='myorders.php'><button class='btn btn-dark col-12 mb-3'>Mes Commandes</button></a>
                        <a href='resetpassword.php?email=<?php echo urlencode($user['email']); ?>'><button class='btn btn-dark col-12 mb-3'>Réinitialiser mon mot de passe</button></a>


                        <button class='btn btn-danger col-12 mb-3' onclick='showDeleteAccountVerification()'>Supprimer mon compte</button>

                        <span id='verifyDeleteAccount' class='m-2' style='display: none;'>
                            <h5 class='mb-3'>Êtes-vous sûr de vouloir supprimer votre compte ?</h5>
                            <form method="POST" action="">
                                <button type="submit" name="action" value="deleteAccount" class="btn btn-danger col-4 mb-3">Oui</button>
                                <button class='btn btn-dark col-4 mb-3' onclick='hideDeleteAccountVerification()'>Non</button>
                            </form>
                        </span>
                        
                        <?php if ($user['est_admin'] == 1): ?>
                            <a href='backoffice/dashboard.php'><button class='btn btn-dark col-12 mb-3'>Panneau d'administration</button></a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="container mt-5 shadow p-5">
            <h4>Mes Adresses</h4>
            <ul>
                <?php foreach ($addresses as $address): ?>
                    <li>
                        <?php echo $address['adresse_complète']; ?>
                        <form method="POST" action="" style="display:inline;">
                            <input type="hidden" name="address_id" value="<?php echo $address['id']; ?>">
                            <button type="submit" name="delete_address" class="btn btn-link">Supprimer</button>
                        </form>
                    </li>
                <?php endforeach; ?>
            </ul>

            <!-- Bouton pour ouvrir la modale d'ajout d'une adresse -->
            <button type="button" class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#addAddressModal">
                Ajouter une adresse
            </button>

            <h4 class="mt-5">Mes Moyens de Paiement</h4>
            <ul>
                <?php foreach ($paymentMethods as $payment): ?>
                    <li>
                        <?php echo ($payment['type'] == 'paypal') ? 'PayPal : ' . $payment['paypal_email'] : 'Carte : ' . $payment['numero_carte']; ?>
                        <form method="POST" action="" style="display:inline;">
                            <input type="hidden" name="payment_id" value="<?php echo $payment['id']; ?>">
                            <button type="submit" name="delete_payment" class="btn btn-link">Supprimer</button>
                        </form>
                    </li>
                <?php endforeach; ?>
            </ul>

            <!-- Bouton pour ouvrir la modale d'ajout d'un moyen de paiement -->
            <button type="button" class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#addPaymentModal">
                Ajouter un moyen de paiement
            </button>
        </div>

        <!-- Modale pour ajouter une adresse -->
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
                                <label for="adresse_complete" class="form-label">Adresse complète</label>
                                <input type="text" class="form-control" name="adresse_complete" required>
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
                            <button type="submit" name="add_address" class="btn btn-primary">Ajouter une adresse</button>
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

                            <button type="submit" name="add_payment" class="btn btn-primary">Ajouter un moyen de paiement</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </main>

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
        function showDeleteAccountVerification() {
            document.getElementById('verifyDeleteAccount').style.display = 'block';
        }

        function hideDeleteAccountVerification() {
            document.getElementById('verifyDeleteAccount').style.display = 'none';
        }
    </script>
</body>

</html>
