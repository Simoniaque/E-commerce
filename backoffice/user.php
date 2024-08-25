<?php
// user.php

include 'header.php';
include 'db.php';

$username = '';
$email = '';
$id = '';

if (isset($_GET['id'])) {
    // Modifier un utilisateur existant
    $id = $_GET['id'];
    $query = "SELECT * FROM users WHERE id = $id";
    $result = mysqli_query($conn, $query);
    $user = mysqli_fetch_assoc($result);
    $username = $user['username'];
    $email = $user['email'];
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    
    if ($id) {
        // Mettre à jour l'utilisateur
        $query = "UPDATE users SET username = '$username', email = '$email' WHERE id = $id";
    } else {
        // Ajouter un nouvel utilisateur
        $query = "INSERT INTO users (username, email) VALUES ('$username', '$email')";
    }
    
    mysqli_query($conn, $query);
    header('Location: users.php');
}

?>

<h1><?php echo $id ? 'Edit' : 'Add'; ?> User</h1>

<form method="POST">
    <label for="username">Username</label>
    <input type="text" name="username" value="<?php echo $username; ?>" required>
    <label for="email">Email</label>
    <input type="email" name="email" value="<?php echo $email; ?>" required>
    <button type="submit">Save</button>
</form>

<?php
include 'footer.php';
?>
