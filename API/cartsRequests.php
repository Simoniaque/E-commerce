<?php

include_once 'API/productsRequests.php';

function GetCart($pdo, $userID) {
    $query = "SELECT * FROM paniers WHERE utilisateur_id = :userID";
    $statement = $pdo->prepare($query);
    $statement->bindParam(':userID', $userID, PDO::PARAM_INT);

    if (!@$statement->execute()) {
        $errorInfo = $statement->errorInfo();
        $errorMessage = json_encode($errorInfo[2]);
        echo "<script>console.error($errorMessage);</script>";
        return false;
    }

    return $statement->fetch(PDO::FETCH_ASSOC);
}


function GetCartID($pdo, $userID) {
    $query = "SELECT id FROM paniers WHERE utilisateur_id = :userID";
    $statement = $pdo->prepare($query);
    $statement->bindParam(':userID', $userID, PDO::PARAM_INT);

    if (!@$statement->execute()) {
        $errorInfo = $statement->errorInfo();
        $errorMessage = json_encode($errorInfo[2]);
        echo "<script>console.error($errorMessage);</script>";
        return false;
    }

    return $statement->fetch(PDO::FETCH_ASSOC);
}


function IsCartEmpty($pdo, $userID) {
    $cartID = GetCartID($pdo, $userID);
    if (!$cartID) {
        return true;
    }

    $query = "SELECT COUNT(*) as count FROM details_paniers WHERE panier_id = :cartID";
    $statement = $pdo->prepare($query);
    $statement->bindParam(':cartID', $cartID['id'], PDO::PARAM_INT);

    if (!@$statement->execute()) {
        $errorInfo = $statement->errorInfo();
        $errorMessage = json_encode($errorInfo[2]);
        echo "<script>console.error($errorMessage);</script>";
        return true;
    }

    $cart = $statement->fetch(PDO::FETCH_ASSOC);
    return $cart['count'] == 0;
}


function GetCartProducts($pdo, $cartID) {
    $query = "SELECT * FROM details_paniers WHERE panier_id = :cartID";
    $statement = $pdo->prepare($query);
    $statement->bindParam(':cartID', $cartID, PDO::PARAM_INT);

    if (!@$statement->execute()) {
        $errorInfo = $statement->errorInfo();
        $errorMessage = json_encode($errorInfo[2]);
        echo "<script>console.error($errorMessage);</script>";
        return false;
    }

    $cartDetails = $statement->fetchAll(PDO::FETCH_ASSOC);
    $products = array();

    foreach ($cartDetails as $cartDetail) {
        $product = GetProductByID($pdo, $cartDetail['produit_id']);
        if ($product) {
            $product['quantite'] = $cartDetail['quantite'];
            $products[] = $product;
        }
    }

    return $products;
}


function AddProductToCart($pdo, $userID, $productID, $quantity){
    
    $cart = GetCart($pdo, $userID);

    if($cart){
        $cartID = $cart['id']; 
    }
    else{
        $cartID = CreateCart($pdo, $userID);
        if($cartID === false) return false;
    }

    $quantityInCart = CartContainsProduct($pdo, $cartID, $productID);
    if($quantityInCart === false){
        if(AddNewProductToCart($pdo, $cartID, $productID, $quantity) === false) return false;
    }
    else{
        $quantity += $quantityInCart["quantity"];
        if(UpdateProductQuantityInCart($pdo, $userID, $productID, $quantity) === false) return false;
    }

    return true;
}

function CreateCart($pdo, $userID){
    $query = "INSERT INTO paniers (utilisateur_id) VALUES (:userID)";
    $statement = $pdo->prepare($query);
    $statement->bindParam(':userID', $userID, PDO::PARAM_INT);

    if (!@$statement->execute()) {
        $errorInfo = $statement->errorInfo();
        $errorMessage = json_encode($errorInfo[2]);
        echo "<script>console.error($errorMessage);</script>";
        return false;
    }

    return $pdo->lastInsertId();
}

