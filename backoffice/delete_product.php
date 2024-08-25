<?php
// delete_product.php
include('config.php'); // Connexion à la base de données

$id = $_GET['id']; // ID du produit à supprimer

// Suppression du produit
$conn->query("DELETE FROM produits WHERE id = $id");

header("Location: products.php");
exit;
?>
