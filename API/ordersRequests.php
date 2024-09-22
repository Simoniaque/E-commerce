<?php

function GetOrders($pdo){
    $query = "SELECT * FROM commandes";

    $statement = $pdo->prepare($query);

    if (!@$statement->execute()) {
        $errorInfo = $statement->errorInfo();
        $errorMessage = json_encode($errorInfo[2]);
        echo "<script>console.error($errorMessage);</script>";
        return false;
    }

    return $statement->fetchAll(PDO::FETCH_ASSOC);
}

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

function GetOrderByNumber($pdo, $orderNumber, $activeOnly = 1) {
    
    $query = "SELECT * FROM commandes WHERE id = :orderNumber AND est_actif >= :activeOnly LIMIT 1";

    $statement = $pdo->prepare($query);
    $statement->bindParam(':orderNumber', $orderNumber, PDO::PARAM_INT);
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

function CheckoutOrder($pdo, $userID, $totalPrice, $billingAddressID, $shippingAddressID, $paymentMethodID,$productsIdAndQuantity, &$message){

    if(empty($productsIdAndQuantity)){
        $message = "Le panier est vide.";
        return false;
    }

    $orderID = CreateOrder($pdo, $userID, $totalPrice, $billingAddressID, $shippingAddressID, $paymentMethodID);

    if($orderID === false){
        $message = "Erreur lors de la commande";
        return false;
    }

    foreach($productsIdAndQuantity as $product){

        $productID = $product["id"];
        
        $productInfo = GetProductByID($pdo, $product["id"], 1);
        
        if($productInfo === false){
            $message = "Erreur lors de la commande";
            echo "<script>console.error(Produit introuvable, id = $productID);</script>";
            return false;
        }

        if($productInfo["stock"] < $product["quantity"]){
            $message = "Quantité insuffisante :  " . $productInfo["nom"];
            return false;
        }

        $productQuery = "INSERT INTO details_commandes (commande_id, produit_id, quantite) 
              VALUES (:orderID, :productID, :quantity)";
              
        $productStatement = $pdo->prepare($productQuery);

        $productStatement->bindParam(':orderID', $orderID, PDO::PARAM_INT);
        $productStatement->bindParam(':productID', $product["id"], PDO::PARAM_INT);
        $productStatement->bindParam(':quantity', $product["quantity"], PDO::PARAM_INT);

        if (!@$productStatement->execute()) {
            $errorInfo = $productStatement->errorInfo();
            $errorMessage = json_encode($errorInfo[2]);
            $message = "Erreur lors de la commande";
            echo "<script>console.error($errorMessage);</script>";

            $deleteOrderResult = DeleteOrder($pdo, $orderID);

            if($deleteOrderResult === false){
                $message .= "   Erreur lors de la suppression de la commande. Veuillez-contacter le support.";

            }

            return false;
        }

        $newStock = $productInfo["stock"] - $product["quantity"];
        $updateStockResult = UpdateProductStock($pdo, $productID, $newStock);

        if($updateStockResult === false){
            $message = "Erreur lors de la commande";
            echo "<script>console.error(Erreur lors de la mise à jour du stock du produit $productID);</script>";

            $deleteOrderResult = DeleteOrder($pdo, $orderID);

            if($deleteOrderResult === false){
                $message .= "   Erreur lors de la suppression de la commande. Veuillez-contacter le support.";

            }

            return false;

        }
    }

    $message = "Commande effectuée avec succès.";
    return $orderID;
}

function CreateOrder($pdo, $userID, $totalPrice, $billingAddressID, $shippingAddressID, $paymentMethodID){

    $query = "INSERT INTO commandes (utilisateur_id, prix_total,statut, adresse_de_facturation, adresse_de_livraison, moyen_de_paiement) 
              VALUES (:userID, :prix_total,1, :billingAddressID, :shippingAddressID, :paymentMethodID)";
              
    $statement = $pdo->prepare($query);
    $statement->bindParam(':userID', $userID, PDO::PARAM_INT);
    $statement->bindParam(':prix_total', $totalPrice, PDO::PARAM_INT);
    $statement->bindParam(':billingAddressID', $billingAddressID, PDO::PARAM_INT);
    $statement->bindParam(':shippingAddressID', $shippingAddressID, PDO::PARAM_INT);
    $statement->bindParam(':paymentMethodID', $paymentMethodID, PDO::PARAM_INT);

    if (!@$statement->execute()) {
        $errorInfo = $statement->errorInfo();
        $errorMessage = json_encode($errorInfo[2]);
        $message = "Erreur lors de la création de la commande.";
        echo "<script>console.error($errorMessage);</script>";
        return false;
    }

    $orderID = $pdo->lastInsertId();
    return $orderID;
}

function DeleteOrder($pdo, $orderID){
    
    $query = "DELETE FROM commandes WHERE id = :orderID";
    $statement = $pdo->prepare($query);
    $statement->bindParam(':orderID', $orderID, PDO::PARAM_INT);

    if (!@$statement->execute()) {
        $errorInfo = $statement->errorInfo();
        $errorMessage = json_encode($errorInfo[2]);
        echo "<script>console.error($errorMessage);</script>";
        return false;
    }

    return true;
}

function UpdateOrderStatus($pdo, $orderID, $status){
    
    $query = "UPDATE commandes SET statut = :status WHERE id = :orderID";
    $statement = $pdo->prepare($query);
    $statement->bindParam(':orderID', $orderID, PDO::PARAM_INT);
    $statement->bindParam(':status', $status, PDO::PARAM_INT);

    if (!@$statement->execute()) {
        $errorInfo = $statement->errorInfo();
        $errorMessage = json_encode($errorInfo[2]);
        echo "<script>console.error($errorMessage);</script>";
        return false;
    }

    return true;
}

function DeactivateOrder($pdo, $orderID){
        
    $query = "UPDATE commandes SET est_actif = 0 WHERE id = :orderID";
    $statement = $pdo->prepare($query);
    $statement->bindParam(':orderID', $orderID, PDO::PARAM_INT);

    if (!@$statement->execute()) {
        $errorInfo = $statement->errorInfo();
        $errorMessage = json_encode($errorInfo[2]);
        echo "<script>console.error($errorMessage);</script>";
        return false;
    }

    return true;
}

function ActivateOrder($pdo, $orderID){
        
    $query = "UPDATE commandes SET est_actif = 1 WHERE id = :orderID";
    $statement = $pdo->prepare($query);
    $statement->bindParam(':orderID', $orderID, PDO::PARAM_INT);

    if (!@$statement->execute()) {
        $errorInfo = $statement->errorInfo();
        $errorMessage = json_encode($errorInfo[2]);
        echo "<script>console.error($errorMessage);</script>";
        return false;
    }

    return true;
}


?>