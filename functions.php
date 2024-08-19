<?php

function debugToConsole($data) {
    $output = $data;
    if (is_array($output))
        $output = implode(',', $output);

    echo "<script>console.log('Debug Objects: " . $output . "' );</script>";
}

function checkLogin($con){
    if(isset($_SESSION['user_id'])){
        $id = $_SESSION['user_id'];
        $query = "SELECT * FROM utilisateurs WHERE id = '$id' limit 1";

        $result = mysqli_query($con,$query);

        if($result && mysqli_num_rows($result)> 0){
            $userData = mysqli_fetch_assoc($result);
            return $userData;
        }
    }
}

function getUserByID($con, $id){
    $query = "SELECT * FROM utilisateurs WHERE id = '$id' limit 1";

    $result = mysqli_query($con,$query);

    if($result && mysqli_num_rows($result)> 0){
        $userData = mysqli_fetch_assoc($result);
        return $userData;
    }
}

function getUserInfo($con, $userId){
    $query = "SELECT * FROM details_utilisateurs WHERE utilisateur_id = '$userId' limit 1";

    $result = mysqli_query($con,$query);

    if($result && mysqli_num_rows($result)> 0){
        $userInfo = mysqli_fetch_assoc($result);
        return $userInfo;
    }
}

function getProducts($con){
    $query = "SELECT * FROM produits";

    $result = mysqli_query($con,$query);

    if($result && mysqli_num_rows($result)> 0){
        $products = mysqli_fetch_all($result, MYSQLI_ASSOC);
        return $products;
    }
}

function getCategoriesList($con){
    $query = "SELECT * FROM categories";

    $result = mysqli_query($con,$query);

    if($result && mysqli_num_rows($result)> 0){
        return $result;
    }
}

function EditUserInfo($con, $id, $name, $phoneNumber, $address, $postalCode, $country, $city, $email){

    $userData = checkLogin($con);
    $userInfo = getUserInfo($con, $id);

    if($name == ""){
        $name = $userData['nom'];
    }
    if($phoneNumber == ""){
        $phoneNumber = $userInfo['telephone'];
    }
    if($address == ""){
        $address = $userInfo['adresse'];
    }
    if($postalCode == ""){
        $postalCode = $userInfo['code_postal'];
    }
    if($country == ""){
        $country = $userInfo['pays'];
    }
    if($city == ""){
        $city = $userInfo['ville'];
    }
    if($email == ""){
        $email = $userData['email'];
    }

    $query = "UPDATE utilisateurs SET nom = '$name', email = '$email' WHERE id = '$id'";

    $result = mysqli_query($con,$query);

    if($result){
        $query = "UPDATE details_utilisateurs SET telephone = '$phoneNumber', adresse = '$address', code_postal = '$postalCode', pays = '$country', ville = '$city' WHERE id = '$id'";

        $result = mysqli_query($con,$query);

        if($result){
            return true;
        }
    }
    return false;
}

function deleteUser($con, $id) {
    //Suppression de l'utilisateur
    $query = "DELETE FROM utilisateurs WHERE id = '$id'";
    $result = mysqli_query($con, $query);
    
    if($result){
        return true;
    }
    return false;
}


function getOrdersByUserIdNewest($con, $user_id){
    $query = "SELECT * FROM commandes WHERE utilisateur_id = '$user_id' ORDER BY date_creation DESC";

    $result = mysqli_query($con,$query);

    if($result && mysqli_num_rows($result)> 0){
        $orders = mysqli_fetch_all($result, MYSQLI_ASSOC);
        return $orders;
    }
}

function getOrderDetails($con, $order_id){
    $query = "SELECT * FROM details_commandes WHERE commande_id = '$order_id'";

    $result = mysqli_query($con,$query);

    if($result && mysqli_num_rows($result)> 0){
        $orderDetails = mysqli_fetch_all($result, MYSQLI_ASSOC);
        return $orderDetails;
    }
}

