<?php $currentPage = urlencode($_SERVER['REQUEST_URI']); 

include_once 'config.php';
include_once 'API/usersRequests.php';
include_once 'API/categoriesRequests.php';
include_once 'API/cartRequests.php';

include_once 'functions.php';

?>

<header>
    <nav class="navbar navbar-light bg-light">
        <div class="container px-4 px-lg-5">
            <!-- Logo -->
            <a class="navbar-brand" href="index.php"><img src="assets/img/logo-black.png" height="50px" alt="Logo"></a>
            <div class="mx-auto mb-2 mb-lg-0" id="showWhenCollapsed">
                <form class="d-flex" method="get" action="search.php">
                    <input class="form-control me-2" type="search" name="search" placeholder="Recherche..." aria-label="Search">
                    <button class="btn btn-outline-dark" type="submit">
                        <i class="bi bi-search"></i>
                    </button>
                </form>
            </div>
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
                            $categories = GetCategories($pdo);

                            if(!$categories){
                                echo "<li><a class='dropdown-item' href='#'>Aucune catégorie trouvée</a></li>";
                            }else{
                                foreach($categories as $category){
                                    $categoryID = $category['id'];
                                    $categoryName = $category['nom'];
                                    echo "<li><a class='dropdown-item' href='category.php?id=$categoryID'>$categoryName</a></li>";
                                }
                            }
                            ?>
                        </ul>
                    </li>
                </ul>

                <!-- Barre de recherche -->
                <div class="mx-auto mb-2 mb-lg-0" id="hideWhenCollapsed">
                    <form class="d-flex" method="get" action="search.php">
                        <input class="form-control me-2" type="search" name="search" placeholder="Recherche..." aria-label="Search">
                        <button class="btn btn-outline-dark" type="submit">
                            <i class="bi bi-search"></i>
                        </button>
                    </form>
                </div>

                <!-- Menu utilisateur -->
                <div id="cart-container">
                    <?php
                    $user = GetCurrentUser($pdo);

                    if ($user) {
                        // Vérifier si le panier est vide
                        $isEmpty = IsCartEmpty($pdo, $user['id']);

                        echo "<a href='cart.php' class='btn btn-outline-dark position-relative' id='cart-button' type='submit'>
                                <i class='bi bi-cart-fill me-1'></i>
                                Panier
                                " . ($isEmpty ? "<span class='position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle'>
                                    <span class='visually-hidden'>New alerts</span>
                                </span>" : "") . "
                              </a>
                              <a class='btn btn-outline-dark ms-3' href='profile.php'>Mon compte</a>
                              <a class='btn btn-outline-dark ms-3' href='logout.php'>Se déconnecter</a>";
                    } else {
                        // Récupérer le panier depuis les cookies pour les utilisateurs non connectés
                        $cartCookieName = 'cart';
                        $cart = isset($_COOKIE[$cartCookieName]) ? json_decode($_COOKIE[$cartCookieName], true) : [];
                        $cartItemCount = count($cart);

                        echo "<a href='cart.php' class='btn btn-outline-dark position-relative' id='cart-button' type='submit'>
                                <i class='bi bi-cart-fill me-1'></i>
                                Panier
                                " . ($cartItemCount > 0 ? "<span class='position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle'>
                                    <span class='visually-hidden'>New alerts</span>
                                </span>" : "") . "
                              </a>
                              <a class='btn btn-outline-dark ms-3' href='login.php?redirect_to=$currentPage'>Se Connecter</a>
                              <a class='btn btn-outline-dark ms-3' href='signup.php?redirect_to=$currentPage'>S'inscrire</a>";
                    }
                    ?>
                </div>
            </div>
        </div>
    </nav>

    <?php
    if ($user && $user['mail_verifie'] == 0) {
        $email = $user['email'];
        DisplayUnDismissibleWarning("Votre adresse mail n'a pas été vérifiée. Veuillez vérifier votre boîte mail. <a class='text-black' href='verifyaccount.php?email=$email'>Renvoyer le mail de vérification.</a>");
    }
    ?>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            function handleNavbarCollapse() {
                const navbar = document.querySelector('.navbar');
                const collapseBreakpoint = 1400;
                if (window.innerWidth > collapseBreakpoint) {
                    navbar.classList.add('navbar-expand');
                    hideWhenCollapsed = document.getElementById('hideWhenCollapsed');
                    showWhenCollapsed = document.getElementById('showWhenCollapsed');
                    hideWhenCollapsed.style.display = 'block';
                    showWhenCollapsed.style.display = 'none';
                } else {
                    navbar.classList.remove('navbar-expand');
                    hideWhenCollapsed = document.getElementById('hideWhenCollapsed');
                    showWhenCollapsed = document.getElementById('showWhenCollapsed');
                    hideWhenCollapsed.style.display = 'none';
                    showWhenCollapsed.style.display = 'block';
                }
            }

            handleNavbarCollapse();

            window.addEventListener('resize', handleNavbarCollapse);
        });
    </script>
</header>
