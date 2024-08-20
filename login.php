<?php
session_start();
include("config.php");
include("functions.php");

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    die;
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $email = $_POST['userEmail'];
    $password = $_POST['userPassword'];

    //Cas un champ du form est vide
    if (empty($email) || empty($password)) {
        echo "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
                    Veuillez remplir tous les champs
                    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                  </div>";
    } else {
        $query = "SELECT * FROM utilisateurs WHERE email = '$email' limit 1";
        $result = mysqli_query($con, $query);

        //Cas aucun utilisateur trouvé avec cette adresse mail
        if (!($result && mysqli_num_rows($result) > 0)) {
            echo "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
                        Aucun compte associé à cette adresse email <a href='signup.php' class='alert-link'>Cliquez-ici pour vous créer un compte</a>
                        <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                      </div>";
        } else {
            $userData = mysqli_fetch_assoc($result);
            //Cas mot de pass incorrect
            if ($userData["mot_de_passe"] != $password) {
                echo "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
                            Mot de passe incorrect
                            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                          </div>";
            } else {

                //Cas connexion réussie
                $_SESSION['user_id'] = $userData['id'];
                header("Location: index.php");
                die;
            }
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
                    <button type="submit" class="btn btn-dark mb-2">Se connecter</button>
                    <br />
                    <a href="resetpassword.php" class="link-opacity-50">Mot de passe oublié</a>
                    <br />
                    <a href="signup.php" class="link-opacity-50">Nouveau sur New Vet ? Creez ici votre
                        compte</a>
                </form>
            </div>
        </div>

        <div class="push"></div>
    </main>

    <?php
    include("footer.php");
    ?>

    <script>
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');
        togglePassword.addEventListener('click', () => {

            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
        });
    </script>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>