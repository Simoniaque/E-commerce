<?php
// user.php
session_start();
include('../config.php');
include('../functions.php');


if (!isset($_GET['id'])) {
    die('Utilisateur non trouvé.');
}
$userID = $_GET['id'];

$userToModify = getUserByID($con, $userID);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $isAdmin = isset($_POST['admin']) ? 1 : 0;
    $emailVerified = isset($_POST['emailVerified']) ? 1 : 0;

    if(EditUser($con, $userID ,$name, $email, $password, $isAdmin, $emailVerified)) {
        echo '<div class="alert alert-success" role="alert">Utilisateur modifié avec succès.</div>';
    } else {
        echo '<div class="alert alert-danger" role="alert">Erreur lors de la modification de l\'utilisateur.</div>';
    }

    $userToModify = getUserByID($con, $userID);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Utilisateur</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" href="../assets/img/logo-black.png" type="image/x-icon">
</head>
<body>
    <div class="container-fluid p-0">
        <div class="row g-0">
            <div class="col-md-2 p-0">
                <?php include 'navbar.php'; ?>
            </div>
            <div class="col-md-10 p-0">
                <?php include 'header.php'; ?>

                <div class="container mt-4">
                    <div id="alertContainer"></div>

                    <h1 class="mb-4">Modifier l'utilisateur</h1>
                    <form id="productForm" method="POST" enctype="multipart/form-data">
                        

                        <div class="mb-3">
                            <label for="name" class="form-label">Nom:</label>
                            <input type="text" id="name" name="name" class="form-control" value="<?php echo $userToModify['nom'];?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email:</label>
                            <input type="email" id="email" name="email" class="form-control" step="0.01" required value="<?php echo $userToModify['email']; ?>">
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Mot de passe:</label>
                            <input type="text" id="password" name="password" class="form-control" value="">
                        </div>
                        
                        <div class="mb-3">
                            <?php $isAdmin = $userToModify['est_admin'] == 1 ? 'checked' : ''; ?>
                            <input type="checkbox" id="admin" name="admin" class="" <?php echo $isAdmin?>>
                            <label for="admin" class="form-label">Est admin</label>
                        </div>

                        <div class="mb-3">
                            <?php $checkedMail = $userToModify['mail_verifie'] == 1 ? 'checked' : ''; ?>
                            <input type="checkbox" id="emailVerified" name="emailVerified" class="" <?php echo $checkedMail?>>
                            <label for="emailVerified" class="form-label">A vérifié son adresse mail</label>
                        </div>
                        

                        <button type="submit" class="btn btn-primary">Modifier l'utilisateur</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
