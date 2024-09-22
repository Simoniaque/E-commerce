<?php

use Symfony\Component\Validator\Constraints\Length;

function GetUsers($pdo, $activeOnly = 1)
{
    $query = "SELECT * FROM utilisateurs WHERE est_actif >= :activeOnly";
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

function GetCurrentUser($pdo, $activeOnly = 1)
{
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

function GetUserByID($pdo, $userID, $activeOnly = 1)
{
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


function GetUserByEmail($pdo, $userEmail, $activeOnly = 1)
{
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


function CheckUserExistsAndIsActive($pdo, $email, &$exists, &$active)
{

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

    if ($result != false) {
        $exists = true;

        if ($result["est_actif"] == 1) {
            $active = true;
        }
    }


    return true;
}

function CreateUser($pdo, $name, $email, $password, &$newUserId)
{

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

function GenerateURLVerifyAccount($pdo, $userID)
{

    if (UserAlreadyHasVerificationToken($pdo, $userID, $userToken) === false) {
        return false;
    }

    //si l'utilisateur a déjà un token on le supprime
    if ($userToken != false) {
        if (DeleteVerificationToken($pdo, $userToken['utilisateur_id']) === false) {
            return false;
        }
    }

    $newTokenValue = bin2hex(random_bytes(32));
    $dateMax = date('Y-m-d H:i:s', strtotime('+15 minutes'));

    if (CreateVerificationToken($pdo, $userID, $newTokenValue, $dateMax) === true) {
        $url = WEBSITE_URL . "verifyaccount.php?email=" . GetUserByID($pdo, $userID)['email'] . "&token=" . $newTokenValue;
        return $url;
    }
}

function CreateVerificationToken($pdo, $id, $tokenValue, $dateMax)
{

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

function DeleteVerificationToken($pdo, $userID)
{

    $query = "DELETE FROM tokens_verification_mail WHERE utilisateur_id = :userID";

    $statement = $pdo->prepare($query);
    $statement->bindParam(':userID', $userID, PDO::PARAM_INT);

    if (!@$statement->execute()) {
        $errorInfo = $statement->errorInfo();
        $errorMessage = json_encode($errorInfo[2]);

        echo "<script>console.error($errorMessage);</script>";

        return false;
    }

    return true;
}

function UserAlreadyHasVerificationToken($pdo, $userID, &$token)
{

    $query = "SELECT * FROM tokens_verification_mail WHERE utilisateur_id = :userID";

    $statement = $pdo->prepare($query);
    $statement->bindParam(':userID', $userID, PDO::PARAM_INT);

    if (!@$statement->execute()) {
        $errorInfo = $statement->errorInfo();
        $errorMessage = json_encode($errorInfo[2]);

        echo "<script>console.error($errorMessage);</script>";

        return false;
    }

    $token = $statement->fetch(PDO::FETCH_ASSOC);

    return true;
}


function DeactivateUser($pdo, $userID)
{

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

function UpdateUserInfo($pdo, $id, $name, $email)
{

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

function AddUserAddress($pdo, $userId, $voie, $ville, $codePostal, $pays)
{
    $query = "INSERT INTO adresses_utilisateurs (utilisateur_id, voie, ville, code_postal, pays) 
              VALUES (:userId, :voie, :ville, :codePostal, :pays)";

    $statement = $pdo->prepare($query);
    $statement->bindParam(':userId', $userId, PDO::PARAM_INT);
    $statement->bindParam(':voie', $voie, PDO::PARAM_STR);
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

function AddUserPaymentMethod($pdo, $userID, $payementType, $cardNumber, $cardName, $expirationDate, $cvv, $paypalEmail)
{
    $expirationDate = $expirationDate ? $expirationDate . '-01' : null;

    $query = "INSERT INTO moyens_paiement (utilisateur_id, type, numero_carte, nom_titulaire, date_expiration, cvv, paypal_email) 
              VALUES (:userID, :payementType, :cardNumber, :cardName, :expirationDate, :cvv, :paypalEmail)";

    //remove all characters except digits in cardNumber
    $cardNumber = preg_replace('/\D/', '', $cardNumber);

    if (strlen($cardNumber) != 16) {
        echo "<script>console.error('Le numéro de la carte doit avoir 16 chiffres');</script>";
        return false;
    }

    $cardNumber = "**** **** **** " . substr($cardNumber, -4);

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


function GetUserAddresses($pdo, $userID, $activeOnly = 1)
{

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

    if ($addressedIDs == false) {
        return false;
    }

    $addresses = array();

    foreach ($addressedIDs as $addressID) {
        $address = GetAddressByID($pdo, $addressID['id'], $activeOnly);
        if ($address != false) {
            array_push($addresses, $address);
        }
    }

    return $addresses;
}


function GetAddressByID($pdo, $addressID, $activeOnly = 1)
{

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

function GetUserPaymentMethods($pdo, $userID, $activeOnly = 1)
{

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

    if ($paymentMethodIDs == false) {
        return false;
    }

    $paymentMethods = array();

    foreach ($paymentMethodIDs as $paymentMethodID) {
        $paymentMethod = GetPaymentMethodByID($pdo, $paymentMethodID['id']);
        if ($paymentMethod != false) {
            array_push($paymentMethods, $paymentMethod);
        }
    }

    return $paymentMethods;
}

function GetPaymentMethodByID($pdo, $paymentMethodID, $activeOnly = 1)
{

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


function DeactivateUserAddress($pdo, $userID, $addressID)
{

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


function DeactivateUserPaymentMethod($pdo, $userID, $paymentID)
{

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


function CheckTokenAndVerifyUser($pdo, $userID, $token)
{

    if (CheckTokenValidity($pdo, $userID, $token) === false) {
        return false;
    }

    if (VerifyUser($pdo, $userID) === false) {
        return false;
    }


    return true;
}

function CheckTokenValidity($pdo, $userID, $token)
{
    $query = "SELECT * FROM tokens_verification_mail WHERE utilisateur_id = :userID AND token = :token AND date_max > NOW()";
    $statement = $pdo->prepare($query);
    $statement->bindParam(':userID', $userID, PDO::PARAM_INT);
    $statement->bindParam(':token', $token, PDO::PARAM_STR);

    if (!@$statement->execute()) {
        $errorInfo = $statement->errorInfo();
        $errorMessage = json_encode($errorInfo[2]);

        echo "<script>console.error($errorMessage);</script>";

        return false;
    }

    $tokenFound = $statement->fetch(PDO::FETCH_ASSOC);

    if ($tokenFound == false) {
        return false;
    }

    return true;
}

function VerifyUser($pdo, $userID)
{

    $query = "UPDATE utilisateurs SET mail_verifie = 1 WHERE id = :userID";
    $statement = $pdo->prepare($query);
    $statement->bindParam(':userID', $userID, PDO::PARAM_INT);

    if (!@$statement->execute()) {
        $errorInfo = $statement->errorInfo();
        $errorMessage = json_encode($errorInfo[2]);

        echo "<script>console.error($errorMessage);</script>";

        return false;
    }
    return true;
}


function CheckPasswordResetTokenValidity($pdo, $userID, $token)
{

    $query = "SELECT * FROM tokens_reinitialisation_mdp WHERE utilisateur_id = :userID AND token = :token AND date_max > NOW()";

    $statement = $pdo->prepare($query);
    $statement->bindParam(':userID', $userID, PDO::PARAM_INT);
    $statement->bindParam(':token', $token, PDO::PARAM_STR);

    if (!@$statement->execute()) {
        $errorInfo = $statement->errorInfo();
        $errorMessage = json_encode($errorInfo[2]);

        echo "<script>console.error($errorMessage);</script>";

        return false;
    }

    $tokenFound = $statement->fetch(PDO::FETCH_ASSOC);

    if ($tokenFound == false) {
        return false;
    }

    return true;
}

function ResetPassword($pdo, $userID, $newPassword)
{

    $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

    $query = "UPDATE utilisateurs SET mot_de_passe = :newPassword WHERE id = :userID";

    $statement = $pdo->prepare($query);
    $statement->bindParam(':newPassword', $hashedPassword, PDO::PARAM_STR);
    $statement->bindParam(':userID', $userID, PDO::PARAM_INT);

    if (!@$statement->execute()) {
        $errorInfo = $statement->errorInfo();
        $errorMessage = json_encode($errorInfo[2]);

        echo "<script>console.error($errorMessage);</script>";

        return false;
    }
    return true;
}

function GenerateURLResetPassword($pdo, $userID)
{

    if (UserAlreadyHasResetToken($pdo, $userID, $foundToken) === false) {
        return false;
    }

    if ($foundToken != false) {
        if (DeleteResetPasswordToken($pdo, $foundToken['utilisateur_id']) === false) {
            return false;
        }
    }

    $newTokenValue = bin2hex(random_bytes(32));
    $dateMax = date('Y-m-d H:i:s', strtotime('+15 minutes'));

    if (CreateResetPasswordToken($pdo, $userID, $newTokenValue, $dateMax) === true) {
        $url = WEBSITE_URL . "resetpassword.php?email=" . GetUserByID($pdo, $userID)['email'] . "&token=" . $newTokenValue;
        return $url;
    }
}

function UserAlreadyHasResetToken($pdo, $userID, &$token)
{

    $query = "SELECT * FROM tokens_reinitialisation_mdp WHERE utilisateur_id = :userID";

    $statement = $pdo->prepare($query);
    $statement->bindParam(':userID', $userID, PDO::PARAM_INT);

    if (!@$statement->execute()) {
        $errorInfo = $statement->errorInfo();
        $errorMessage = json_encode($errorInfo[2]);

        echo "<script>console.error($errorMessage);</script>";

        return false;
    }

    $token = $statement->fetch(PDO::FETCH_ASSOC);

    return true;
}

function DeleteResetPasswordToken($pdo, $userID)
{

    $query = "DELETE FROM tokens_reinitialisation_mdp WHERE utilisateur_id = :userID";

    $statement = $pdo->prepare($query);
    $statement->bindParam(':userID', $userID, PDO::PARAM_INT);

    if (!@$statement->execute()) {
        $errorInfo = $statement->errorInfo();
        $errorMessage = json_encode($errorInfo[2]);

        echo "<script>console.error($errorMessage);</script>";

        return false;
    }

    return true;
}

function CreateResetPasswordToken($pdo, $userID, $newTokenValue, $dateMax)
{

    $query = "INSERT INTO tokens_reinitialisation_mdp (utilisateur_id, token, date_max) VALUES (:userID, :newTokenValue, :dateMax)";

    $statement = $pdo->prepare($query);
    $statement->bindParam(':userID', $userID, PDO::PARAM_INT);
    $statement->bindParam(':newTokenValue', $newTokenValue, PDO::PARAM_STR);
    $statement->bindParam(':dateMax', $dateMax, PDO::PARAM_STR);

    if (!@$statement->execute()) {
        $errorInfo = $statement->errorInfo();
        $errorMessage = json_encode($errorInfo[2]);

        echo "<script>console.error($errorMessage);</script>";

        return false;
    }

    return true;
}

function UpdateUser($pdo, $userID, $name, $email, $password, $isAdmin, $emailVerified, $isActive)
{

    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    $query = "UPDATE utilisateurs SET nom = :name, email = :email, mot_de_passe = :password, est_admin = :isAdmin, mail_verifie = :emailVerified, est_actif = :isActive WHERE id = :userID";

    $statement = $pdo->prepare($query);
    $statement->bindParam(':userID', $userID, PDO::PARAM_INT);
    $statement->bindParam(':name', $name, PDO::PARAM_STR);
    $statement->bindParam(':email', $email, PDO::PARAM_STR);
    $statement->bindParam(':password', $hashedPassword, PDO::PARAM_STR);
    $statement->bindParam(':isAdmin', $isAdmin, PDO::PARAM_INT);
    $statement->bindParam(':emailVerified', $emailVerified, PDO::PARAM_INT);
    $statement->bindParam(':isActive', $isActive, PDO::PARAM_INT);

    if (!@$statement->execute()) {
        $errorInfo = $statement->errorInfo();
        $errorMessage = json_encode($errorInfo[2]);

        echo "<script>console.error($errorMessage);</script>";

        return false;
    }

    return true;
}

function AddMessage($pdo, $name, $email, $phone, $subject, $message){
    $query = "INSERT INTO messages_contact (nom, email, telephone, sujet, message) VALUES (:name, :email, :phone, :subject, :message)";
    $statement = $pdo->prepare($query);
    $statement->bindParam(':name', $name, PDO::PARAM_STR);
    $statement->bindParam(':email', $email, PDO::PARAM_STR);
    $statement->bindParam(':phone', $phone, PDO::PARAM_STR);
    $statement->bindParam(':subject', $subject, PDO::PARAM_STR);
    $statement->bindParam(':message', $message, PDO::PARAM_STR);

    if (!@$statement->execute()) {
        $errorInfo = $statement->errorInfo();
        $errorMessage = json_encode($errorInfo[2]);
        echo "<script>console.error($errorMessage);</script>";
        return false;
    }
    return true;
}

function GetMessages($pdo){
    $query = "SELECT * FROM messages_contact";
    $statement = $pdo->prepare($query);

    if (!@$statement->execute()) {
        $errorInfo = $statement->errorInfo();
        $errorMessage = json_encode($errorInfo[2]);
        echo "<script>console.error($errorMessage);</script>";
        return false;
    }

    return $statement->fetchAll(PDO::FETCH_ASSOC);
}


function DeleteMessage($pdo, $messageID){
    $query = "DELETE FROM messages_contact WHERE id = :messageID";
    $statement = $pdo->prepare($query);
    $statement->bindParam(':messageID', $messageID, PDO::PARAM_INT);

    if (!@$statement->execute()) {
        $errorInfo = $statement->errorInfo();
        $errorMessage = json_encode($errorInfo[2]);
        echo "<script>console.error($errorMessage);</script>";
        return false;
    }
    return true;
}

function ProcessMessage($pdo, $messageID){
    $query = "UPDATE messages_contact SET traite = 1 WHERE id = :messageID";
    $statement = $pdo->prepare($query);
    $statement->bindParam(':messageID', $messageID, PDO::PARAM_INT);

    if (!@$statement->execute()) {
        $errorInfo = $statement->errorInfo();
        $errorMessage = json_encode($errorInfo[2]);
        echo "<script>console.error($errorMessage);</script>";
        return false;
    }
    return true;
}

function AddUser($pdo,$name, $email, $password, $isAdmin, $emailVerified){
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    $query = "INSERT INTO utilisateurs (nom, email, mot_de_passe, est_admin, mail_verifie) VALUES (:name, :email, :password, :isAdmin, :emailVerified)";
    $statement = $pdo->prepare($query);
    $statement->bindParam(':name', $name, PDO::PARAM_STR);
    $statement->bindParam(':email', $email, PDO::PARAM_STR);
    $statement->bindParam(':password', $hashedPassword, PDO::PARAM_STR);
    $statement->bindParam(':isAdmin', $isAdmin, PDO::PARAM_INT);
    $statement->bindParam(':emailVerified', $emailVerified, PDO::PARAM_INT);

    if (!@$statement->execute()) {
        $errorInfo = $statement->errorInfo();
        $errorMessage = json_encode($errorInfo[2]);
        echo "<script>console.error($errorMessage);</script>";
        return false;
    }
    return true;    
}