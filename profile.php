<?php
session_start();

include("config.php");
include("functions.php");

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    die;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_SESSION['user_id'];

    // Si la requête est pour supprimer le compte
    $input = json_decode(file_get_contents('php://input'), true);
    if (isset($input['action']) && $input['action'] === 'deleteAccount') {
        if (deleteUser($con, $id)) {
            session_destroy();
            echo "User deleted successfully";
        } else {
            echo "Error deleting user";
        }
        exit;
    }

    // Sinon, c'est une mise à jour des informations de l'utilisateur
    $name = $_POST['name'];
    $phoneNumber = $_POST['phoneNumber'];
    $address = $_POST['address'];
    $postalCode = $_POST['postalCode'];
    $country = $_POST['country'];
    $city = $_POST['city'];
    $email = $_POST['email'];

    if (EditUserInfo($con, $id, $name, $phoneNumber, $address, $postalCode, $country, $city, $email)) {
        echo "User info updated successfully";
    } else {
        echo "Error updating user info";
    }
    exit;
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
                body: JSON.stringify({ action: 'deleteAccount' }),
                headers: {
                    'Content-Type': 'application/json'
                }
            })
                .then(response => response.text())
                .then(data => {
                    console.log(data);
                    if (data === "User deleted successfully") {
                        alert("Account deleted successfully");
                        window.location.href = 'index.php'; // Redirection vers la page d'accueil
                    } else {
                        alert("Error deleting account");
                    }
                })
                .catch(error => {
                    console.error(error);
                    alert("Error deleting account");
                });
        }

        function EditUserInfo() {
            const formData = new FormData();
            formData.append('name', document.getElementById("nameInput").value);
            formData.append('phoneNumber', document.getElementById("phoneNumberInput").value);
            formData.append('address', document.getElementById("addressInput").value);
            formData.append('postalCode', document.getElementById("postalCodeInput").value);
            formData.append('country', document.getElementById("countryInput").value);
            formData.append('city', document.getElementById("cityInput").value);
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
    </script>
</head>

<body>
    <main>
        <?php include "header.php"; ?>

        <?php
        $userData = checkLogin($con);
        $id = $userData['id'];

        $userInfo = getUserInfo($con, $id);

        $name = $userData['nom'];
        $phoneNumber = $userInfo['telephone'];
        $address = $userInfo['adresse'];
        $postalCode = $userInfo['code_postal'];
        $country = $userInfo['pays'];
        $city = $userInfo['ville'];
        $email = $userData['email'];



        echo ("<div class='container rounded bg-white mt-5 mb-5'>
        <div class='row'>
            <div class='col-md-6 border-right'>
                <div class='p-3 py-5'>
                    <div class='d-flex justify-content-between align-items-center mb-3'>
                        <h4 class='text-right'>Informations du compte</h4>
                    </div>
                    <div class='row mt-3'>
                        <div class='col-md-12 mb-3'>
                            <label class='labels'>Nom</label>
                            <input type='text' id='nameInput' class='form-control' placeholder='$name' value=''>
                        </div>
                        <hr />
                        <div class='col-md-12 mb-3'>
                            <label class='labels'>Numéro de téléphone</label>
                            <input type='text' id='phoneNumberInput' class='form-control' placeholder='$phoneNumber' value=''>
                        </div>
                        <hr />
                        <div class='col-md-12 mb-3'>
                            <label class='labels'>Adresse postale</label>
                            <input type='text' id='addressInput' class='form-control' placeholder='$address' value=''>
                        </div>
                        <hr />
                        <div class='col-md-12 mb-3'>
                            <label class='labels'>Code postal</label>
                            <input type='text' id='postalCodeInput' class='form-control' placeholder='$postalCode' value=''>
                        </div>
                        <hr />
                        <div class='col-md-12 mb-3'>
                            <label class='labels'>Pays</label>
                            <input type='text' id='countryInput' class='form-control' placeholder='$country' value=''>
                        </div>
                        <hr />
                        <div class='col-md-12 mb-3'>
                            <label class='labels'>Ville</label>
                            <input type='text' id='cityInput' class='form-control' placeholder='$city' value=''>
                        </div>
                        <hr />
                        <div class='col-md-12 mb-3'>
                            <label class='labels'>Adresse Mail</label>
                            <input type='text' id='emailInput' class='form-control' placeholder='$email' value=''>
                        </div>
                    </div>
                    <div class='mt-5 text-center'>
                        <button class='btn btn-dark profile-button' type='button' onclick='EditUserInfo()'>Sauvegarder</button>
                    </div>
                </div>
            </div>
            <div class='col-md-6'>
                <div class='p-3 py-5'>
                    <a href='myorders.php'><button  class='btn btn-dark col-12 mb-3'>Mes Commandes</button></a>
                    <button class='btn btn-danger col-12 mb-3' onclick='showDeleteAccountVerification()'>Supprimer mon compte</button>
                    <span id='verifyDeleteAccount' class='m-2' style='display: none;'>
                        <h5 class='mb-3'>Êtes-vous sûr de vouloir supprimer votre compte ?</h5>
                        <button class='btn btn-danger col-4 mb-3 me-3' onclick='deleteAccount()'>Oui</button>
                        <button class='btn btn-dark col-4 mb-3' onclick='hideDeleteAccountVerification()'>Non</button>
                    </span>
                </div>
            </div>
        </div>
    </div>");
        ?>
        
        <div class="push"></div>
    </main>
    <?php include "footer.php"; ?>
</body>

</html>