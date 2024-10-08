<?php
session_start();
include_once "config.php";
include_once "functions.php";
include_once "mail.php";
include_once "API/usersRequests.php";

$user = GetCurrentUser($pdo);
if ($user != null &&  $user['mail_verifie'] != 0) {
    header("Location: index.php");
    die;
}

$message = "";

if (!empty($_GET)) {
    if (isset($_GET['email'])) {

        $email = $_GET['email'];
        $user = GetUserByEmail($pdo, $email);
        if (!$user) {
            header("Location: index.php");
            die;
        }

        if (isset($_GET['token'])) {
            $token = $_GET['token'];
            if (CheckTokenAndVerifyUser($pdo, $user['id'], $token)) {
                $message = "Compte verifié avec succès !";
            } else {
                $message = "Lien de vérification invalide !";
            }
        } else {
            SendMailVerifyAccount($user['email'], $user['nom'], GenerateURLVerifyAccount($pdo, $user['id']));

            $message = "Un email de vérification vous a été envoyé à l'adresse : " . $user['email'] . ".";
        }
    } else {
        header("Location: index.php");
        die;
    }
} else {
    header("Location: index.php");
    die;
}

?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Vérification</title>
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
                    <?php
                    echo "<div class='text-center'> 
                            $message
                        </div>";
                    ?>
                </form>
            </div>
        </div>

        <div class="push"></div>
    </main>

    <?php
    include("footer.php");
    ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>