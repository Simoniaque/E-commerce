<?php
$dbAddress = "localhost";
$dbUsername = "root";
$dbPassword = "";
$dbName = "newvet";

define('WEBSITE_URL', "localhost/E-Commerce/"); 
define('PATH_PRODUCTS_IMAGES', 'https://imgproduitnewvet.blob.core.windows.net/imagescontainer/');
define('PATH_CATEGORY_IMAGES', 'https://imgproduitnewvet.blob.core.windows.net/imagescategories/');

try {
    $pdoString = "mysql:host=$dbAddress;dbname=$dbName;charset=utf8";
    $pdo = new PDO($pdoString, $dbUsername, $dbPassword, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
} catch (PDOException $e) {
    $errorMessage = json_encode($e->getMessage());
    echo "<script>console.error('Erreur de connexion à la base de données : ' + $errorMessage);</script>";
    die();
}