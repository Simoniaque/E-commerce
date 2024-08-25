<?php
// categories.php

include 'header.php'; // Inclure l'en-tête commune
include 'config.php';

// Récupérer toutes les catégories
$query = "SELECT * FROM categories";
$result = mysqli_query($conn, $query);
?>

<h1>Categories</h1>

<a href="category.php">Add New Category</a>

<table>
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Actions</th>
    </tr>
    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo $row['nom']; ?></td>
            <td>
                <a href="category.php?id=<?php echo $row['id']; ?>">Edit</a>
                <a href="delete_category.php?id=<?php echo $row['id']; ?>">Delete</a>
            </td>
        </tr>
    <?php } ?>
</table>
