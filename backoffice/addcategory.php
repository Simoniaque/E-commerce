<?php
// addcategory.php
session_start();
include('../config.php');
include('../functions.php');

$response = array('success' => false, 'message' => '');

// Traitement du formulaire lors de l'envoi
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nom = $_POST['nom'];
    $description = $_POST['description'];

    $result = addCategory($con, $nom, $description);

    if ($result) {
        $response['success'] = true;
        $response['message'] = 'Catégorie ajoutée avec succès!';
        $response['category_id'] = $result; // On suppose que `addCategory` retourne l'ID de la nouvelle catégorie
    } else {
        $response['message'] = 'Erreur lors de l\'ajout de la catégorie: ' . $con->error;
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
    <title>Ajouter une Catégorie</title>
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

                    <h1 class="mb-4">Ajouter une Catégorie</h1>
                    <form id="categoryForm" method="POST" enctype="multipart/form-data">
                        <div class="row mb-3">
                            <div class="col">
                                <label for="image1" class="form-label">Image 1:</label>
                                <input type="file" id="image1" name="image1" class="form-control" accept="image/*">
                                <div class="img-container">
                                    <img id="previewImage1" src="" alt="" class="img-preview mt-2">
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

                        <button type="submit" class="btn btn-primary">Ajouter la Catégorie</button>
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

        const convertToPNG = (file) => {
            return new Promise((resolve, reject) => {
                const reader = new FileReader();
                reader.onload = () => {
                    const img = new Image();
                    img.onload = () => {
                        const canvas = document.createElement('canvas');
                        const ctx = canvas.getContext('2d');
                        canvas.width = img.width;
                        canvas.height = img.height;
                        ctx.drawImage(img, 0, 0);
                        canvas.toBlob((blob) => {
                            if (blob) {
                                resolve(blob);
                            } else {
                                reject(new Error('Erreur lors de la conversion en PNG.'));
                            }
                        }, 'image/png');
                    };
                    img.src = reader.result;
                };
                reader.readAsDataURL(file);
            });
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
                const convertedFile = await convertToPNG(file);

                const formData = new FormData();
                formData.append('file', convertedFile);
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

        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('categoryForm');

            form.addEventListener('submit', async function(event) {
                event.preventDefault(); // Empêcher la soumission normale du formulaire

                const formData = new FormData(form);

                try {
                    const response = await fetch('addcategory.php', { // Notez le changement ici pour soumettre à `addcategory.php`
                        method: 'POST',
                        body: formData
                    });

                    if (!response.ok) {
                        throw new Error('Erreur lors de la soumission du formulaire');
                    }

                    const data = await response.json();

                    if (data.success) {
                        // Assurez-vous de spécifier l'ID de la catégorie pour le traitement des images
                        await uploadImage(formData.get('image1'), 'imagescategories', `${data.category_id}.png`);
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
