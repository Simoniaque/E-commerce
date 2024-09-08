<?php

function GetUserOrderByNumber($pdo, $userID, $orderNumber, $activeOnly = 1) {

    //do a pdo request
    //like this :  $query = "SELECT * FROM produits WHERE est_actif >= :activeOnly";
    /*$statement = $pdo->prepare($query);
    $statement->bindParam(':activeOnly', $activeOnly, PDO::PARAM_INT);

    if (!@$statement->execute()) {
        $errorInfo = $statement->errorInfo();
        $errorMessage = json_encode($errorInfo[2]);
        echo "<script>console.error($errorMessage);</script>";
        return false;
    }

    return $statement->fetchAll(PDO::FETCH_ASSOC);*/

    
    $query = "SELECT * FROM commandes WHERE numero = :orderNumber AND utilisateur_id = :userID AND est_actif >= :activeOnly";

    $statement = $pdo->prepare($query);
    $statement->bindParam(':orderNumber', $orderNumber, PDO::PARAM_INT);
    $statement->bindParam(':userID', $userID, PDO::PARAM_INT);
    $statement->bindParam(':activeOnly', $activeOnly, PDO::PARAM_INT);

    if (!@$statement->execute()) {
        $errorInfo = $statement->errorInfo();
        $errorMessage = json_encode($errorInfo[2]);
        echo "<script>console.error($errorMessage);</script>";
        return false;
    }

    return $statement->fetchAll(PDO::FETCH_ASSOC);
}


function GetUserOrdersByNewest($pdo, $userID){
    $query = "SELECT * FROM commandes WHERE utilisateur_id = :userID ORDER BY date_creation DESC";

    $statement = $pdo->prepare($query);
    $statement->bindParam(':userID', $userID, PDO::PARAM_INT);

    if (!@$statement->execute()) {
        $errorInfo = $statement->errorInfo();
        $errorMessage = json_encode($errorInfo[2]);
        echo "<script>console.error($errorMessage);</script>";
        return false;
    }

    return $statement->fetchAll(PDO::FETCH_ASSOC);
}

?>