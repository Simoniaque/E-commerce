<?php
session_start();
include('../config.php');
include('../functions.php');

// Récupérer les catégories existantes
$categories = getCategories($con);

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Catégories</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" href="../assets/img/logo-black.png" type="image/x-icon">
    <style>
        .table-container {
            margin-top: 20px;
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

                    <h1 class="mb-4">Gestion des Catégories</h1>

                    <!-- Formulaire d'ajout de catégorie -->
                    <form id="addCategoryForm" method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="category_name" class="form-label">Nom de la Catégorie:</label>
                            <input type="text" id="category_name" name="category_name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="category_description" class="form-label">Description:</label>
                            <textarea id="category_description" name="category_description" class="form-control" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="category_image" class="form-label">Image:</label>
                            <input type="file" id="category_image" name="category_image" class="form-control" accept="image/*" required>
                        </div>
                        <button type="submit" name="add_category" class="btn btn-primary">Ajouter Catégorie</button>
                    </form>

                    <!-- Table des catégories existantes -->
                    <div class="table-container">
                        <h2 class="mb-4">Catégories Existantes</h2>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nom</th>
                                    <th>Description</th>
                                    <th>Image</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                foreach ($categories as $category) {
                                    $idCategory = $category['id'];
                                    $nomCategory = $category['nom'];
                                    $descriptionCategory = $category['description'];
                                    $imagePath = "https://imgproduitnewvet.blob.core.windows.net/imagescategories/$idCategory.png";

                                    echo "<tr>
                                        <td>$idCategory</td>
                                        <td>$nomCategory</td>
                                        <td>$descriptionCategory</td>
                                        <td><img src='$imagePath' alt='Image' width='100'></td>
                                        <td>
                                            <a href='category.php?id=$idCategory' class='btn btn-warning btn-sm'>Modifier</a>
                                            <button class='btn btn-danger btn-sm delete-category' data-id='$idCategory'>Supprimer</button>
                                        </td>
                                    </tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- JavaScript pour la gestion des catégories -->
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
            const addCategoryForm = document.getElementById('addCategoryForm');

            addCategoryForm.addEventListener('submit', async (event) => {
                event.preventDefault();

                const formData = new FormData(addCategoryForm);

                try {
                    const response = await fetch('categories.php', {
                        method: 'POST',
                        body: formData
                    });

                    const data = await response.json();
                    if (data.success) {
                        showAlert(data.message, 'success');
                        addCategoryForm.reset();
                        // Reload the page to reflect the changes
                        location.reload();
                    } else {
                        showAlert(data.message, 'danger');
                    }
                } catch (error) {
                    showAlert(`Erreur lors de la soumission du formulaire: ${error.message}`, 'danger');
                }
            });

            document.querySelectorAll('.delete-category').forEach(button => {
                button.addEventListener('click', async (event) => {
                    const categoryId = button.getAttribute('data-id');

                    const formData = new FormData();
                    formData.append('delete_category', true);
                    formData.append('category_id', categoryId);

                    try {
                        const response = await fetch('categories.php', {
                            method: 'POST',
                            body: formData
                        });

                        const data = await response.json();
                        if (data.success) {
                            showAlert(data.message, 'success');
                            // Reload the page to reflect the changes
                            location.reload();
                        } else {
                            showAlert(data.message, 'danger');
                        }
                    } catch (error) {
                        showAlert(`Erreur lors de la suppression de la catégorie: ${error.message}`, 'danger');
                    }
                });
            });
        });
    </script>
</body>

</html>
