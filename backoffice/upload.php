<?php
require '../vendor/autoload.php'; // Assurez-vous que le chemin est correct

use MicrosoftAzure\Storage\Blob\BlobRestProxy;
use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;

$connectionString = "DefaultEndpointsProtocol=https;AccountName=imgproduitnewvet;AccountKey=wn85f9ndBMq16Bis0lEq4ud2iRItnx+b24MI2HU6X1/w8HN1SLW1gZyDRTekph2nJtestcld5GtV+AStwPlIuw==;";
$blobClient = BlobRestProxy::createBlobService($connectionString);

if (isset($_FILES['file']) && isset($_POST['blobName']) && isset($_POST['container'])) {
    $container = $_POST['container'];
    $blobName = $_POST['blobName'];
    $file = $_FILES['file'];

    if ($file['error'] == UPLOAD_ERR_OK) {
        try {
            // Lire le fichier temporaire en tant que chaîne de caractères
            $content = file_get_contents($file['tmp_name']);

            if ($content === false) {
                echo "Erreur lors de la lecture du fichier.";
                exit;
            }

            // Uploader le fichier
            $blobClient->createBlockBlob($container, $blobName, $content);

            echo "Upload réussi pour $blobName dans le container $container";
        } catch (ServiceException $e) {
            echo "Erreur de service: " . $e->getMessage();
        } catch (Exception $e) {
            echo "Erreur: " . $e->getMessage();
        }
    } else {
        echo "Erreur lors du téléchargement du fichier.";
    }
} else {
    echo "Données manquantes pour l'upload.";
}
?>
