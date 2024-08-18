<?php
$dbAddress = "localhost";
$dbUsername = "root";
$dbPassword = "";
$dbName = "newvet";

define('PATH_PRODUCTS_IMAGES', 'https://imgproduitnewvet.blob.core.windows.net/imagescontainer/');
define('PATH_CATEGORY_IMAGES', 'https://imgproduitnewvet.blob.core.windows.net/imagescategories/');


if (!$con = mysqli_connect($dbAddress, $dbUsername, $dbPassword, $dbName)){
    die("No connection to database");
}
?>
