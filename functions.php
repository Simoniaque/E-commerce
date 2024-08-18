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

function getUserInfo($con, $userId){
    $query = "SELECT * FROM infos_clients WHERE id = '$userId' limit 1";

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
        $query = "UPDATE infos_clients SET telephone = '$phoneNumber', adresse = '$address', code_postal = '$postalCode', pays = '$country', ville = '$city' WHERE id = '$id'";

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
?>
