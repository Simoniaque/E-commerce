<?php

function GetUserOrderByNumber($pdo, $userID, $orderNumber, $activeOnly = 1) {
    
    $query = "SELECT * FROM commandes WHERE id = :orderNumber AND utilisateur_id = :userID AND est_actif >= :activeOnly LIMIT 1";

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

    return $statement->fetch(PDO::FETCH_ASSOC);
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

function GetOrderDetails($pdo, $orderNumber){

    $query = "SELECT * FROM details_commandes WHERE commande_id = :orderNumber";

    $statement = $pdo->prepare($query);
    $statement->bindParam(':orderNumber', $orderNumber, PDO::PARAM_INT);

    if (!@$statement->execute()) {
        $errorInfo = $statement->errorInfo();
        $errorMessage = json_encode($errorInfo[2]);
        echo "<script>console.error($errorMessage);</script>";
        return false;
    }

    return $statement->fetchAll(PDO::FETCH_ASSOC);

}

?>