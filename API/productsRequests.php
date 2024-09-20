<?php


function GetProducts($pdo, $activeOnly = 1){

    $query = "SELECT * FROM produits WHERE est_actif >= :activeOnly";
    $statement = $pdo->prepare($query);
    $statement->bindParam(':activeOnly', $activeOnly, PDO::PARAM_INT);

    if (!@$statement->execute()) {
        $errorInfo = $statement->errorInfo();
        $errorMessage = json_encode($errorInfo[2]);
        echo "<script>console.error($errorMessage);</script>";
        return false;
    }

    return $statement->fetchAll(PDO::FETCH_ASSOC);

}

function GetHighlightProducts($pdo) {
    $query = "SELECT * FROM produits_en_avant";
    $statement = $pdo->prepare($query);

    if (!@$statement->execute()) {
        $errorInfo = $statement->errorInfo();
        $errorMessage = json_encode($errorInfo[2]);
        echo "<script>console.error($errorMessage);</script>";
        return false;
    }

    $productsIDs = $statement->fetchAll(PDO::FETCH_ASSOC);
    if(!$productsIDs){
        return false;
    }

    $products = array();
    foreach ($productsIDs as $productID) {
        $product = GetProductByID($pdo, $productID['produit_id'], 1);
        if ($product) {
            array_push($products, $product);
        }
    }

    return $products;
}

function GetProductByID($pdo, $productID, $activeOnly = 1) {
    $query = "SELECT * FROM produits WHERE id = :productID AND est_actif >= :activeOnly";
    $statement = $pdo->prepare($query);
    $statement->bindParam(':productID', $productID, PDO::PARAM_INT);
    $statement->bindParam(':activeOnly', $activeOnly, PDO::PARAM_INT);

    if (!@$statement->execute()) {
        $errorInfo = $statement->errorInfo();
        $errorMessage = json_encode($errorInfo[2]);
        echo "<script>console.error($errorMessage);</script>";
        return false;
    }

    return $statement->fetch(PDO::FETCH_ASSOC);
}

function GetProductMaterials($pdo, $productID, $activeOnly = 1) {
    $query = "SELECT materiau_id as id FROM produits_materiaux WHERE produit_id = :productID";
    $statement = $pdo->prepare($query);
    $statement->bindParam(':productID', $productID, PDO::PARAM_INT);

    if (!@$statement->execute()) {
        $errorInfo = $statement->errorInfo();
        $errorMessage = json_encode($errorInfo[2]);
        echo "<script>console.error($errorMessage);</script>";
        return false;
    }

    $materialIDs = $statement->fetchAll(PDO::FETCH_ASSOC);
    if(!$materialIDs){
        return false;
    }

    $materials = array();
    foreach ($materialIDs as $materialID) {
        $material = GetMaterialByID($pdo, $materialID['id'],$activeOnly);
        if ($material) {
            array_push($materials, $material);
        }
    }


    return $materials;
}

function GetMaterialByID($pdo, $materialID, $activeOnly = 1) {
    $query = "SELECT * FROM materiaux WHERE id = :materialID AND est_actif >= :activeOnly";
    $statement = $pdo->prepare($query);
    $statement->bindParam(':materialID', $materialID, PDO::PARAM_INT);
    $statement->bindParam(':activeOnly', $activeOnly, PDO::PARAM_INT);

    if (!@$statement->execute()) {
        $errorInfo = $statement->errorInfo();
        $errorMessage = json_encode($errorInfo[2]);
        echo "<script>console.error($errorMessage);</script>";
        return false;
    }

    return $statement->fetch(PDO::FETCH_ASSOC);
}


function GetProductsByCategory($pdo, $categoryID, $activeOnly = 1) {
    $query = "SELECT * FROM produits WHERE categorie_id = :categoryID AND est_actif >= :activeOnly";
    $statement = $pdo->prepare($query);
    $statement->bindParam(':categoryID', $categoryID, PDO::PARAM_INT);
    $statement->bindParam(':activeOnly', $activeOnly, PDO::PARAM_INT);

    if (!@$statement->execute()) {
        $errorInfo = $statement->errorInfo();
        $errorMessage = json_encode($errorInfo[2]);
        echo "<script>console.error($errorMessage);</script>";
        return false;
    }

    return $statement->fetchAll(PDO::FETCH_ASSOC);
}


function UpdateProductStock($pdo, $productID, $newStock){
    $query = "UPDATE produits SET stock = :newStock WHERE id = :productID";
    $statement = $pdo->prepare($query);
    $statement->bindParam(':newStock', $newStock, PDO::PARAM_INT);
    $statement->bindParam(':productID', $productID, PDO::PARAM_INT);

    if (!@$statement->execute()) {
        $errorInfo = $statement->errorInfo();
        $errorMessage = json_encode($errorInfo[2]);
        echo "<script>console.error($errorMessage);</script>";
        return false;
    }
    return true;
}


function SetHighlightProducts($pdo, $product1, $product2, $product3, $product4){

    $query = "DELETE FROM produits_en_avant";
    $statement = $pdo->prepare($query);
    if (!@$statement->execute()) {
        $errorInfo = $statement->errorInfo();
        $errorMessage = json_encode($errorInfo[2]);
        echo "<script>console.error($errorMessage);</script>";
        return false;
    }

    if(AddHightlightProduct($pdo, $product1) == false){
        return false;
    }
    if(AddHightlightProduct($pdo, $product2) == false){
        return false;
    }
    if(AddHightlightProduct($pdo, $product3) == false){
        return false;
    }
    if(AddHightlightProduct($pdo, $product4) == false){
        return false;
    }

    return true;

}

