<?php
require '../vendor/autoload.php';
include_once '../config.php';

use MicrosoftAzure\Storage\Blob\BlobRestProxy;
use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;


$blobClient = BlobRestProxy::createBlobService($azureConnectionString);

header('Content-Type: application/json');

if (isset($_FILES['file']) && isset($_POST['blobName']) && isset($_POST['container'])) {
    $container = $_POST['container'];
    $blobName = $_POST['blobName'];
    $file = $_FILES['file'];

    if ($file['error'] == UPLOAD_ERR_OK) {
        try {
            $content = file_get_contents($file['tmp_name']);

            if ($content === false) {
                http_response_code(400); // Bad Request
                echo json_encode(['success' => false, 'message' => 'Erreur lors de la lecture du fichier.']);
                exit;
            }

            $blobClient->createBlockBlob($container, $blobName, $content);

            http_response_code(200); // OK
            echo json_encode(['success' => true, 'message' => "Upload réussi pour $blobName dans le container $container"]);
        } catch (ServiceException $e) {
            http_response_code(500); // Internal Server Error
            echo json_encode(['success' => false, 'message' => "Erreur de service: " . $e->getMessage()]);
        } catch (Exception $e) {
            http_response_code(500); // Internal Server Error
            echo json_encode(['success' => false, 'message' => "Erreur: " . $e->getMessage()]);
        }
    } else {
        http_response_code(400); // Bad Request
        echo json_encode(['success' => false, 'message' => 'Erreur lors du téléchargement du fichier.']);
    }
} else {
    http_response_code(400); // Bad Request
    echo json_encode(['success' => false, 'message' => 'Paramètres manquants.']);
}
?>