function getOrdersByUserIdAndNumber($con, $userId, $orderNumber) {
    $stmt = $con->prepare("SELECT * FROM commandes WHERE utilisateur_id = ? AND id = ?");
    $stmt->bind_param("ii", $userId, $orderNumber);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

function getProductById($con, $productId){
    $query = "SELECT * FROM produits WHERE id = '$productId'";

    $result = mysqli_query($con,$query);

    if($result && mysqli_num_rows($result)> 0){
        $product = mysqli_fetch_assoc($result);
        return $product;
    }
}

function getCategoryById($con, $category_id){
    $query = "SELECT * FROM categories WHERE id = '$category_id' limit 1";

    $result = mysqli_query($con,$query);

    if($result && mysqli_num_rows($result)> 0){
        $category = mysqli_fetch_assoc($result);
        return $category;
    }
}
function show_404() {
    header("HTTP/1.1 404 Not Found");
    exit();
}

function getProductsByCategory($con, $category_id){
    $query = "SELECT * FROM produits WHERE categorie_id = '$category_id'";

    $result = mysqli_query($con,$query);

    if($result && mysqli_num_rows($result)> 0){
        $products = mysqli_fetch_all($result, MYSQLI_ASSOC);
        return $products;
    }
}

function getCart($con, $userId){
    $query = "SELECT * FROM paniers WHERE utilisateur_id = '$userId'";

    $result = mysqli_query($con,$query);

    if($result && mysqli_num_rows($result)> 0){
        $cart = mysqli_fetch_assoc($result);
        return $cart;
    }
}

function addItemToCart(){

}

function getHighlightCategories($con){
    $query = "SELECT * FROM categories_en_avant";

    $result = mysqli_query($con,$query);

    if($result && mysqli_num_rows($result)> 0){
        
        //recupérer les categories selon les id dans $result
        $categoriesIDs = mysqli_fetch_all($result, MYSQLI_ASSOC);

        $categories = array();

        foreach ($categoriesIDs as $categoryID) {
            $category = getCategoryById($con, $categoryID['categorie_id']);
            $categories[] = $category;
        }

        return $categories;
    }
}

function getHighlightProducts($con){
    $query = "SELECT * FROM produits_en_avant";

    $result = mysqli_query($con,$query);


    if($result && mysqli_num_rows($result)> 0){
        $productsIDs = mysqli_fetch_all($result, MYSQLI_ASSOC);
        
        $products = array();

        foreach ($productsIDs as $productID) {
            $product = getProductById($con, $productID['produit_id']);
            $products[] = $product;
        }

        return $products;
    }
}

function getProductsByCategories($con, $idsCategories){
    $query = "SELECT * FROM produits WHERE categorie_id IN (".implode(',',$idsCategories).")";

    $result = mysqli_query($con,$query);

    if($result && mysqli_num_rows($result)> 0){
        $products = mysqli_fetch_all($result, MYSQLI_ASSOC);
        return $products;
    }
}


function getAllProducts($con){
    $query = "SELECT * FROM produits";

    $result = mysqli_query($con,$query);

    if($result && mysqli_num_rows($result)> 0){
        $products = mysqli_fetch_all($result, MYSQLI_ASSOC);
        return $products;
    }
}

function orderProductsByPrice($products){
    usort($products, function($a, $b) {
        return $a['prix'] <=> $b['prix'];
    });
    return $products;
}

function orderProductsByDate($products){
    usort($products, function($a, $b) {
        return $a['date_ajout'] <=> $b['date_ajout'];
    });
    return $products;
}

function filterProductsByMinPrice($products, $minPrice){
    $filteredProducts = array_filter($products, function($product) use ($minPrice){
        return $product['prix'] >= $minPrice;
    });
    return $filteredProducts;
}

function filterProductsByMaxPrice($products, $maxPrice){
    $filteredProducts = array_filter($products, function($product) use ($maxPrice){
        return $product['prix'] <= $maxPrice;
    });
    return $filteredProducts;
}

function getOrderById($con, $orderId){
    $query = "SELECT * FROM commandes WHERE id = '$orderId'";

    $result = mysqli_query($con,$query);

    if($result && mysqli_num_rows($result)> 0){
        $order = mysqli_fetch_assoc($result);
        return $order;
    }
}

function userAlreadyExists($con, $email){
    $query = "SELECT * FROM utilisateurs WHERE email = '$email'";

    $result = mysqli_query($con,$query);

    if($result && mysqli_num_rows($result)> 0){
        return true;
    }
    return false;
}

function addUser($con, $name, $email, $password){
    $query = "INSERT INTO utilisateurs (nom, email, mot_de_passe) VALUES ('$name', '$email', '$password')";

    $result = mysqli_query($con,$query);

    if($result){
        //retourner l'id de l'utilisateur ajouté 
        return mysqli_insert_id($con);
    }
    return 0;
}

function getUserByEmail($con,$email){
    $query = "SELECT * FROM utilisateurs WHERE email = '$email' limit 1";

    $result = mysqli_query($con,$query);

    if($result && mysqli_num_rows($result)> 0){
        $user = mysqli_fetch_assoc($result);
        return $user;
    }
}

function verifyAccount($con, $userID, $token){
    $query = "SELECT * FROM tokens_verification_mail WHERE utilisateur_id = '$userID' AND token = '$token' AND date_max > NOW()";

    $result = mysqli_query($con,$query);

    if($result && mysqli_num_rows($result)> 0){
        $verifyAccountQuery = "UPDATE utilisateurs SET mail_verifie = 1 WHERE id = '$userID'";
        $resultVerifyAccount = mysqli_query($con,$verifyAccountQuery);

        if($resultVerifyAccount && mysqli_affected_rows($con) > 0){
            //supprimer le token
            $deleteTokenQuery = "DELETE FROM tokens_verification_mail WHERE utilisateur_id = '$userID' AND token = '$token'";
            $resultDeleteToken = mysqli_query($con,$deleteTokenQuery);

            if($resultDeleteToken && mysqli_affected_rows($con) > 0){
                return true;
            }
        }
    }
    return false;

}

function generateURLVerifyAccount($con, $id){
    
    $query = "SELECT * FROM tokens_verification_mail WHERE utilisateur_id = '$id'";
    $result = mysqli_query($con,$query);
    
    if($result && mysqli_num_rows($result)> 0){
        $deleteTokenQuery = "DELETE FROM tokens_verification_mail WHERE utilisateur_id = '$id'";
        mysqli_query($con,$deleteTokenQuery);
    }

    $token = bin2hex(random_bytes(32));
    $dateMax = date('Y-m-d H:i:s', strtotime('+15 minutes'));

    $queryCreateToken = "INSERT INTO tokens_verification_mail (utilisateur_id, token, date_max) VALUES ('$id', '$token', '$dateMax')";
    $resultCreateToken = mysqli_query($con,$queryCreateToken);

    if (!$resultCreateToken) {
        echo "Error executing insert query: " . mysqli_error($con);
        return false; 
    }else{
        $url = WEBSITE_URL . "verifyaccount.php?email=".getUserByID($con, $id)['email']."&token=".$token; 
        return $url;
    }
}

function generateURLResetPassword($con, $id){
        
        $query = "SELECT * FROM tokens_reinitialisation_mdp WHERE utilisateur_id = '$id'";
        $result = mysqli_query($con,$query);
        
        if($result && mysqli_num_rows($result)> 0){
            $deleteTokenQuery = "DELETE FROM tokens_reinitialisation_mdp WHERE utilisateur_id = '$id'";
            mysqli_query($con,$deleteTokenQuery);
        }
    
        $token = bin2hex(random_bytes(32));
        $dateMax = date('Y-m-d H:i:s', strtotime('+15 minutes'));
    
        $queryCreateToken = "INSERT INTO tokens_reinitialisation_mdp (utilisateur_id, token, date_max) VALUES ('$id', '$token', '$dateMax')";
        $resultCreateToken = mysqli_query($con,$queryCreateToken);
    
        if (!$resultCreateToken) {
            echo "Error executing insert query: " . mysqli_error($con);
            return false; 
        }else{
            $url = WEBSITE_URL . "resetpassword.php?email=".getUserByID($con, $id)['email']."&token=".$token; 
            return $url;
        }

}

function checkPasswordResetToken($con, $userID, $token){
    $query = "SELECT * FROM tokens_reinitialisation_mdp WHERE utilisateur_id = '$userID' AND token = '$token' AND date_max > NOW()";

    $result = mysqli_query($con,$query);

    if($result && mysqli_num_rows($result)> 0){
        return true;
    }
    return false;
}

function resetPassword($con, $userID, $password){
    $query = "UPDATE utilisateurs SET mot_de_passe = '$password' WHERE id = '$userID'";

    $result = mysqli_query($con,$query);

    if($result && mysqli_affected_rows($con) > 0){

        //check si l'utilisateur a encore un token, si oui le supprimer 
        $queryCheckToken = "SELECT * FROM tokens_reinitialisation_mdp WHERE utilisateur_id = '$userID'";
        $resultCheckToken = mysqli_query($con, $queryCheckToken);

        if ($resultCheckToken && mysqli_num_rows($resultCheckToken) > 0) {
            $deleteTokenQuery = "DELETE FROM tokens_reinitialisation_mdp WHERE utilisateur_id = '$userID'";
            mysqli_query($con, $deleteTokenQuery);
        }

        return true;
    }

    return false;
}


function addToCart($con, $userId, $productId, $quantity){

    // Check if the user already has a cart
    $cart = getCart($con, $userId);

    if($cart){
        $cartID = $cart['id'];
    }else{
        // If the user doesn't have a cart, create a new one
        $query1 = "INSERT INTO paniers (utilisateur_id) VALUES ('$userId')";

        $result1 = mysqli_query($con,$query1);

        if($result1){
            $cartID = mysqli_insert_id($con);
        }
    }

    // Check if the product is already in the cart
    $query2 = "SELECT * FROM details_paniers WHERE panier_id = '$cartID' AND produit_id = '$productId'";

    $result2 = mysqli_query($con,$query2);

    if($result2 && mysqli_num_rows($result2)> 0){
        // If the product is already in the cart, update the quantity
        $cartDetail = mysqli_fetch_assoc($result2);
        $newQuantity = $cartDetail['quantite'] + $quantity;

        $query3 = "UPDATE details_paniers SET quantite = '$newQuantity' WHERE panier_id = '$cartID' AND produit_id = '$productId'";

        $result3 = mysqli_query($con,$query3);

        if($result3){
            return true;
        }
    }else{
        // If the product is not in the cart, add it with the specified quantity
        $query4 = "INSERT INTO details_paniers (panier_id, produit_id, quantite) VALUES ('$cartID', '$productId', '$quantity')";

        $result4 = mysqli_query($con,$query4);

        if($result4){
            return true;
        }
    }
    return false;

}

function getCartID($con, $userID){
    $query = "SELECT * FROM paniers WHERE utilisateur_id = '$userID' limit 1";

    $result = mysqli_query($con,$query);

    if($result && mysqli_num_rows($result)> 0){
        $cart = mysqli_fetch_assoc($result);
        return $cart['id'];
    }
}

function getCartDetails($con, $cartID){
    $query = "SELECT * FROM details_paniers WHERE panier_id = '$cartID'";

    $result = mysqli_query($con,$query);

    if($result && mysqli_num_rows($result)> 0){
        $cartDetails = mysqli_fetch_all($result, MYSQLI_ASSOC);
        return $cartDetails;
    }
}

function getCartProducts($con, $cartID){
    $query = "SELECT * FROM details_paniers WHERE panier_id = '$cartID'";

    $result = mysqli_query($con,$query);

    if($result && mysqli_num_rows($result)> 0){
        $cartDetails = mysqli_fetch_all($result, MYSQLI_ASSOC);
        $products = array();
        foreach ($cartDetails as $cartDetail) {
            $product = getProductById($con, $cartDetail['produit_id']);
            $product['quantite'] = $cartDetail['quantite'];
            $products[] = $product;
        }
        return $products;
    }
}

function updateCartQuantity($con, $userID, $productID, $quantity){

    
    $cartID = getCartID($con, $userID);

    $queryCheckExist = "SELECT * FROM details_paniers WHERE panier_id = '$cartID' AND produit_id = '$productID'";
    $resultCheckExist = mysqli_query($con,$queryCheckExist);

    if($resultCheckExist && mysqli_num_rows($resultCheckExist)> 0){
        $cartDetail = mysqli_fetch_assoc($resultCheckExist);
        $quantity += $cartDetail['quantite'];
    }else{
        //Ajouter le produit au panier
        return addToCart($con, $userID, $productID, $quantity);
    }

    $query = "UPDATE details_paniers SET quantite = '$quantity' WHERE panier_id = '$cartID' AND produit_id = '$productID'";

    $result = mysqli_query($con,$query);

    if($result){
        return true;
    }
    return false;
}

function calculateTotalCartPrice($con, $userID){
    $cartID = getCartID($con, $userID);

    $query = "SELECT SUM(prix * quantite) as total FROM details_paniers JOIN produits ON details_paniers.produit_id = produits.id WHERE panier_id = '$cartID'";

    $result = mysqli_query($con,$query);

    if($result && mysqli_num_rows($result)> 0){
        $total = mysqli_fetch_assoc($result);
        return $total['total'];
    }
}

function deleteProductFromCart($con, $userID, $productID){
    $cartID = getCartID($con, $userID);

    $query = "DELETE FROM details_paniers WHERE panier_id = '$cartID' AND produit_id = '$productID'";

    $result = mysqli_query($con,$query);

    if($result){
        return true;
    }
    return false;
}
