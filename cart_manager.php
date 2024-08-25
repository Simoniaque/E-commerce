<?php
session_start();
include("config.php");
include("functions.php");

if (isset($_POST['productID']) && isset($_POST['quantity']) && isset($_POST['action'])) {
    $productID = intval($_POST['productID']);
    $quantity = intval($_POST['quantity']);
    $action = $_POST['action'];

    if (isset($_SESSION['user_id'])) {
        $userID = $_SESSION['user_id'];

        if ($action === 'add') {
            // Appelle la fonction pour ajouter le produit au panier
            addToCart($con, $userID, $productID, $quantity);
        } elseif ($action === 'update') {
            // Appelle la fonction pour mettre à jour la quantité
            updateCartQuantity($con, $userID, $productID, $quantity);
            
            // Si la quantité est 0, supprime le produit du panier
            if ($quantity == 0) {
                deleteProductFromCart($con, $userID, $productID);
            }
        }
    } else {
        // Gérer le panier en utilisant des cookies pour les utilisateurs non connectés
        $cartCookieName = 'cart';
        $cart = isset($_COOKIE[$cartCookieName]) ? json_decode($_COOKIE[$cartCookieName], true) : [];

        if ($action === 'add') {
            // Incrémente la quantité du produit dans le panier
            if (isset($cart[$productID])) {
                $cart[$productID] += $quantity;
            } else {
                $cart[$productID] = $quantity;
            }
        } elseif ($action === 'update') {
            if ($quantity == 0) {
                // Supprime le produit du panier
                unset($cart[$productID]);
            } else {
                // Met à jour la quantité du produit dans le panier
                $cart[$productID] = $quantity;
            }
        }

        // Met à jour le cookie
        setcookie($cartCookieName, json_encode($cart), time() + (86400 * 30), "/"); // Expire dans 30 jours
    }
} else {
    http_response_code(400);
}
?>
