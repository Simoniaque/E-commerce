<?php


function GetCategories($pdo, $activeOnly = 1) {
    $query = "SELECT * FROM categories WHERE est_actif >= :activeOnly";
    $statement = $pdo->prepare($query);
    $statement->bindParam(':activeOnly', $activeOnly, PDO::PARAM_INT);

    if (!@$statement->execute()) {
        $errorInfo = $statement->errorInfo();
        $errorMessage = json_encode($errorInfo[2]);
        echo "<script>console.error($errorMessage);</script>";
        return false;
    }

    $categories = $statement->fetchAll(PDO::FETCH_ASSOC);

    return $categories;
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
        $category = GetCategoryByID($pdo, $categoryID['categorie_id']);
        if ($category) {
            array_push($categories, $category);
        }
    }


    return $categories;
}

function GetCategoryByID($pdo, $categoryID, $activeOnly = 1) {
    $query = "SELECT * FROM categories WHERE id = :categoryID AND est_actif >= :activeOnly";
    $statement = $pdo->prepare($query);
    $statement->bindParam(':categoryID', $categoryID, PDO::PARAM_INT);
    $statement->bindParam(':activeOnly', $activeOnly, PDO::PARAM_INT);

    if (!@$statement->execute()) {
        $errorInfo = $statement->errorInfo();
        $errorMessage = json_encode($errorInfo[2]);
        echo "<script>console.error($errorMessage);</script>";
        return false;
    }
    $category = $statement->fetch(PDO::FETCH_ASSOC);

    return $category;
}

function SetHighlightCategories($pdo, $category1, $category2, $category3, $category4) {

    //delete all previous highlights
    $query = "DELETE FROM categories_en_avant";
    $statement = $pdo->prepare($query);
    if (!@$statement->execute()){
        $errorInfo = $statement->errorInfo();
        $errorMessage = json_encode($errorInfo[2]);
        echo "<script>console.error($errorMessage);</script>";
        return false;
    }
    else{
        
        //add new highlights
        if (!AddHightlightCategory($pdo, $category1)) {
            return false;
        }
        if (!AddHightlightCategory($pdo, $category2)) {
            return false;
        }
        if (!AddHightlightCategory($pdo, $category3)) {
            return false;
        }
        if (!AddHightlightCategory($pdo, $category4)) {
            return false;
        }
        return true;
    }
}

function AddHightlightCategory($pdo, $categoryID) {
    $query = "INSERT INTO categories_en_avant (categorie_id) VALUES (:categoryID)";
    $statement = $pdo->prepare($query);
    $statement->bindParam(':categoryID', $categoryID, PDO::PARAM_INT);

    if (!@$statement->execute()) {
        $errorInfo = $statement->errorInfo();
        $errorMessage = json_encode($errorInfo[2]);
        echo "<script>console.error($errorMessage);</script>";
        return false;
    }

    return true;
}

function DisableCategory($pdo, $categoryID) {
    $query = "UPDATE categories SET est_actif = 0 WHERE id = :categoryID";
    $statement = $pdo->prepare($query);
    $statement->bindParam(':categoryID', $categoryID, PDO::PARAM_INT);

    if (!@$statement->execute()) {
        $errorInfo = $statement->errorInfo();
        $errorMessage = json_encode($errorInfo[2]);
        echo "<script>console.error($errorMessage);</script>";
        return false;
    }

    return true;
}

function UpdateCategory($pdo, $categoryID, $name, $description, $isActive) {
    $query = "UPDATE categories SET nom = :name, description = :description, est_actif = :isActive WHERE id = :categoryID";
    $statement = $pdo->prepare($query);
    $statement->bindParam(':categoryID', $categoryID, PDO::PARAM_INT);
    $statement->bindParam(':name', $name, PDO::PARAM_STR);
    $statement->bindParam(':description', $description, PDO::PARAM_STR);
    $statement->bindParam(':isActive', $isActive, PDO::PARAM_INT);

    if (!@$statement->execute()) {
        $errorInfo = $statement->errorInfo();
        $errorMessage = json_encode($errorInfo[2]);
        echo "<script>console.error($errorMessage);</script>";
        return false;
    }

    return true;
}

function AddCategory($pdo, $nom, $description){
    $query = "INSERT INTO categories (nom, description) VALUES (:nom, :description)";
    $statement = $pdo->prepare($query);
    $statement->bindParam(':nom', $nom, PDO::PARAM_STR);
    $statement->bindParam(':description', $description, PDO::PARAM_STR);

    if (!@$statement->execute()) {
        $errorInfo = $statement->errorInfo();
        $errorMessage = json_encode($errorInfo[2]);
        echo "<script>console.error($errorMessage);</script>";
        return false;
    }

    return $pdo->lastInsertId();
}
