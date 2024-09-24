<?php
session_start();

include_once "../config.php";
include_once "../API/usersRequests.php";
include_once "../API/categoriesRequests.php";
include_once "../functions.php";
include_once "../API/productsRequests.php";

$user = GetCurrentUser($pdo);

if ($user === false) {
    header('Location: ../index.php');
    exit;
}

if ($user['est_admin'] == 0) {
    header('Location: ../index.php');
    exit;
}

if(isset($_GET['material_name'])) {
    $materialName = $_GET['material_name'];

    if (empty($materialName)) {
        DisplayDismissibleAlert("Veuillez entrer un nom de matériau.");
    } else {
        if (AddMaterial($pdo, $materialName)) {
            DisplayDismissibleSuccess("Matériau ajouté avec succès.");
        } else {
            DisplayDismissibleAlert("Erreur lors de l'ajout du matériau.");
        }
    }

}

if (isset($_GET['deactivate_id'])) {
    $materialID = intval($_GET['deactivate_id']);

    if (DisableMaterial($pdo, $materialID)) {
        DisplayDismissibleSuccess("Matériau désactivé avec succès.");
    } else {
        DisplayDismissibleAlert("Erreur lors de la désactivation du matériau.");
    }
} else if (isset($_GET['activate_id'])) {
    $materialID = intval($_GET['activate_id']);

    if (ActivateMaterial($pdo, $materialID)) {
        DisplayDismissibleSuccess("Matériau activé avec succès.");
    } else {
        DisplayDismissibleAlert("Erreur lors de l'activation du matériau.");
    }
}

// Récupérer toutes les catégories
$materials = GetMaterials($pdo, 0);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Matériaux</title>
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
                    <!-- form pour ajouter un matériau, bouton et barre d'input-->
                    <form class="d-flex" method="GET">
                        <input class="form-control me-2" type="search" placeholder="Nom du matériau" aria-label="Search" name="material_name">
                        <button class="btn btn-dark" type="submit">Ajouter</button>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead class="bg-dark text-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Nom</th>
                                    <th>Actions</th>
                                    <th>Est Active</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($materials as $material): ?>
                                    <tr>
                                        <td>
                                            <?php echo $material['id']; ?>
                                        </td>
                                        <td>
                                            <?php echo $material['nom']; ?>
                                        </td>
                                        <td>
                                            <a class="btn btn-dark btn-sm" href="?activate_id=<?php echo $material['id']; ?>">Activer</a>
                                            <a class="btn btn-danger btn-sm" href="?deactivate_id=<?php echo $material['id']; ?>" onclick="return confirm('Êtes-vous sûr de vouloir désactiver ce matériau ?');">Désactiver</a>
                                        </td>

                                        <td class="bg-<?php echo $material['est_actif'] == 1 ? 'success' : 'danger'; ?>"></td>
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
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    </script>
</body>

</html>