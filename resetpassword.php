<?php
session_start();
include("config.php");
include("functions.php");
include("mail.php");

$message = "";
$allowPasswordReset = false;
$resetMailForm = false;

// Traitement des requêtes POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Vérification de la présence de toutes les données nécessaires
    if (!isset($_POST['email'], $_POST['password'], $_POST['password_confirm'], $_POST['token'])) {
        $message = "Veuillez remplir tous les champs";
    } else {
        // Récupération des données du formulaire
        $email = $_POST['email'];
        $password = $_POST['password'];
        $password_confirm = $_POST['password_confirm'];
        $token = $_POST['token'];

        // Vérification de la correspondance des mots de passe
        if ($password !== $password_confirm) {
            echo "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
                    Les mots de passe ne correspondent pas
                    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                  </div>";

            $allowPasswordReset = true;
        } else {
            // Récupération de l'utilisateur par email
            $user = getUserByEmail($con, $email);
            if (!$user) {
                $message = "Aucun compte associé à cette adresse email";
            } else {
                // Vérification du jeton de réinitialisation
                if (!checkPasswordResetToken($con, $user['id'], $token)) {
                    $message = "Lien de réinitialisation invalide !";
                } else {
                    // Réinitialisation du mot de passe
                    if (resetPassword($con, $user['id'], $password)) {
                        $message = "Mot de passe réinitialisé avec succès !";
                    } else {
                        $message = "Une erreur est survenue lors de la réinitialisation de votre mot de passe.";
                    }
                }
            }
        }
    }

    // Traitement des requêtes GET
} else if (!empty($_GET)) {
    // Vérification de la présence de l'email
    if (!isset($_GET['email'])) {
        $resetMailForm = true;
    } else {
        // Récupération de l'utilisateur par email
        $email = $_GET['email'];
        $user = getUserByEmail($con, $email);
        if (!$user) {
            $message = "Aucun compte associé à l'adresse mail : " . $email;
        } else {
            // Vérification de la présence du jeton
            if (isset($_GET['token'])) {
                $token = $_GET['token'];
                if (!checkPasswordResetToken($con, $user['id'], $token)) {
                    $message = "Lien de réinitialisation invalide !";
                } else {
                    $allowPasswordReset = true;
                }
            } else {
                // Envoi de l'email de réinitialisation
                sendMailResetPassword($user['email'], $user['nom'], generateURLResetPassword($con, $user['id']));
                $message = "Un email de réinitialisation vous a été envoyé à l'adresse : " . $user['email'] . ".";
            }
        }
    }
} else {
    // Affichage du formulaire de réinitialisation par email
    $resetMailForm = true;
}

?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Se Connecter</title>
    <link rel="icon" type="image/x-icon" href="assets/img/logo-black.png" />

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <link rel="stylesheet" href="assets/css/style.css" />
</head>

<body>
    <main>
        <?php include "header.php"; ?>

        <div class="d-flex justify-content-center align-items-center" style="height: 80vh;">
            <div class="col-12 col-md-6 col-lg-4">
                <?php
                if ($allowPasswordReset) {
                    // Formulaire de réinitialisation du mot de passe
                    echo "<form class='p-4 shadow rounded-1 bg-light' method='post'>
                            <div class='form-group mb-3'>
                                <label>Nouveau mot de passe</label>
                                <input name='password' type='password' class='form-control'>
                            </div>
                            <div class='form-group mb-3'>
                                <label>Confirmer mot de passe</label>
                                <input name='password_confirm' type='password' class='form-control'>
                            </div>
                            <input type='hidden' name='email' value='$email'>
                            <input type='hidden' name='token' value='$token'>
                            <button type='submit' class='btn btn-dark mb-2'>Réinitialiser mot de passe</button>
                        </form>";
                } else if ($resetMailForm) {
                    // Formulaire d'envoi de l'email de réinitialisation
                    echo "<form class='p-4 shadow rounded-1 bg-light' method='get'>
                            <div class='form-group mb-3'>
                                <label>Adresse email</label>
                                <input name='email' type='email' class='form-control'>
                            </div>
                            <button type='submit' class='btn btn-dark mb-2'>Envoyer mail réinitialisation mot de passe</button>
                        </form>";
                } else {
                    // Affichage du message
                    echo "<div class='p-4 shadow rounded-1 bg-light text-center'>$message</div>";
                }
                ?>
            </div>
        </div>

        <div class="push"></div>
    </main>

    <?php include("footer.php"); ?>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>