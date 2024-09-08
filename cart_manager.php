<?php
session_start();

include_once "config.php";
include_once "functions.php";
include_once "API/usersRequests.php";
include_once "API/productsRequests.php";
include_once "API/cartsRequests.php";

$user = GetCurrentUser($pdo);

if ($user !== false) {
    if (isset($_POST['productID']) && isset($_POST['quantity'])) {
        $productID = intval($_POST['productID']);
        $quantity = intval($_POST['quantity']);
        $action = $_POST['action'] ?? 'add'; // Valeur par défaut 'add'

        if ($action === 'set') {
            if (UpdateProductQuantityInCart($pdo, $user['id'], $productID, $quantity)) {
                http_response_code(200);
                echo json_encode(['success' => 'Quantite du produit mise à jour dans le panier']);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Echec de la mise à jour de la quantité du produit dans le panier']);
            }
        } else {
            if (AddProductToCart($pdo, $user['id'], $productID, $quantity)) {
                http_response_code(200);
                echo json_encode(['success' => 'Produit ajouté au panier']);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Echec de l\'ajout du produit au panier']);
            }
        }
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Paramètres manquants']);
    }
} else {
    http_response_code(401);
    echo json_encode(['error' => 'Vous devez être connecté pour effectuer cette action']);
}
exit;