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
                    <li class="nav-item"><a class="nav-link" href="about.php">A propos</a></li>
                    <li class="nav-item"><a class="nav-link" href="#!">Contact</a></li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">Catégories</a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="#!">Tous les produits</a></li>
                            <li>
                                <hr class="dropdown-divider" />
                            </li>
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
                    echo "<button class='btn btn-outline-dark' type='submit'>
                            <i class='bi-cart-fill me-1'></i>
                            Panier
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
</header>