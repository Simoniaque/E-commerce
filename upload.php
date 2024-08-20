<?php
session_start();

include("config.php");
include("functions.php");

require 'vendor/autoload.php';

use MicrosoftAzure\Storage\Blob\BlobRestProxy;
use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;
use MicrosoftAzure\Storage\Blob\Models\CreateBlockBlobOptions;

function uploadImgToBlob($connectionString, $containerName, $file, $blobName)
{
    try {
        $blobClient = BlobRestProxy::createBlobService($connectionString);

        // Vérifier si l'image existe déjà dans le container
        $blobExists = imgExistsOnContainer($blobClient, $containerName, $blobName);

        // Options de création de blob
        $options = new CreateBlockBlobOptions();
        $options->setContentType($file['type']);

        // Lire le contenu du fichier
        $content = fopen($file['tmp_name'], "r");

        if ($blobExists) {
            // Remplacer l'image existante
            $blobClient->createBlockBlob($containerName, $blobName, $content, $options);
            return "Le fichier '$blobName' a été remplacé dans le container";
        } else {
            $blobClient->createBlockBlob($containerName, $blobName, $content, $options);
            return "Le fichier '$blobName' a été téléchargé avec succès dans le container";
        }
    } catch (ServiceException $e) {
        $code = $e->getCode();
        $error_message = $e->getMessage();
        return "Erreur lors du téléchargement du fichier '$blobName' : $code - $error_message";
    }
}

function imgExistsOnContainer($blobClient, $containerName, $blobName)
{
    try {
        // Vérifier si le blob existe dans le container
        $blobInfo = $blobClient->getBlobMetadata($containerName, $blobName);
        return true;
    } catch (ServiceException $e) {
        $code = $e->getCode();
        // 404 signifie que le blob n'existe pas
        if ($code == 404) {
            return false;
        } else {
            // Autre erreur
            throw $e;
        }
    }
}

function addProduct($_productName, $_productCategory, $_productDescription, $_productPrice, $_productStock)
{
    global $con;

    // Escape the input to prevent SQL injection
    $productName = mysqli_real_escape_string($con, $_productName);
    $productCategory = mysqli_real_escape_string($con, $_productCategory);
    $productDescription = mysqli_real_escape_string($con, $_productDescription);
    $productPrice = mysqli_real_escape_string($con, $_productPrice);
    $productStock = mysqli_real_escape_string($con, $_productStock);

    // Get the category id from the categories table
    $categorySql = "SELECT id FROM categories WHERE nom = '$productCategory'";
    $categoryResult = mysqli_query($con, $categorySql);
    $categoryRow = mysqli_fetch_assoc($categoryResult);
    $categoryId = $categoryRow['id'];

    // Prepare an SQL statement
    $sql = "INSERT INTO produits (categorie_id, nom, description, prix, stock) VALUES ('$categoryId', '$productName', '$productDescription','$productPrice', '$productStock')";

    // Execute the statement
    if (mysqli_query($con, $sql)) {
        $last_id = mysqli_insert_id($con);
        return $last_id;
    } else {
        return "Erreur lors de l'ajout du produit: " . mysqli_error($con);
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $uploadType = $_POST['uploadType'];

    if ($uploadType == 'product') {
        if (isset($_FILES['productImg'])) {
            $productName = $_POST['productName'];
            $productCategory = $_POST['productCategory'];
            $productDescription = $_POST['productDescription'];
            $productPrice = $_POST['productPrice'];
            $productStock = $_POST['productStock'];

            $productId = addProduct($productName, $productCategory, $productDescription, $productPrice, $productStock);

            if (is_numeric($productId)) {
                $file = $_FILES['productImg'];
                $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                $newFilename = $productId . '.' . $extension;
                $file['name'] = $newFilename;

                $connectionString = "DefaultEndpointsProtocol=https;AccountName=imgproduitnewvet;AccountKey=wn85f9ndBMq16Bis0lEq4ud2iRItnx+b24MI2HU6X1/w8HN1SLW1gZyDRTekph2nJtestcld5GtV+AStwPlIuw==;";
                $containerName = "imagescontainer";
                $blobName = $newFilename;
                $message = uploadImgToBlob($connectionString, $containerName, $file, $blobName);


                echo "Le produit a été ajouté avec succès. ID: " . $productId;
            } else {
                echo "Erreur lors de l'ajout du produit: " . $productId;
            }
        } else {
            echo "Aucune image de produit sélectionnée.";
        }
    } elseif ($uploadType == 'carousel') {
        if (isset($_FILES['file'])) {
            $connectionString = "DefaultEndpointsProtocol=https;AccountName=imgproduitnewvet;AccountKey=wn85f9ndBMq16Bis0lEq4ud2iRItnx+b24MI2HU6X1/w8HN1SLW1gZyDRTekph2nJtestcld5GtV+AStwPlIuw==;";
            $containerName = "imagescarousel";
            $file = $_FILES['file'];

            // Récupérer le nom du fichier à partir des paramètres GET
            $blobName = isset($_GET['filename']) ? $_GET['filename'] : $file['name'];

            $message = uploadImgToBlob($connectionString, $containerName, $file, $blobName);
            echo $message;
        } else {
            echo "Aucun fichier sélectionné.";
        }
    } else {
        echo "Type d'upload non reconnu.";
    }
}
