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