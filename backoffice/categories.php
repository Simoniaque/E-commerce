<?php
session_start();

include_once "../config.php";
include_once "../API/usersRequests.php";
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

if (isset($_GET['delete_id'])) {
    $categoryID = intval($_GET['delete_id']);
    
    if (DisableCategory($pdo, $categoryID)) {
        DisplayDismissibleSuccess("Catégorie désactivée avec succès.");
    } else {
        DisplayDismissibleAlert("Erreur lors de la désactivation de la catégorie.");
    }
}

// Récupérer toutes les catégories
$categories = GetCategories($pdo,0);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catégories</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
                    <h1>Liste des Catégories</h1>
                    <a class="btn btn-dark mb-3" href="addcategory.php">Ajouter catégorie</a>

                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead class="bg-dark text-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Nom</th>
                                    <th>Description</th>
                                    <th>Image</th>
                                    <th>Actions</th>
                                    <th>Est Active</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($categories as $category): ?>
                                <tr>
                                    <td><?php echo $category['id']; ?></td>
                                    <td><?php echo $category['nom']; ?></td>
                                    <td><?php echo $category['description']; ?></td>
                                    <td>
                                        <img src="<?php echo 'https://imgproduitnewvet.blob.core.windows.net/imagescategories/' . $category['id'] . '.png'; ?>" alt="Image de la catégorie" style="width: 100px; height: auto;">
                                    </td>
                                    <td>
                                        <a class="btn btn-dark btn-sm mb-2" href="category.php?id=<?php echo $category['id']; ?>">Modifier</a>
                                        <a class="btn btn-danger btn-sm" href="?delete_id=<?php echo $category['id']; ?>" onclick="return confirm('Êtes-vous sûr de vouloir désactiver cette catégorie ?');">Désactiver</a>
                                    </td>
                                    
                                    <td class="bg-<?php echo $category['est_actif'] == 1 ? 'success' : 'danger'; ?>"></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        if ( window.history.replaceState ) {
            window.history.replaceState( null, null, window.location.href );
        }
    </script>
</body>
</html>