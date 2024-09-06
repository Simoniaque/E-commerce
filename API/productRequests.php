<?php

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

    $products = array();
    foreach ($productsIDs as $productID) {
        $product = GetActiveProductByID($pdo, $productID['produit_id']);
        if ($product) {
            array_push($products, $product);
        }
    }

    return $products;
}

function GetActiveProductByID($pdo, $productID) {
    $query = "SELECT * FROM produits WHERE id = :productID AND est_actif = 1";
    $statement = $pdo->prepare($query);
    $statement->bindParam(':productID', $productID, PDO::PARAM_INT);

    if (!@$statement->execute()) {
        $errorInfo = $statement->errorInfo();
        $errorMessage = json_encode($errorInfo[2]);
        echo "<script>console.error($errorMessage);</script>";
        return false;
    }

    return $statement->fetch(PDO::FETCH_ASSOC);
}
