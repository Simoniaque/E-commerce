<header>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container px-4 px-lg-5">
            <a class="navbar-brand" href="index.php"><img src="assets/img/logo-black.png" height="50px"></img></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4">
                    <li class="nav-item"><a class="nav-link active" aria-current="page" href="index.php">Accueil</a></li>
                    <!--<li class="nav-item"><a class="nav-link" href="about.php">A propos</a></li>
                    <li class="nav-item"><a class="nav-link" href="#!">Contact</a></li>-->
                    
                    <li class="nav-item"><a class="nav-link" href="allproducts.php">Tous nos produits</a></li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">Catégories</a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            
                            <?php
                            $sqlrequest = "SELECT * FROM categories;";
                            $result = $con->query($sqlrequest);

                            while ($row = $result->fetch_assoc()) {
                                $categoryName = $row['nom'];

                                echo "<li><a class='dropdown-item' href=''>$categoryName</a></li>";
                            }
                            ?>
                        </ul>
                    </li>
                </ul>
                <?php
                $userData = checkLogin($con);
                if ($userData) {

                    $codeBadge ="";
                    if(getCart($con, $userData['id'])){
                        $codeBadge = "<span class='position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle'>
                                <span class='visually-hidden'>New alerts</span>
                            </span>";

                    }

                    echo "<button class='btn btn-outline-dark position-relative' type='submit'>
                            <i class='bi-cart-fill me-1'></i>
                            Panier
                            $codeBadge
                          </button>
                          <a class='btn btn-outline-dark ms-3' href='profile.php'>Mon compte</a>
                          <a class='btn btn-outline-dark ms-3' href='logout.php'>Se déconnecter</a>";
                } else {
                    echo "<a class='btn btn-outline-dark ms-3' href='login.php'>Se Connecter</a>
                          <a class='btn btn-outline-dark ms-3' href='signup.php'>S'inscrire</a>";
                }

                
                ?>
            </div>
        </div>
    </nav>

    <?php
    $user = checkLogin($con);
    if($user != null &&  $user['mail_verifie'] == 0){
        $email = $user['email'];
        //afficher un bandeau d'alerte
        echo "<div class='alert alert-warning show mb-0 text-center' role='alert'>
                Votre adresse mail n'a pas été vérifiée. Veuillez vérifier votre boîte mail. <a class='text-black' href='verifyaccount.php?email=$email'>Renvoyer le mail de vérification.</a>
              </div>";
    }?>
</header>