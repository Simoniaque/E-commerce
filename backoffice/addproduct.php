<?php
session_start();

include_once "../config.php";
include_once "../API/usersRequests.php";
include_once "../API/categoriesRequests.php";
include_once "../API/productsRequests.php";
include_once "../functions.php";

$user = GetCurrentUser($pdo);

if($user === false){
    header('Location: ../index.php');
    exit;
}

if($user['est_admin'] == 0){
    header('Location: ../index.php');
    exit;
}

$response = array('success' => false, 'message' => '');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nom = $_POST['nom'];
    $description = $_POST['description'];
    $prix = floatval($_POST['prix']);
    $stock = intval($_POST['stock']);
    $categorie_id = intval($_POST['categorie_id']);
    $materialsId = isset($_POST['material']) ? array_map('intval', $_POST['material']) : array();

    $newProductId = AddProduct($pdo, $nom, $description, $prix, $stock, $categorie_id, $materialsId);

    if ($newProductId) {
        $response['success'] = true;
        $response['message'] = 'Produit ajouté avec succès!';
        $response['product_id'] = $newProductId;

    } else {
        $response['message'] = 'Erreur lors de l\'ajout du produit.';
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter Produit</title>
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

                    <h1 class="mb-4">Ajouter un Produit</h1>
                    <form id="productForm" method="POST" enctype="multipart/form-data">
                        <div class="row mb-3">
                            <div class="col">
                                <label for="image1" class="form-label">Image 1:</label>
                                <input type="file" id="image1" name="image1" class="form-control" accept="image/webp" required>
                                <div class="img-container">
                                    <img id="previewImage1" src="" alt="" class="img-preview mt-2">
                                </div>
                            </div>
                            <div class="col">
                                <label for="image2" class="form-label">Image 2:</label>
                                <input type="file" id="image2" name="image2" class="form-control" accept="image/webp" required>
                                <div class="img-container">
                                    <img id="previewImage2" src="" alt="" class="img-preview mt-2">
                                </div>
                            </div>
                            <div class="col">
                                <label for="image3" class="form-label">Image 3:</label>
                                <input type="file" id="image3" name="image3" class="form-control" accept="image/webp" required>
                                <div class="img-container">
                                    <img id="previewImage3" src="" alt="" class="img-preview mt-2">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="nom" class="form-label">Nom:</label>
                            <input type="text" id="nom" name="nom" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description:</label>
                            <textarea id="description" name="description" class="form-control" rows="3" required></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="prix" class="form-label">Prix:</label>
                            <input type="number" id="prix" name="prix" class="form-control" step="0.01" required>
                        </div>

                        <div class="mb-3">
                            <label for="stock" class="form-label">Stock:</label>
                            <input type="number" id="stock" name="stock" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for='material' class='form-label'>Matériaux:</label><br/>
                            <?php
                            $materials = GetMaterials($pdo);
                            foreach ($materials as $material) {
                                $idMat = $material['id'];
                                $nomMat = $material['nom'];
                                echo "<input type='checkbox' class='mx-2' id='material$idMat' name='material[]' value='$idMat'>$nomMat</input>";
                            }
                            ?>
                        </div>

                        <div class="mb-3">
                            <label for="categorie_id" class="form-label">Catégorie:</label>
                            <select id="categorie_id" name="categorie_id" class="form-select">
                                <?php
                                $categories = GetCategories($pdo);
                                foreach ($categories as $cat) {
                                    echo "<option value='{$cat['id']}'>{$cat['nom']}</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary">Ajouter le Produit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- JavaScript pour la gestion des images et l'upload -->
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

        const uploadImage = async (file, container, blobName) => {
            if (!file) {
                console.log(`Aucun fichier pour ${blobName}`);
                return;
            }

            const maxSize = 5 * 1024 * 1024; // 5 MB

            if (file.size > maxSize) {
                showAlert('La taille de l\'image ne doit pas dépasser 5 Mo.', 'danger');
                return;
            }

            try {

                const formData = new FormData();
                formData.append('file', file);
                formData.append('blobName', blobName);
                formData.append('container', container);

                const response = await fetch('upload.php', {
                    method: 'POST',
                    body: formData
                });

                const resultText = await response.text();
                showAlert(`Résultat de l'upload de ${blobName} vers ${container}: ${resultText}`, 'success');
            } catch (error) {
                showAlert(`Erreur lors de l'upload de ${blobName} vers ${container}: ${error.message}`, 'danger');
            }
        };

        const handleUploads = async (idProduit, fileInputs, container) => {
            let filesSelected = false;
            const files = fileInputs.map(id => document.getElementById(id).files[0]);
            const blobNames = [
                `${idProduit}.webp`,
                `${idProduit}_2.webp`,
                `${idProduit}_3.webp`
            ];

            for (let i = 0; i < files.length; i++) {
                if (files[i]) {
                    filesSelected = true;
                    console.log(`Uploading ${blobNames[i]}...`);
                    await uploadImage(files[i], container, blobNames[i]);
                }
            }

            if (!filesSelected) {
                console.log('Aucun fichier sélectionné pour l\'upload.');
            }
        };

        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('productForm');

            form.addEventListener('submit', async function(event) {
                event.preventDefault(); // Empêcher la soumission normale du formulaire
                
                const formData = new FormData(form);
                
                try {
                    const response = await fetch('addproduct.php', {
                        method: 'POST',
                        body: formData
                    });

                    const data = await response.json();
                    if (data.success) {
                        await handleUploads(data.product_id, ['image1', 'image2', 'image3'], '<?php echo PRODUCT_IMAGES_CONTAINER; ?>');
                        showAlert(data.message, 'success');
                        form.reset(); // Réinitialiser le formulaire si nécessaire
                    } else {
                        showAlert(data.message, 'danger');
                    }
                } catch (error) {
                    showAlert(`Erreur lors de la soumission du formulaire: ${error.message}`, 'danger');
                }
            });

            const fileInputs = document.querySelectorAll('input[type="file"]');
            fileInputs.forEach(input => {
                input.addEventListener('change', function() {
                    const id = this.id;
                    const preview = document.getElementById(`preview${id.charAt(0).toUpperCase() + id.slice(1)}`);
                    const file = this.files[0];

                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            preview.src = e.target.result;
                        };
                        reader.readAsDataURL(file);
                    } else {
                        preview.src = '';
                    }
                });
            });
        });
    </script>
</body>
</html>
