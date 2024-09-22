<?php
$dbAddress = "localhost";
$dbUsername = "root";
$dbPassword = "";
$dbName = "newvet";

define('WEBSITE_URL', "localhost/E-Commerce/"); 

define('STORAGE_ACCOUNT_NAME', 'imgproduitnewvet');
define('PATH_CAROUSEL_IMAGES', 'https://imgproduitnewvet.blob.core.windows.net/imagescarousel/');
define('PATH_PRODUCTS_IMAGES', 'https://imgproduitnewvet.blob.core.windows.net/imagescontainer/');
define('PATH_CATEGORY_IMAGES', 'https://imgproduitnewvet.blob.core.windows.net/imagescategories/');
define('CATEGORY_IMAGES_CONTAINER', 'imagescategories');
define('PRODUCT_IMAGES_CONTAINER', 'imagescontainer');
define('CAROUSEL_IMAGES_CONTAINER', 'imagescarousel');
define('ACCOUNT_KEY', "wn85f9ndBMq16Bis0lEq4ud2iRItnx+b24MI2HU6X1/w8HN1SLW1gZyDRTekph2nJtestcld5GtV+AStwPlIuw==");
$azureConnectionString = "DefaultEndpointsProtocol=https;AccountName=".STORAGE_ACCOUNT_NAME.";AccountKey=". ACCOUNT_KEY . ";";

define('MAILJET_API_KEY', '0bc323f63d691610b12559e414c49398');
define('MAIJET_API_SECRET_KEY', '4b5e4da435d031796507a324c034a7cd');
define('MAILJET_SENDER_EMAIL', 'contact.newvet@gmail.com');


try {
    $pdoString = "mysql:host=$dbAddress;dbname=$dbName;charset=utf8";
    $pdo = new PDO($pdoString, $dbUsername, $dbPassword, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
} catch (PDOException $e) {
    $errorMessage = json_encode($e->getMessage());
    echo "<script>console.error('Erreur de connexion à la base de données : ' + $errorMessage);</script>";
    die();
}