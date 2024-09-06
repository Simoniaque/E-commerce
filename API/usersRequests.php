<?php

function GetCurrentUser($pdo, $activeOnly = 1) {
    if (isset($_SESSION['user_id'])) {
        $id = $_SESSION['user_id'];
        $query = "SELECT * FROM utilisateurs WHERE id = :id AND est_actif >= :activeOnly LIMIT 1 ";
        $statement = $pdo->prepare($query);
        $statement->bindParam(':id', $id, PDO::PARAM_INT);
        $statement->bindParam(':activeOnly', $activeOnly, PDO::PARAM_INT);

        if (!@$statement->execute()) {
            $errorInfo = $statement->errorInfo();
            $errorMessage = json_encode($errorInfo[2]);
            echo "<script>console.error($errorMessage);</script>";
            return false;
        }

        return $statement->fetch(PDO::FETCH_ASSOC);
    }
    return false;
}

function GetUserByID($pdo, $userID, $activeOnly = 1) {
    $query = "SELECT * FROM utilisateurs WHERE id = :userID AND est_actif >= :activeOnly LIMIT 1";
    $statement = $pdo->prepare($query);
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


function GetUserByEmail($pdo, $userEmail, $activeOnly = 1) {
    $query = "SELECT * FROM utilisateurs WHERE email = :email AND est_actif >= :activeOnly LIMIT 1 ";
    $statement = $pdo->prepare($query);
    
    $statement->bindParam(':email', $userEmail, PDO::PARAM_STR);
    $statement->bindParam(':activeOnly', $activeOnly, PDO::PARAM_INT);
    
    if (!@$statement->execute()) {
        $errorInfo = $statement->errorInfo();
        $errorMessage = json_encode($errorInfo[2]);

        echo "<script>console.error($errorMessage);</script>";
        
        
        return false;
    }

    $user = $statement->fetch(PDO::FETCH_ASSOC);
    return $user;
}


function CheckUserExistsAndIsActive($pdo, $email, &$exists, &$active){

    $exists = false;
    $active = false;

    $query = "SELECT * FROM utilisateurs WHERE email = :email";
    $statement = $pdo->prepare($query);
    $statement->bindParam(':email', $email, PDO::PARAM_STR);

    if (!@$statement->execute()) {
        $errorInfo = $statement->errorInfo();
        $errorMessage = json_encode($errorInfo[2]);

        echo "<script>console.error($errorMessage);</script>";
        
        return false;
    }

    $result = $statement->fetch(PDO::FETCH_ASSOC);
    
    if($result != false){
        $exists = true;

        if($result["est_actif"] == 1){
            $active = true;
        }
    }
    

    return true;
}

function CreateUser ($pdo, $name, $email, $password, &$newUserId){

    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    $query = "INSERT INTO utilisateurs (nom, email, mot_de_passe) VALUES (:name, :email, :password)";
    $statement = $pdo->prepare($query);
    $statement->bindParam(':name', $name, PDO::PARAM_STR);
    $statement->bindParam(':email', $email, PDO::PARAM_STR);
    $statement->bindParam(':password', $hashedPassword, PDO::PARAM_STR);

    if (!@$statement->execute()) {
        $errorInfo = $statement->errorInfo();
        $errorMessage = json_encode($errorInfo[2]);

        echo "<script>console.error($errorMessage);</script>";
        
        return false;
    }

    $newUserId = $pdo->lastInsertId();
    return true;
}

function GenerateURLVerifyAccount($pdo, $userID){

    if(UserAlreadyHasVerificationToken($pdo, $userID, $userToken) === false){
        
        echo "ici";
        return false;
    }

    //si l'utilisateur a déjà un token on le supprime
    if($userToken != false){
        if(DeleteVerificationToken($pdo, $userToken['id']) === false){
            
        echo "ici2";
            return false;
        }
    }

    $newTokenValue = bin2hex(random_bytes(32));
    $dateMax = date('Y-m-d H:i:s', strtotime('+15 minutes'));

    if(CreateVerificationToken($pdo, $userID, $newTokenValue, $dateMax) === true){
        $url = WEBSITE_URL . "verifyaccount.php?email=".GetUserByID($pdo, $userID)['email']."&token=".$newTokenValue; 
        return $url;
    }
}

function CreateVerificationToken($pdo, $id, $tokenValue, $dateMax){

    $query = "INSERT INTO tokens_verification_mail (utilisateur_id, token, date_max) VALUES (:id, :token, :dateMax)";

    $statement = $pdo->prepare($query);
    $statement->bindParam(':id', $id, PDO::PARAM_INT);
    $statement->bindParam(':token', $tokenValue, PDO::PARAM_STR);
    $statement->bindParam(':dateMax', $dateMax, PDO::PARAM_STR);

    if (!@$statement->execute()) {
        $errorInfo = $statement->errorInfo();
        $errorMessage = json_encode($errorInfo[2]);

        echo "<script>console.error($errorMessage);</script>";
        
        return false;
    }

    return true;

}

function DeleteVerificationToken($pdo, $id){

    $query = "DELETE FROM tokens_verification_mail WHERE id = :id";

    $statement = $pdo->prepare($query);
    $statement->bindParam(':id', $id, PDO::PARAM_INT);

    if (!@$statement->execute()) {
        $errorInfo = $statement->errorInfo();
        $errorMessage = json_encode($errorInfo[2]);

        echo "<script>console.error($errorMessage);</script>";
        
        return false;
    }

    return true;

}

function UserAlreadyHasVerificationToken($pdo, $userID, &$token){

    $query = "SELECT * FROM tokens_verification_mail WHERE utilisateur_id = :id";

    $statement = $pdo->prepare($query);
    $statement->bindParam(':id', $userID, PDO::PARAM_INT);

    if (!@$statement->execute()) {
        $errorInfo = $statement->errorInfo();
        $errorMessage = json_encode($errorInfo[2]);

        echo "<script>console.error($errorMessage);</script>";
        
        return false;
    }

    $token = $statement->fetch(PDO::FETCH_ASSOC);

    return true;
}


function DeactivateUser($pdo, $userID){

    $query = "UPDATE utilisateurs SET est_actif = 0 WHERE id = :id";

    $statement = $pdo->prepare($query);
    $statement->bindParam(':id', $userID, PDO::PARAM_INT);

    if (!@$statement->execute()) {
        $errorInfo = $statement->errorInfo();
        $errorMessage = json_encode($errorInfo[2]);

        echo "<script>console.error($errorMessage);</script>";
        
        return false;
    }

    return true;
}

function UpdateUserInfo($pdo, $id, $name, $email){
    
    $query = "UPDATE utilisateurs SET nom = :name, email = :email WHERE id = :id";

    $statement = $pdo->prepare($query);
    $statement->bindParam(':id', $id, PDO::PARAM_INT);
    $statement->bindParam(':name', $name, PDO::PARAM_STR);
    $statement->bindParam(':email', $email, PDO::PARAM_STR);

    if (!@$statement->execute()) {
        $errorInfo = $statement->errorInfo();
        $errorMessage = json_encode($errorInfo[2]);

        echo "<script>console.error($errorMessage);</script>";
        
        return false;
    }

    return true;
}

function AddUserAddress($pdo, $userId, $adresseComplete, $ville, $codePostal, $pays) {
    $query = "INSERT INTO adresses_utilisateurs (utilisateur_id, adresse_complète, ville, code_postal, pays) 
              VALUES (:userId, :adresseComplete, :ville, :codePostal, :pays)";

    $statement = $pdo->prepare($query);
    $statement->bindParam(':userId', $userId, PDO::PARAM_INT);
    $statement->bindParam(':adresseComplete', $adresseComplete, PDO::PARAM_STR);
    $statement->bindParam(':ville', $ville, PDO::PARAM_STR);
    $statement->bindParam(':codePostal', $codePostal, PDO::PARAM_STR);
    $statement->bindParam(':pays', $pays, PDO::PARAM_STR);

    if (!$statement->execute()) {
        $errorInfo = $statement->errorInfo();
        $errorMessage = json_encode($errorInfo[2]);

        echo "<script>console.error($errorMessage);</script>";
        
        return false;
    }

    return true;
}

function AddUserPaymentMethod($pdo, $userID, $payementType, $cardNumber, $cardName, $expirationDate, $cvv, $paypalEmail){
    $expirationDate = $expirationDate ? $expirationDate . '-01' : null;
    
    $query = "INSERT INTO moyens_paiement (utilisateur_id, type, numero_carte, nom_titulaire, date_expiration, cvv, paypal_email) 
              VALUES (:userID, :payementType, :cardNumber, :cardName, :expirationDate, :cvv, :paypalEmail)";

    $statement = $pdo->prepare($query);
    $statement->bindParam(':userID', $userID, PDO::PARAM_INT);
    $statement->bindParam(':payementType', $payementType, PDO::PARAM_STR);
    $statement->bindParam(':cardNumber', $cardNumber, PDO::PARAM_STR);
    $statement->bindParam(':cardName', $cardName, PDO::PARAM_STR);
    $statement->bindParam(':expirationDate', $expirationDate, PDO::PARAM_STR);
    $statement->bindParam(':cvv', $cvv, PDO::PARAM_STR);
    $statement->bindParam(':paypalEmail', $paypalEmail, PDO::PARAM_STR);

    if (!@$statement->execute()) {
        $errorInfo = $statement->errorInfo();
        $errorMessage = json_encode($errorInfo[2]);

        echo "<script>console.error($errorMessage);</script>";
        
        return false;
    }

    return true;
    
}


function GetUserAddresses($pdo, $userID, $activeOnly = 1){
    
        $query = "SELECT * FROM adresses_utilisateurs WHERE utilisateur_id = :userID";
    
        $statement = $pdo->prepare($query);
        $statement->bindParam(':userID', $userID, PDO::PARAM_INT);

        if (!@$statement->execute()) {
            $errorInfo = $statement->errorInfo();
            $errorMessage = json_encode($errorInfo[2]);
    
            echo "<script>console.error($errorMessage);</script>";
            
            return false;
        }

        $addressedIDs = $statement->fetchAll(PDO::FETCH_ASSOC);

        if($addressedIDs == false){
            return false;
        }

        $addresses = array();

        foreach($addressedIDs as $addressID){
            $address = GetAddressByID($pdo, $addressID['id']);
            if($address != false){
                array_push($addresses, $address, $activeOnly);
            }
        }
    
        return $addresses;
}


function GetAddressByID($pdo, $addressID,$activeOnly = 1){
        
    $query = "SELECT * FROM adresses_utilisateurs WHERE id = :addressID AND est_actif >= :activeOnly LIMIT 1";
    
    $statement = $pdo->prepare($query);
    $statement->bindParam(':addressID', $addressID, PDO::PARAM_INT);
    $statement->bindParam(':activeOnly', $activeOnly, PDO::PARAM_INT);
    
    if (!@$statement->execute()) {
        $errorInfo = $statement->errorInfo();
        $errorMessage = json_encode($errorInfo[2]);
    
        echo "<script>console.error($errorMessage);</script>";
        
        return false;
    }
    
    return $statement->fetch(PDO::FETCH_ASSOC);

}

function GetUserPaymentMethods($pdo, $userID, $activeOnly =1){
    
    $query = "SELECT * FROM moyens_paiement WHERE utilisateur_id = :userID";
    
    $statement = $pdo->prepare($query);
    $statement->bindParam(':userID', $userID, PDO::PARAM_INT);
    
    if (!@$statement->execute()) {
        $errorInfo = $statement->errorInfo();
        $errorMessage = json_encode($errorInfo[2]);
    
        echo "<script>console.error($errorMessage);</script>";
        
        return false;
    }

    $paymentMethodIDs = $statement->fetchAll(PDO::FETCH_ASSOC);

    if($paymentMethodIDs == false){
        return false;
    }

    $paymentMethods = array();

    foreach($paymentMethodIDs as $paymentMethodID){
        $paymentMethod = GetPaymentMethodByID($pdo, $paymentMethodID['id']);
        if($paymentMethod != false){
            array_push($paymentMethods, $paymentMethod);
        }
    }
    
    return $paymentMethods;

}

function GetPaymentMethodByID($pdo, $paymentMethodID, $activeOnly = 1){

    $query = "SELECT * FROM moyens_paiement WHERE id = :paymentMethodID AND est_actif >= :activeOnly LIMIT 1";

    $statement = $pdo->prepare($query);
    $statement->bindParam(':paymentMethodID', $paymentMethodID, PDO::PARAM_INT);
    $statement->bindParam(':activeOnly', $activeOnly, PDO::PARAM_INT);

    if (!@$statement->execute()) {
        $errorInfo = $statement->errorInfo();
        $errorMessage = json_encode($errorInfo[2]);

        echo "<script>console.error($errorMessage);</script>";
        
        return false;
    }

    return $statement->fetch(PDO::FETCH_ASSOC);
}


function DeactivateUserAddress($pdo, $userID, $addressID){
    
    $query = "UPDATE adresses_utilisateurs SET est_actif = 0 WHERE utilisateur_id = :userID AND id = :addressID";

    $statement = $pdo->prepare($query);
    $statement->bindParam(':userID', $userID, PDO::PARAM_INT);
    $statement->bindParam(':addressID', $addressID, PDO::PARAM_INT);

    if (!@$statement->execute()) {
        $errorInfo = $statement->errorInfo();
        $errorMessage = json_encode($errorInfo[2]);

        echo "<script>console.error($errorMessage);</script>";
        
        return false;
    }

    return true;
}


function DeactivateUserPaymentMethod($pdo, $userID, $paymentID){

    $query = "UPDATE moyens_paiement SET est_actif = 0 WHERE utilisateur_id = :userID AND id = :paymentID";

    $statement = $pdo->prepare($query);
    $statement->bindParam(':userID', $userID, PDO::PARAM_INT);
    $statement->bindParam(':paymentID', $paymentID, PDO::PARAM_INT);

    if (!@$statement->execute()) {
        $errorInfo = $statement->errorInfo();
        $errorMessage = json_encode($errorInfo[2]);

        echo "<script>console.error($errorMessage);</script>";
        
        return false;
    }

    return true;
}