<?php

function GetCurrentUser($pdo) {
    if (isset($_SESSION['user_id'])) {
        $id = $_SESSION['user_id'];
        $query = "SELECT * FROM utilisateurs WHERE id = :id LIMIT 1";
        $statement = $pdo->prepare($query);
        $statement->bindParam(':id', $id, PDO::PARAM_INT);

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

function GetUserByID($pdo, $userID) {
    $query = "SELECT * FROM utilisateurs WHERE id = :userID LIMIT 1";
    $statement = $pdo->prepare($query);
    $statement->bindParam(':userID', $userID, PDO::PARAM_INT);

    if (!@$statement->execute()) {
        $errorInfo = $statement->errorInfo();
        $errorMessage = json_encode($errorInfo[2]);
        echo "<script>console.error($errorMessage);</script>";
        return false;
    }

    return $statement->fetch(PDO::FETCH_ASSOC);
}


function GetUserByEmail($pdo, $userEmail) {
    $query = "SELECT * FROM utilisateurs WHERE email = :email limit 1";
    $statement = $pdo->prepare($query);
    
    $statement->bindParam(':email', $userEmail, PDO::PARAM_STR);
    
    if (!@$statement->execute()) {
        $errorInfo = $statement->errorInfo();
        $errorMessage = json_encode($errorInfo[2]);

        echo "<script>console.error($errorMessage);</script>";
        
        
        return false;
    }

    $user = $statement->fetch(PDO::FETCH_ASSOC);
    return $user;
}


function CheckUserExists($pdo, $email, &$exists){

    $query = "SELECT COUNT(*) as count FROM utilisateurs WHERE email = :email";
    $statement = $pdo->prepare($query);
    $statement->bindParam(':email', $email, PDO::PARAM_STR);

    if (!@$statement->execute()) {
        $errorInfo = $statement->errorInfo();
        $errorMessage = json_encode($errorInfo[2]);

        echo "<script>console.error($errorMessage);</script>";
        
        return false;
    }

    $result = $statement->fetch(PDO::FETCH_ASSOC);
    
    $exists = $result['count'] > 0;

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