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

if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];

    if (DeleteMessage($pdo, $id)) {
        DisplayDismissibleSuccess('Message supprimé avec succès.');
    } else {
        DisplayDismissibleAlert('Erreur lors de la suppression du message.');
    }    
}

if(isset($_GET['traiter_id'])) {
    $id = $_GET['traiter_id'];

    if (ProcessMessage($pdo, $id)) {
        DisplayDismissibleSuccess('Message traité avec succès.');
    } else {
        DisplayDismissibleAlert('Erreur lors du traitement du message.');
    }

}

$messages = GetMessages($pdo);

//Trier les message selon la date et selon s'ils ont déjà été traités
usort($messages, function($a, $b) {
    if ($a['traite'] == $b['traite']) {
        return $a['date_message'] <=> $b['date_message'];
    }
    return $a['traite'] <=> $b['traite'];
});

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages</title>
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
                    <h1>Liste des messages</h1>

                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead class="bg-dark text-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Nom</th>
                                    <th>Email</th>
                                    <th>Numéro</th>
                                    <th>Message</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                    <th>Traité</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($messages as $message): ?>
                                <tr>
                                    <td><?php echo $message['id']; ?></td>
                                    <td><?php echo $message['nom']; ?></td>
                                    <td><?php echo $message['email']; ?></td>
                                    <td><?php echo $message['telephone']; ?></td>
                                    <td><?php echo $message['message']; ?></td>
                                    <td><?php echo $message['date_message']; ?></td>
                                    <td>
                                        <?php
                                        if($message['traite'] == 0) {
                                            echo '<a class="btn btn-dark btn-sm mb-1" href="?traiter_id='.$message['id'].'">Marquer comme traité</a>';
                                        } 
                                        ?>
                                        <a class="btn btn-danger btn-sm" href="?delete_id=<?php echo $message['id']; ?>" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce message ?');">Supprimer</a>
                                    </td>
                                    <td class="bg-<?php echo $message['traite'] == 1 ? 'success' : 'danger'; ?>"></td>
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