function CartContainsProduct($pdo, $cartID, $productID){
    
    $query = "SELECT quantite as quantity FROM details_paniers WHERE panier_id = :cartID AND produit_id = :productID";
    $statement = $pdo->prepare($query);
    $statement->bindParam(':cartID', $cartID, PDO::PARAM_INT);
    $statement->bindParam(':productID', $productID, PDO::PARAM_INT);

    if (!@$statement->execute()) {
        $errorInfo = $statement->errorInfo();
        $errorMessage = json_encode($errorInfo[2]);
        echo "<script>console.error($errorMessage);</script>";
        return false;
    }

    
    return $statement->fetch(PDO::FETCH_ASSOC);
}

function UpdateProductQuantityInCart($pdo, $userID, $productID, $newQuantity){

    $cart = GetCart($pdo, $userID);

    if($cart){
        $cartID = $cart['id']; 
    }
    else{
        $cartID = CreateCart($pdo, $userID);
        if($cartID === false) return false;
    }
    
    if($newQuantity < 0){
        return false;
    }

    $product = GetProductByID($pdo, $productID);

    if($product === false){
        return false;
    }

    if($newQuantity > $product['stock']){
        return false;
    }

    
    if($newQuantity == 0){
        return DeleteProductFromCart($pdo, $userID, $productID);
    }

    
    $query = "UPDATE details_paniers SET quantite = :quantity WHERE panier_id = :cartID AND produit_id = :productID";
    $statement = $pdo->prepare($query);
    $statement->bindParam(':quantity', $newQuantity, PDO::PARAM_INT);
    $statement->bindParam(':cartID', $cartID, PDO::PARAM_INT);
    $statement->bindParam(':productID', $productID, PDO::PARAM_INT);
    
    if (@!$statement->execute()) {
        $errorInfo = $statement->errorInfo();
        $errorMessage = json_encode($errorInfo[2]);
        echo "<script>console.error($errorMessage);</script>";
        return false;
    }

    return true;
}

function DeleteProductFromCart($pdo, $userID, $productID){
    $cart = GetCart($pdo, $userID);
    if(!$cart){
        return false;
    }

    $cartID = $cart['id'];

    $query = "DELETE FROM details_paniers WHERE panier_id = :cartID AND produit_id = :productID";
    $statement = $pdo->prepare($query);
    $statement->bindParam(':cartID', $cartID, PDO::PARAM_INT);
    $statement->bindParam(':productID', $productID, PDO::PARAM_INT);

    if (!@$statement->execute()) {
        $errorInfo = $statement->errorInfo();
        $errorMessage = json_encode($errorInfo[2]);
        echo "<script>console.error($errorMessage);</script>";
        return false;
    }

    return true;
}

//NE PAS APPELER SI LE PRODUIT EXISTE DEJA DANS LE PANIER
function AddNewProductToCart($pdo, $cartID, $productID, $quantity){

    if($quantity <= 0){
        return false;
    }

    $product = GetProductByID($pdo, $productID);
    if($product === false){
        echo "<script>console.error('Produit non trouvé');</script>";
        return false;
    }

    if($quantity > $product['stock']){
        echo "<script>console.error('Stock insuffisant');</script>";
        return false;
    }

    $query = "INSERT INTO details_paniers (panier_id, produit_id, quantite) VALUES (:cartID, :productID, :quantity)";
    $statement = $pdo->prepare($query);
    $statement->bindParam(':cartID', $cartID, PDO::PARAM_INT);
    $statement->bindParam(':productID', $productID, PDO::PARAM_INT);
    $statement->bindParam(':quantity', $quantity, PDO::PARAM_INT);

    if (!@$statement->execute()) {
        $errorInfo = $statement->errorInfo();
        $errorMessage = json_encode($errorInfo[2]);
        echo "<script>console.error($errorMessage);</script>";
        return false;
    }
}
