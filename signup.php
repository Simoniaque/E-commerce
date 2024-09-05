<?php

use GuzzleHttp\Promise\Create;

session_start();
include_once "config.php";
include_once "functions.php";
include_once "mail.php";
include_once "API/usersRequests.php";

if (isset($_SESSION['user_id'])) {
    if(GetUserByID($pdo, $_SESSION['user_id'])){
        header("Location: index.php");
        die;
    }
    else
    {
        unset($_SESSION['user_id']);
    }
}

$redirectUrl = isset($_GET['redirect_to']) ? $_GET['redirect_to'] : 'index.php';

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $name = $_POST['userName'];
    $email = $_POST['userEmail'];
    $password = $_POST['userPassword'];
    $passwordConfirm = $_POST['userPasswordConfirm'];

    if (!empty($name) && !empty($email) && !empty($password) && !empty($passwordConfirm)) {

        if ($password !== $passwordConfirm) {
            DisplayDismissibleAlert("Les mots de passe ne correspondent pas.");
        } else {

            $userExists; 

            if (CheckUserExists($pdo, $email, $userExists) && $userExists) {
                DisplayDismissibleAlert("Cette adresse email est déjà utilisée.");
            } else {

                if (CreateUser($pdo, $name, $email, $password,$newUserId)) {
                    
                    $_SESSION['user_id'] = $newUserId;

                    $verificationURL = GenerateURLVerifyAccount($pdo, $newUserId);

                    if($verificationURL != false){
                        SendAccountCreatedEmail($email, $name, $verificationURL);
                    }
                    else{
                        DisplayDismissibleAlert("Une erreur est survenue lors de l'url de vérification.");

                    }
                    
                    ConvertCookieCartToDBCart($pdo, $newUserId);

                    DisplayDismissibleSuccess("Votre compte a été créé avec succès.");

                    echo "
                        <script type='text/javascript'>
                            setTimeout(function() {
                                window.location.href = '$redirectUrl';
                            }, 1500);
                        </script>";

                    
                } else {
                    DisplayDismissibleAlert("Une erreur est survenue lors de la création de votre compte.");
                }
            }
        }
    } else {
        DisplayDismissibleAlert("Veuillez remplir tous les champs.");
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>S'inscrire</title>
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
                <form class="p-4 shadow rounded-1 bg-light" method="post" onsubmit="return validateForm()">
                    <div class="form-group mb-3">
                        <label>Nom</label>
                        <input name="userName" type="text" class="form-control" required>
                    </div>
                    <div class="form-group mb-3">
                        <label>Adresse email</label>
                        <input name="userEmail" type="email" class="form-control" required>
                    </div>
                    <div class="form-group mb-3">
                        <label>Mot de passe</label>
                        <input name="userPassword" type="password" class="form-control" id="password" required>
                        <div id="passwordHelp" class="password-requirements">
                            <p><span id="length" class="invalid">Doit contenir au moins 8 caractères</span></p>
                            <p><span id="uppercase" class="invalid">Doit contenir au moins une majuscule</span></p>
                            <p><span id="number" class="invalid">Doit contenir au moins un chiffre</span></p>
                            <p><span id="special" class="invalid">Doit contenir au moins un caractère spécial</span></p>
                        </div>
                    </div>
                    <div class="form-group mb-3">
                        <label>Confirmez le mot de passe</label>
                        <input name="userPasswordConfirm" type="password" class="form-control" id="passwordConfirm" required>
                    </div>
                    <div class="form-group form-check mb-3">
                        <input type="checkbox" class="form-check-input" id="togglePassword">
                        <label class="form-check-label">Afficher mot de passe</label>
                    </div>
                    <button type="submit" class="btn btn-dark mb-2">S'inscrire</button>
                    <br />
                    <a href="login.php" class="text-xs link-opacity-25">Vous avez déjà un compte ? Connectez-vous ici</a>
                </form>
            </div>
        </div>
        <div class="push"></div>
    </main>

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
        const passwordConfirm = document.querySelector('#passwordConfirm');

        togglePassword.addEventListener('click', () => {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            passwordConfirm.setAttribute('type', type);
        });

        document.querySelector('#password').addEventListener('input', () => {
            isValidPassword(document.querySelector('#password').value);
        });
    </script>

    <?php include "footer.php"; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>

