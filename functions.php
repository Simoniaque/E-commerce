<?php


function DisplayDismissibleAlert($message)
{
    echo "
    <div class='mb-0 alert alert-danger alert-dismissible fade show text-center' role='alert'>
        $message
        <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'>
    </button></div>";
}

function DisplayDismissibleSuccess($message)
{
    echo "
    <div class='mb-0 alert alert-success alert-dismissible fade show text-center' role='alert'>
        $message
        <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'>
    </button></div>";
}

function DisplayDismissibleWarning($message)
{
    echo "
    <div class='mb-0 alert alert-warning alert-dismissible fade show text-center' role='alert'>
        $message
        <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'>
    </button></div>";
}



function DisplayUnDismissibleWarning($message)
{
    echo "
    <div class='alert alert-warning show mb-0 text-center' role='alert'>
        $message
    </div>";
}

function DebugToConsole() {}

function ConvertCookieCartToDBCart($pdo, $userID)
{
    $cartCookieName = 'cart';
    $cart = isset($_COOKIE[$cartCookieName]) ? json_decode($_COOKIE[$cartCookieName], true) : [];

    foreach ($cart as $productID => $quantity) {
        if ($quantity > 0) {
            if (AddProductToCart($pdo, $userID, $productID, $quantity)) {
                echo "<script>console.log('Produit ajouté au panier.');</script>";
            } else {
                echo "<script>console.error('Erreur lors de l\'ajout du produit au panier.');</script>";
                return false;
            }
        }
    }

    setcookie($cartCookieName, "", time() - 3600, "/");

    return true;
}

function RemoveUnActiveProductsFromCookieCart($pdo)
{
    $cartCookieName = 'cart';
    $cart = isset($_COOKIE[$cartCookieName]) ? json_decode($_COOKIE[$cartCookieName], true) : [];
    $productRemoved = false;

    // Parcourir le panier avec les IDs de produits comme clés et les quantités comme valeurs
    foreach ($cart as $productID => $quantity) {
        $product = GetProductByID($pdo, $productID);
        if ($product === false) {
            echo "<script>console.log('" . json_encode($cart) . "');</script>";
            unset($cart[$productID]); // Suppression du produit avec cet ID
            $productRemoved = true;
            echo "<script>console.log('" . json_encode($cart) . "');</script>";
        }
    }

    // Mettre à jour le cookie après modification
    setcookie($cartCookieName, json_encode($cart), time() + 3600 * 24 * 30, "/");
    return $productRemoved;
}

function AdaptQuantityInCookieCart($pdo)
{
    $cartCookieName = 'cart';
    $cart = isset($_COOKIE[$cartCookieName]) ? json_decode($_COOKIE[$cartCookieName], true) : [];
    $quantityChanged = false;

    // Parcourir le panier avec les IDs de produits comme clés et les quantités comme valeurs
    foreach ($cart as $productID => $quantity) {
        $product = GetProductByID($pdo, $productID);
        if ($product !== false) {
            if ($product['stock'] == 0) {
                unset($cart[$productID]);
                $quantityChanged = true;

                echo "<script>setTimeout(function(){window.location.reload();}, 100);</script>";
            } else {

                if ($quantity > $product['stock']) {
                    $cart[$productID] = $product['stock'];
                    $quantityChanged = true;

                    echo "<script>setTimeout(function(){window.location.reload();}, 100);</script>";
                }
            }
        }
    }

    // Mettre à jour le cookie après modification
    setcookie($cartCookieName, json_encode($cart), time() + 3600 * 24 * 30, "/");
    return $quantityChanged;
}



function show_404()
{

    echo "<script>window.location.href = '/E-Commerce/404.php';</script>";
    exit();
}
