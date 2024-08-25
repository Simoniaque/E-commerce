<header>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container px-4 px-lg-5">
            <!-- Logo -->
            <a class="navbar-brand" href="index.php"><img src="assets/img/logo-black.png" height="50px" alt="Logo"></a>

            <!-- Bouton pour le menu mobile -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Zone de recherche et liens de navigation -->
            <div class="collapse navbar-collapse" id="navbarSupportedContent">


                <!-- Liens de navigation -->
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4">
                    <li class="nav-item"><a class="nav-link active" aria-current="page" href="index.php">Accueil</a></li>
                    <li class="nav-item"><a class="nav-link" href="search.php">Tous nos produits</a></li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Catégories</a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <?php
                            $sqlrequest = "SELECT * FROM categories;";
                            $result = $con->query($sqlrequest);

                            while ($row = $result->fetch_assoc()) {
                                $categoryName = $row['nom'];
                                $categoryID = $row['id'];

                                echo "<li><a class='dropdown-item' href='category.php?id=$categoryID'>$categoryName</a></li>";
                            }
                            ?>
                        </ul>
                    </li>
                </ul>

                <!-- Barre de recherche -->
                <div class="mx-auto mb-2 mb-lg-0 ">
                    <form class="d-flex" method="get" action="search.php">
                        <input class="form-control me-2" type="search" name="search" placeholder="Recherche..." aria-label="Search">
                        <button class="btn btn-outline-dark" type="submit">
                            <i class="bi bi-search"></i>
                        </button>
                    </form>
                </div>

                <!-- Menu utilisateur -->
                <?php
                $userData = checkLogin($con);
                if ($userData) {
                    $codeBadge = "";
                    if (getCart($con, $userData['id'])) {
                        $codeBadge = "<span class='position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle'>
                                <span class='visually-hidden'>New alerts</span>
                            </span>";
                    }

                    echo "<a href='cart.php' class='btn btn-outline-dark position-relative' type='submit'>
                            <i class='bi bi-cart-fill me-1'></i>
                            Panier
                            $codeBadge
                          </a>
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
    if ($user != null && $user['mail_verifie'] == 0) {
        $email = $user['email'];
        // Afficher un bandeau d'alerte
        echo "<div class='alert alert-warning show mb-0 text-center' role='alert'>
                Votre adresse mail n'a pas été vérifiée. Veuillez vérifier votre boîte mail. <a class='text-black' href='verifyaccount.php?email=$email'>Renvoyer le mail de vérification.</a>
              </div>";
    }
    ?>
</header>