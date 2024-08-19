<?php
session_start();
include("config.php");
include("functions.php");

if (isset($_POST['productID']) && isset($_POST['quantity'])) {
    $userID = $_SESSION['user_id'];
    $productID = intval($_POST['productID']);
    $quantity = intval($_POST['quantity']);

    // Appelle la fonction pour mettre à jour la quantité
    updateCartQuantity($con, $userID, $productID, $quantity);

    // Si la quantité est 0, supprime le produit du panier
    if ($quantity == 0) {
        deleteProductFromCart($con, $userID, $productID);
    }
}
?>