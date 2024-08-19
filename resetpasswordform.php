<?php
session_start();
include("config.php");
include("functions.php");
include("mail.php");

if(isset($_SESSION['user_id'])){
    header("Location: index.php");
    die;
}

$mailSent = false;

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $email = $_POST['userEmail'];

    //Cas un champ du form est vide
    if (empty($email)) {
        echo "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
                    Veuillez saisir votre adresse mail
                    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                  </div>";
    } else {
        $userExists = userAlreadyExists($con,$email);

        if ($userExists) {
            $userName = getUserByEmail($con,$email)['nom'];
            $mailSent = sendMailResetPassword($email, $userName);
        } else {
            echo "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
                    Aucun compte associé à cette adresse email <a href='signup.php' class='alert-link'>Cliquez-ici pour vous créer un compte</a>
                    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                </div>";
        }
    }
}

?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Se Connecter</title>
    <link rel="icon" type="image/x-icon" href="../assets/logo-dark.png" />

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
                    if($mailSent){
                        echo "<div> 
                                Un mail pour réinitialiser votre mot de passe vous a été envoyé.
                            </div>";
                    }else{
                        echo "<div class='form-group mb-3'>
                                <label>Adresse email</label>
                                <input name='userEmail' type='email' class='form-control'>
                            </div>
                            <button type='submit' class='btn btn-dark mb-2'>Envoyer mail réinitialisation mot de passe</button>";
                    }
                    
                    ?>
                </form>
            </div>
        </div>

        <div class="push"></div>
    </main>

    <?php
    include("footer.php");
    ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
</body>

</html>