<?php
session_start();
include_once ("config.php");
include_once ("functions.php");
include_once ("mail.php");
include_once "API/usersRequests.php";


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
            DisplayDismissibleAlert("Les mots de passe ne correspondent pas");

            $allowPasswordReset = true;
        } else {
            // Récupération de l'utilisateur par email
            $user = GetUserByEmail($pdo, $email);
            if (!$user) {
                $message = "Aucun compte associé à cette adresse email";
            } else {
                // Vérification du jeton de réinitialisation
                if (!CheckPasswordResetTokenValidity($pdo, $user['id'], $token)) {
                    $message = "Lien de réinitialisation invalide !";
                } else {
                    // Réinitialisation du mot de passe
                    if (ResetPassword($pdo, $user['id'], $password)) {
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
        $user = GetUserByEmail($pdo, $email);
        if (!$user) {
            $message = "Aucun compte associé à l'adresse mail : " . $email;
        } else {
            // Vérification de la présence du jeton
            if (isset($_GET['token'])) {
                $token = $_GET['token'];
                if (!CheckPasswordResetTokenValidity($pdo, $user['id'], $token)) {
                    $message = "Lien de réinitialisation invalide !";
                } else {
                    $allowPasswordReset = true;
                }
            } else {
                // Envoi de l'email de réinitialisation
                SendMailResetPassword($user['email'], $user['nom'], GenerateURLResetPassword($pdo, $user['id']));
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
    <title>Réinitialisation</title>
    <link rel="icon" type="image/x-icon" href="assets/img/logo-black.png" />

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <link rel="stylesheet" href="assets/css/style.css" />
    <style>
        .password-requirements {
            font-size: 0.875em;
            color: #6c757d;
        }
        .password-requirements p {
            margin: 0;
            padding: 0.2em 0;
        }
        .password-requirements span {
            display: inline-block;
            width: 100%;
            transition: opacity 0.3s ease;
        }
        .password-requirements span.hidden {
            opacity: 0;
            display: none;
        }
    </style>
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
                                <input id='password' name='password' type='password' class='form-control' required>
                                <div id='passwordHelp' class='password-requirements'>
                                    <p><span id='length' class='invalid'>Doit contenir au moins 8 caractères</span></p>
                                    <p><span id='uppercase' class='invalid'>Doit contenir au moins une majuscule</span></p>
                                    <p><span id='number' class='invalid'>Doit contenir au moins un chiffre</span></p>
                                    <p><span id='special' class='invalid'>Doit contenir au moins un caractère spécial</span></p>
                                </div>
                            </div>
                            <div class='form-group mb-3'>
                                <label>Confirmer mot de passe</label>
                                <input id='password_confirm' name='password_confirm' type='password' class='form-control' required>
                            </div>
                            <input type='hidden' name='email' value='$email'>
                            <input type='hidden' name='token' value='$token'>
                            <button type='submit' class='btn btn-dark mb-2'>Réinitialiser mot de passe</button>
                            <div class='form-group form-check mb-3'>
                                <input type='checkbox' class='form-check-input' id='togglePassword'>
                                <label class='form-check-label'>Afficher mot de passe</label>
                            </div>
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
    <script>
        function validateForm() {
            const password = document.querySelector('#password').value;
            const passwordConfirm = document.querySelector('#passwordConfirm').value;

            if (password !== passwordConfirm) {
                alert('Les mots de passe ne correspondent pas.');
                return false;
            }

            if (!isValidPassword(password)) {
                alert('Le mot de passe ne respecte pas les critères de sécurité.');
                return false;
            }

            return true;
        }

        function isValidPassword(password) {
            const lengthCriteria = /(?=.{8,})/;
            const uppercaseCriteria = /(?=.*[A-Z])/;
            const numberCriteria = /(?=.*\d)/;
            const specialCriteria = /(?=.*[@$!%*?&_])/;

            const lengthValid = lengthCriteria.test(password);
            const uppercaseValid = uppercaseCriteria.test(password);
            const numberValid = numberCriteria.test(password);
            const specialValid = specialCriteria.test(password);

            document.querySelector('#length').className = lengthValid ? 'valid hidden' : 'invalid';
            document.querySelector('#uppercase').className = uppercaseValid ? 'valid hidden' : 'invalid';
            document.querySelector('#number').className = numberValid ? 'valid hidden' : 'invalid';
            document.querySelector('#special').className = specialValid ? 'valid hidden' : 'invalid';

            return lengthValid && uppercaseValid && numberValid && specialValid;
        }

        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');
        const passwordConfirm = document.querySelector('#password_confirm');

        togglePassword.addEventListener('click', () => {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            passwordConfirm.setAttribute('type', type);
        });

        document.querySelector('#password').addEventListener('input', () => {
            isValidPassword(document.querySelector('#password').value);
        });
    </script>

    <script>
        if ( window.history.replaceState ) {
            window.history.replaceState( null, null, window.location.href );
        }
    </script>

</body>

</html>