<?php
session_start();

include_once "../config.php";
include_once "../API/usersRequests.php";
include_once "../API/productsRequests.php";
include_once "../API/categoriesRequests.php";
include_once "../functions.php";

$user = GetCurrentUser($pdo);

if ($user === false) {
    header('Location: ../index.php');
    exit;
}

if ($user['est_admin'] == 0) {
    header('Location: ../index.php');
    exit;
}

if(!isset($_GET['id'])){
    header('Location: categories.php');
    exit;
}

$categoryID = $_GET['id'];
$category = GetCategoryById($pdo, $categoryID, 0);

if (!$category) {
    die('Catégorie non trouvée.');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nom = $_POST['nom'];
    $description = $_POST['description'];
    $isActive = $_POST['isActive'];

    if (UpdateCategory($pdo, $categoryID, $nom, $description, $isActive)) {
        DisplayDismissibleSuccess('Catégorie modifiée avec succès.');

        $category = GetCategoryById($pdo, $categoryID, 0);
    } else {
        DisplayDismissibleAlert('Erreur lors de la modification de la catégorie.');
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" href="../assets/img/logo-black.png" type="image/x-icon">
    <style>
        .img-container {
            position: relative;
            display: inline-block;
            width: 30%;
        }

        .img-preview {
            max-height: 150px;
            width: 100%;
            object-fit: contain;
        }
    </style>
</head>

<body>
    <div class="container-fluid p-0">
        <div class="row g-0">
            <div class="col-md-2 p-0">
                <?php include 'navbar.php'; ?>
            </div>
            <div class="col-md-10 p-0">
                <?php include 'header.php'; ?>

                <div class="container mt-4">
                    <div id="alertContainer"></div>

                    <h1 class="mb-4">Modifier Catégorie</h1>
                    <form id="categoryForm" method="POST" enctype="multipart/form-data">
                        <div class="row mb-3">
                            <div class="col">
                                <label for="image1" class="form-label">Image 1:</label>
                                <input type="file" id="image1" name="image1" class="form-control" accept="image/png">
                                <div class="img-container">
                                    <img id="previewImage1" src="https://imgproduitnewvet.blob.core.windows.net/imagescategories/<?php echo $categoryID; ?>.png" alt="" class="img-preview mt-2">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="nom" class="form-label">Nom:</label>
                            <input type="text" id="nom" name="nom" class="form-control" value="<?php echo $category['nom']; ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description:</label>
                            <textarea id="description" name="description" class="form-control" rows="3" required><?php echo $category['description']; ?></textarea>
                        </div>

                        <div class="mb-5">
                            <label for="isActive" class="form-label">Est Actif:</label>
                            <select id="isActive" name="isActive" class="form-select">
                                <option value="1" <?php echo $category['est_actif'] == 1 ? 'selected' : ''; ?>>Oui</option>
                                <option value="0" <?php echo $category['est_actif'] == 0 ? 'selected' : ''; ?>>Non</option>
                            </select>
                        </div>


                        <button type="submit" class="btn btn-primary">Modifier la Catégorie</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- JavaScript pour la gestion des images et l'upload -->
    <script>
        document.getElementById('categoryForm').addEventListener('submit', (e) => {
            e.preventDefault();
            document.querySelector('button[type="submit"]').disabled = true;
            
            uploadImages();
        });

        async function uploadImages() {

            const image1 = document.getElementById('image1').files[0];
            if (image1) {
                const image1Name = '<?php echo $categoryID; ?>.png';

                await uploadToAzureBlob(image1, image1Name);
            }

            document.getElementById('categoryForm').submit();
        }

        async function uploadToAzureBlob(imageBlob, imageName) {
            const formData = new FormData();
            formData.append('file', imageBlob);
            formData.append('blobName', imageName);
            formData.append('container', 'imagescategories');

            try {
                const response = await fetch('upload.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (response.ok) {
                    console.log(`Succès: ${result.message}`);

                    //add a bootstrap success alert
                    const alertContainer = document.getElementById('alertContainer');
                    const alert = document.createElement('div');
                    alert.className = 'alert alert-success alert-dismissible';
                    alert.innerHTML = `<button type="button" class="btn-close" data-bs-dismiss="alert"></button>${result.message}`;
                    alertContainer.appendChild(alert);
                } else {
                    alert(`Erreur: ${result.message}`);
                }
            } catch (error) {
                alert(`Erreur lors de l'upload de ${imageName} vers imagescategories: ${error.message}`);
            }
        }


        const previewImage = (event, previewId) => {
            const preview = document.getElementById(previewId);
            const file = event.target.files[0];

            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    preview.src = e.target.result;
                };
                reader.readAsDataURL(file);
            } else {
                preview.src = '';
            }
        };

        document.getElementById('image1').addEventListener('change', (e) => previewImage(e, 'previewImage1'));
    </script>

    <script>
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    </script>
</body>

</html>