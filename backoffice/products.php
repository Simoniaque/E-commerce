<?php

session_start();

include_once "../config.php";
include_once "../API/usersRequests.php";
include_once "../API/productsRequests.php";
include_once "../API/categoriesRequests.php";
include_once "../functions.php";

$user = GetCurrentUser($pdo);

if($user === false){
    header('Location: ../index.php');
    exit;
}

if($user['est_admin'] == 0){
    header('Location: ../index.php');
    exit;
}

$products = GetProducts($pdo, 0);

if (isset($_GET['delete_id'])) {
    $productID = intval($_GET['delete_id']);

    $result = DisableProduct($pdo, $productID);

    if ($result) {
        DisplayDismissibleSuccess("Produit désactivé avec succès.");
    } else {
        DisplayDismissibleAlert("Erreur lors de la suppression du produit.");
    }

    $products = GetProducts($pdo, 0);
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produits</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="icon" href="../assets/img/logo-black.png" type="image/x-icon">
</head>

<body>
    <div class="container-fluid p-0">
        <div class="row g-0">
            <!-- Sidebar -->
            <div class="col-md-2 p-0 bg-dark text-white">
                <?php include 'navbar.php'; ?>
            </div>

            <!-- Main content area -->
            <div class="col-md-10 p-0">
                <?php include 'header.php'; ?>

                <div class="container-fluid mt-4">
                    <h1>Liste des Produits</h1>
                    <a class="btn btn-dark mb-3" href="addproduct.php">Ajouter produit</a>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead class="bg-dark text-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Nom</th>
                                    <th>Description</th>
                                    <th>Prix</th>
                                    <th>Stock</th>
                                    <th>Catégorie</th>
                                    <th>Actions</th>
                                    <th>Est Actif</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($products as $product): ?>
                                    <tr>
                                        <td><?php echo $product['id']; ?></td>
                                        <td><?php echo $product['nom']; ?></td>
                                        <td><?php echo $product['description']; ?></td>
                                        <td><?php echo number_format($product['prix'], 2, ',', ' '); ?> €</td>
                                        <td><?php echo $product['stock']; ?></td>
                                        <td><?php echo GetProductCategory($pdo, $product['id'])['nom']; ?></td>
                                        <td>
                                            <a class="btn btn-dark btn-sm mb-2" href="product.php?id=<?php echo $product['id']; ?>">Modifier</a>
                                            <a class="btn btn-danger btn-sm mb-2" href="products.php?delete_id=<?php echo $product['id']; ?>" onclick="return confirm('Êtes-vous sûr de vouloir désactiver ce produit ?');">Désactiver</a>
                                        </td>
                                        <td class="bg-<?php echo $product['est_actif'] == 1 ? 'success' : 'danger'; ?>"></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    <script>
        if ( window.history.replaceState ) {
            window.history.replaceState( null, null, window.location.href );
        }
    </script>
</body>

</html>