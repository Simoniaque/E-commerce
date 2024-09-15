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
    // Gestion du panier via les cookies si l'utilisateur n'est pas connecté
    $cartCookieName = 'cart';
    $cart = isset($_COOKIE[$cartCookieName]) ? json_decode($_COOKIE[$cartCookieName], true) : [];
    
    if (isset($_POST['productID']) && isset($_POST['quantity'])) {
        $productID = intval($_POST['productID']);
        $quantity = intval($_POST['quantity']);
        $action = $_POST['action'] ?? 'add';
    
        if ($quantity <= 0) {
            // Retirer l'article du cookie si la quantité est <= 0
            if (isset($cart[$productID])) {
                unset($cart[$productID]); // Suppression de l'article
                // Mise à jour du cookie après suppression
                if (empty($cart)) {
                    // Si le panier est vide après suppression, effacer le cookie
                    setcookie($cartCookieName, '', time() - 3600, "/");
                } else {
                    // Sinon, mettre à jour le cookie avec le nouveau panier
                    setcookie($cartCookieName, json_encode($cart), time() + 3600 * 24 * 30, "/");
                }
                http_response_code(200);
                echo json_encode(['success' => 'Produit retiré du panier']);
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'Produit non trouvé dans le panier']);
            }
            exit;
        }
    
        // Ajouter ou mettre à jour le produit dans le panier
        if ($action === 'set') {
            $cart[$productID] = $quantity; // Remplacer la quantité
        } else {
            // Ajouter la quantité à l'existant, ou ajouter le produit s'il n'existe pas
            if (isset($cart[$productID])) {
                $cart[$productID] += $quantity;
            } else {
                $cart[$productID] = $quantity;
            }
        }
    
        // Mettre à jour le cookie avec le nouveau panier
        setcookie($cartCookieName, json_encode($cart), time() + 3600 * 24 * 30, "/");
    
        http_response_code(200);
        echo json_encode(['success' => 'Produit ajouté au panier']);
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Paramètres manquants']);
    }
}
exit;