function AddHightlightProduct($pdo, $productID){
    $query = "INSERT INTO produits_en_avant (produit_id) VALUES (:productID)";
    $statement = $pdo->prepare($query);
    $statement->bindParam(':productID', $productID, PDO::PARAM_INT);
    
    if (!@$statement->execute()) {
        $errorInfo = $statement->errorInfo();
        $errorMessage = json_encode($errorInfo[2]);
        echo "<script>console.error($errorMessage);</script>";
        return false;
    }

    return true;

}


function DisableProduct($pdo, $productID){
    $query = "UPDATE produits SET est_actif = 0 WHERE id = :productID";
    $statement = $pdo->prepare($query);
    $statement->bindParam(':productID', $productID, PDO::PARAM_INT);

    if (!@$statement->execute()) {
        $errorInfo = $statement->errorInfo();
        $errorMessage = json_encode($errorInfo[2]);
        echo "<script>console.error($errorMessage);</script>";
        return false;
    }

    return true;
}

function GetProductCategory($pdo, $productID){
    $query = "SELECT categorie_id FROM produits WHERE id = :productID limit 1";
    $statement = $pdo->prepare($query);
    $statement->bindParam(':productID', $productID, PDO::PARAM_INT);

    if (!@$statement->execute()) {
        $errorInfo = $statement->errorInfo();
        $errorMessage = json_encode($errorInfo[2]);
        echo "<script>console.error($errorMessage);</script>";
        return false;
    }

    $categoryID = $statement->fetch(PDO::FETCH_ASSOC);
    if($categoryID == false){
        return false;
    }

    return GetCategoryByID($pdo, $categoryID['categorie_id'], 0);

}

function UpdateProduct($pdo, $productID, $nom, $description, $prix, $stock, $categorie_id, $material, $isActive){
    $query = "UPDATE produits SET nom = :nom, description = :description, prix = :prix, stock = :stock, categorie_id = :categorie_id, est_actif = :isActive WHERE id = :productID";
    $statement = $pdo->prepare($query);
    $statement->bindParam(':nom', $nom, PDO::PARAM_STR);
    $statement->bindParam(':description', $description, PDO::PARAM_STR);
    $statement->bindParam(':prix', $prix, PDO::PARAM_STR);
    $statement->bindParam(':stock', $stock, PDO::PARAM_INT);
    $statement->bindParam(':categorie_id', $categorie_id, PDO::PARAM_INT);
    $statement->bindParam(':isActive', $isActive, PDO::PARAM_INT);
    $statement->bindParam(':productID', $productID, PDO::PARAM_INT);

    if (!@$statement->execute()) {
        $errorInfo = $statement->errorInfo();
        $errorMessage = json_encode($errorInfo[2]);
        echo "<script>console.error($errorMessage);</script>";
        return false;
    }

    $query = "DELETE FROM produits_materiaux WHERE produit_id = :productID";
    $statement = $pdo->prepare($query);
    $statement->bindParam(':productID', $productID, PDO::PARAM_INT);

    if (!@$statement->execute()) {
        $errorInfo = $statement->errorInfo();
        $errorMessage = json_encode($errorInfo[2]);
        echo "<script>console.error($errorMessage);</script>";
        return false;
    }

    foreach ($material as $materialID) {
        $query = "INSERT INTO produits_materiaux (produit_id, materiau_id) VALUES (:productID, :materialID)";
        $statement = $pdo->prepare($query);
        $statement->bindParam(':productID', $productID, PDO::PARAM_INT);
        $statement->bindParam(':materialID', $materialID, PDO::PARAM_INT);

        if (!@$statement->execute()) {
            $errorInfo = $statement->errorInfo();
            $errorMessage = json_encode($errorInfo[2]);
            echo "<script>console.error($errorMessage);</script>";
            return false;
        }
    }

    return true;
}

function GetMaterials($pdo, $activeOnly = 1){

    $query = "SELECT * FROM materiaux WHERE est_actif >= :activeOnly";
    $statement = $pdo->prepare($query);
    $statement->bindParam(':activeOnly', $activeOnly, PDO::PARAM_INT);

    if (!@$statement->execute()) {
        $errorInfo = $statement->errorInfo();
        $errorMessage = json_encode($errorInfo[2]);
        echo "<script>console.error($errorMessage);</script>";
        return false;
    }

    return $statement->fetchAll(PDO::FETCH_ASSOC);
}

function GetMaterialsIDByProduct($pdo, $productID){
    $query = "SELECT materiau_id FROM produits_materiaux WHERE produit_id = :productID";
    $statement = $pdo->prepare($query);
    $statement->bindParam(':productID', $productID, PDO::PARAM_INT);

    if (!@$statement->execute()) {
        $errorInfo = $statement->errorInfo();
        $errorMessage = json_encode($errorInfo[2]);
        echo "<script>console.error($errorMessage);</script>";
        return false;
    }
    
    $materials = $statement->fetchAll(PDO::FETCH_ASSOC);

    if(!$materials){
        echo "<script>console.error('No materials found for product $productID');</script>";
        return false;
    }

    $materialsID = array();
    foreach ($materials as $material) {
        array_push($materialsID, $material['materiau_id']);
    }

    return $materialsID;
}