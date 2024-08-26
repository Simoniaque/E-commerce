<?php
session_start();
include('../config.php');
include('../functions.php');

$response = array('success' => false, 'message' => '');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_category'])) {
        $categoryId = intval($_POST['category_id']);
        $categoryName = $_POST['category_name'];
        $categoryDescription = $_POST['category_description'];
        $image = $_FILES['category_image'];

        // Fonction pour l'upload de l'image
        function uploadImageToAzure($file, $container) {
            $response = array('success' => false, 'url' => '', 'error' => '');

            if ($file['error'] !== UPLOAD_ERR_OK) {
                $response['error'] = 'Erreur lors du téléchargement de l\'image.';
                return $response;
            }

            $maxSize = 5 * 1024 * 1024; // 5 MB
            if ($file['size'] > $maxSize) {
                $response['error'] = 'La taille de l\'image ne doit pas dépasser 5 Mo.';
                return $response;
            }

            $filePath = $file['tmp_name'];
            $fileName = basename($file['name']);
            $blobName = uniqid() . '_' . $fileName;

            // Utilisation de la bibliothèque Azure SDK pour PHP
            // Vous devez configurer et inclure le SDK Azure Blob Storage dans votre projet
            $connectionString = 'DefaultEndpointsProtocol=https;AccountName=your_account_name;AccountKey=your_account_key;TableEndpoint=your_table_endpoint;';
            $blobClient = BlobRestProxy::createBlobService($connectionString);

            try {
                $content = fopen($filePath, "r");
                $blobClient->createBlockBlob($container, $blobName, $content);
                fclose($content);

                $response['success'] = true;
                $response['url'] = "https://your_account_name.blob.core.windows.net/{$container}/{$blobName}";
            } catch (Exception $e) {
                $response['error'] = $e->getMessage();
            }

            return $response;
        }

        // Upload de l'image
        $imageUrl = null;
        if ($image['error'] === UPLOAD_ERR_OK) {
            $imageUploadResult = uploadImageToAzure($image, 'imagescategories');
            if ($imageUploadResult['success']) {
                $imageUrl = $imageUploadResult['url'];
            } else {
                $response['message'] = $imageUploadResult['error'];
                echo json_encode($response);
                exit;
            }
        }

        if (updateCategory($con, $categoryId, $categoryName, $categoryDescription, $imageUrl)) {
            $response['success'] = true;
            $response['message'] = 'Catégorie mise à jour avec succès.';
        } else {
            $response['message'] = 'Erreur lors de la mise à jour de la catégorie.';
        }

        echo json_encode($response);
        exit;
    }
} else {
    $categoryId = isset($_GET['id']) ? intval($_GET['id']) : 0;
    $category = getCategoryById($con, $categoryId);

    if (!$category) {
        echo 'Catégorie non trouvée.';
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Catégorie</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" href="../assets/img/logo-black.png" type="image/x-icon">
</head>

<body>
    <div class="container mt-4">
        <h1>Modifier Catégorie</h1>

        <form id="updateCategoryForm" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="category_id" value="<?php echo $category['id']; ?>">
            <div class="mb-3">
                <label for="category_name" class="form-label">Nom de la Catégorie:</label>
                <input type="text" id="category_name" name="category_name" class="form-control" value="<?php echo htmlspecialchars($category['nom']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="category_description" class="form-label">Description:</label>
                <textarea id="category_description" name="category_description" class="form-control" rows="3" required><?php echo htmlspecialchars($category['description']); ?></textarea>
            </div>
            <div class="mb-3">
                <label for="category_image" class="form-label">Image:</label>
                <input type="file" id="category_image" name="category_image" class="form-control" accept="image/*">
            </div>
            <button type="submit" name="update_category" class="btn btn-primary">Mettre à Jour</button>
        </form>

        <div id="alertContainer" class="mt-3"></div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- JavaScript pour la gestion de la modification -->
    <script>
        const showAlert = (message, type) => {
            const alertContainer = document.getElementById('alertContainer');
            alertContainer.innerHTML = `
                <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss='alert' aria-label='Close'></button>
                </div>
            `;
        };

        document.addEventListener('DOMContentLoaded', () => {
            const updateCategoryForm = document.getElementById('updateCategoryForm');

            updateCategoryForm.addEventListener('submit', async (event) => {
                event.preventDefault();

                const formData = new FormData(updateCategoryForm);

                try {
                    const response = await fetch('category.php', {
                        method: 'POST',
                        body: formData
                    });

                    const data = await response.json();
                    if (data.success) {
                        showAlert(data.message, 'success');
                    } else {
                        showAlert(data.message, 'danger');
                    }
                } catch (error) {
                    showAlert(`Erreur lors de la soumission du formulaire: ${error.message}`, 'danger');
                }
            });
        });
    </script>
</body>

</html>
