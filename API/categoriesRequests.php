<?php


function GetActiveCategories($pdo) {
    $query = "SELECT * FROM categories WHERE est_actif = 1";
    $statement = $pdo->prepare($query);

    if (!@$statement->execute()) {
        $errorInfo = $statement->errorInfo();
        $errorMessage = json_encode($errorInfo[2]);
        echo "<script>console.error($errorMessage);</script>";
        return false;
    }

    return $statement->fetchAll(PDO::FETCH_ASSOC);
}

function GetHighlightCategories($pdo) {
    $query = "SELECT * FROM categories_en_avant";
    $statement = $pdo->prepare($query);

    if (!@$statement->execute()) {
        $errorInfo = $statement->errorInfo();
        $errorMessage = json_encode($errorInfo[2]);
        echo "<script>console.error($errorMessage);</script>";
        return false;
    }

    $categoriesIDs = $statement->fetchAll(PDO::FETCH_ASSOC);

    $categories = array();
    
    foreach ($categoriesIDs as $categoryID) {
        $category = GetActiveCategoryByID($pdo, $categoryID['categorie_id']);
        if ($category) {
            array_push($categories, $category);
        }
    }


    return $categories;
}

function GetActiveCategoryByID($pdo, $categoryID) {
    $query = "SELECT * FROM categories WHERE id = :categoryID AND est_actif = 1";
    $statement = $pdo->prepare($query);
    $statement->bindParam(':categoryID', $categoryID, PDO::PARAM_INT);

    if (!@$statement->execute()) {
        $errorInfo = $statement->errorInfo();
        $errorMessage = json_encode($errorInfo[2]);
        echo "<script>console.error($errorMessage);</script>";
        return false;
    }

    return $statement->fetch(PDO::FETCH_ASSOC);
}
