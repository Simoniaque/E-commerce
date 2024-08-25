<?php
session_start();
include("config.php");
include("functions.php");
include("mail.php");

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    die;
}

$redirectUrl = isset($_GET['redirect_to']) ? $_GET['redirect_to'] : 'index.php';

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $name = $_POST['userName'];
    $email = $_POST['userEmail'];
    $password = $_POST['userPassword'];

    if (!empty($name) && !empty($email) && !empty($password)) {

        $userAlreadyExists = userAlreadyExists($con, $email);

        if ($userAlreadyExists) {
            echo "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
                    Cette adresse email est déjà utilisée.
                    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                  </div>";
        } else {

            $newUserId = addUser($con, $name, $email, $password);

            if ($newUserId > 0) {
                sendAccountCreatedEmail($email, $name, generateURLVerifyAccount($con, $newUserId));
                $_SESSION['user_id'] = $newUserId;

                // Convertir le panier cookie en panier DB
                convertCookieCartToDBCart($con, $newUserId);

                header("Location: " . $redirectUrl);
                die;
            } else {
                echo "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
                    Une erreur est survenue lors de la création de votre compte.
                    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                  </div>";
            }
        }
    } else {
        echo "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
                    Veuillez remplir tous les champs
                    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                  </div>";
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
</head>

<body>

    <main>
        <?php include "header.php"; ?>

        <div class="d-flex justify-content-center align-items-center" style="height: 80vh;">
            <div class="col-12 col-md-6 col-lg-4">
                <form class="p-4 shadow rounded-1 bg-light" method="post">
                    <div class="form-group mb-3">
                        <label>Nom</label>
                        <input name="userName" type="text mb-3" class="form-control">
                    </div>
                    <div class="form-group mb-3">
                        <label>Adresse email</label>
                        <input name="userEmail" type="email" class="form-control">
                    </div>
                    <div class="form-group mb-3">
                        <label>Mot de passe</label>
                        <input name="userPassword" type="password" class="form-control" id="password">
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
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');
        togglePassword.addEventListener('click', () => {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
        });
    </script>

    <?php include "footer.php"; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>
