<?php

include_once 'API/cartRequests.php';

function DisplayDismissibleAlert($message){
    echo "
    <div class='mb-0 alert alert-danger alert-dismissible fade show' role='alert'>
        $message
        <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'>
    </button></div>";
}

function DisplayDismissibleSuccess($message){
    echo "
    <div class='mb-0 alert alert-success alert-dismissible fade show' role='alert'>
        $message
        <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'>
    </button></div>";
}

function DisplayDismissibleWarning($message){
    echo "
    <div class='mb-0 alert alert-warning alert-dismissible fade show' role='alert'>
        $message
        <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'>
    </button></div>";
}



function DisplayUnDismissibleWarning($message){
    echo "
    <div class='alert alert-warning show mb-0 text-center' role='alert'>
        $message
    </div>";

}

function DebugToConsole(){

}

function ConvertCookieCartToDBCart($pdo, $userID){
    $cartCookieName = 'cart';
    $cart = isset($_COOKIE[$cartCookieName]) ? json_decode($_COOKIE[$cartCookieName], true) : [];

    foreach ($cart as $productID => $quantity) {
        if ($quantity > 0) {
            if(AddProductToCart($pdo, $userID, $productID, $quantity)){
                echo "<script>console.log('Produit ajouté au panier.');</script>";
            }
            else{
                echo "<script>console.error('Erreur lors de l\'ajout du produit au panier.');</script>";
                return false;
            }
        }
    }

    setcookie($cartCookieName, "", time() - 3600, "/");

    return true;
}