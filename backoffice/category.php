<?php
// category.php

include 'header.php';
include 'db.php';

$name = '';
$id = '';

if (isset($_GET['id'])) {
    // Modifier une catégorie existante
    $id = $_GET['id'];
    $query = "SELECT * FROM categories WHERE id = $id";
    $result = mysqli_query($conn, $query);
    $category = mysqli_fetch_assoc($result);
    $name = $category['name'];
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    
    if ($id) {
        // Mettre à jour la catégorie
        $query = "UPDATE categories SET name = '$name' WHERE id = $id";
    } else {
        // Ajouter une nouvelle catégorie
        $query = "INSERT INTO categories (name) VALUES ('$name')";
    }
    
    mysqli_query($conn, $query);
    header('Location: categories.php');
}

?>

<h1><?php echo $id ? 'Edit' : 'Add'; ?> Category</h1>

<form method="POST">
    <label for="name">Category Name</label>
    <input type="text" name="name" value="<?php echo $name; ?>" required>
    <button type="submit">Save</button>
</form>

<?php
include 'footer.php';
?>
