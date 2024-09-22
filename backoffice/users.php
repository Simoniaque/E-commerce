<?php
session_start();

include_once "../config.php";
include_once "../API/usersRequests.php";
include_once "../functions.php";

$user = GetCurrentUser($pdo);

if ($user == false) {
    header('Location: ../index.php');
    exit;
}

if ($user['est_admin'] == 0) {
    header('Location: ../index.php');
    exit;
}


$users = GetUsers($pdo,0);

if(isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];
    if(DeactivateUser($pdo, $id)){
        DisplayDismissibleSuccess('Utilisateur désactivé avec succès.');
    }else{
        DisplayDismissibleAlert('Erreur lors de la désactivation de l\'utilisateur.');
    }

    $users = GetUsers($pdo,0);
    
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Utilisateurs</title>
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
                    <h1>Liste des clients</h1>
                    <a class="btn btn-dark mb-3" href="adduser.php">Ajouter utilisateur</a>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead class="bg-dark text-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Nom</th>
                                    <th>Email</th>
                                    <th>Actions</th>
                                    <th>Est actif</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($users as $row): ?>
                                <tr>
                                    <td><?php echo $row['id']; ?></td>
                                    <td><?php echo $row['nom']; ?></td>
                                    <td><?php echo $row['email']; ?></td>
                                    <td>
                                        <a class="btn btn-dark btn-sm" href="user.php?id=<?php echo $row['id']; ?>">Modifier</a>
                                        <a class="btn btn-danger btn-sm" href="users.php?action=delete&id=<?php echo $row['id']; ?>" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?');">Supprimer</a>
                                    </td>
                                    <td class="bg-<?php echo $row['est_actif'] == 1 ? 'success' : 'danger'; ?>"></td>
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

