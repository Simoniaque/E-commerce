<?php
session_start(); // Démarrer la session pour les messages flash

include('../config.php');

include('../functions.php');

$id = intval($_GET['id']); // ID du produit à modifier, assurez-vous que c'est un entier

// Récupérer les informations du produit
$product = getProductById($con, $id);

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nom = $_POST['nom'];
    $description = $_POST['description'];
    $prix = floatval($_POST['prix']);
    $stock = intval($_POST['stock']);
    $categorie_id = intval($_POST['categorie_id']);
    $materialsId = $_POST['material'];


    $result = updateProduct($con, $id, $nom, $description, $prix, $stock, $categorie_id, $materialsId);



    if ($result) {
        $_SESSION['flash_message'] = [
            'type' => 'success',
            'message' => 'Produit mis à jour avec succès!'
        ];
    } else {
        $_SESSION['flash_message'] = [
            'type' => 'danger',
            'message' => 'Erreur lors de la mise à jour du produit: ' . $conn->error
        ];
    }

    header("Location: product.php?id=$id");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Produit</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="icon" href="../assets/img/logo-black.png" type="image/x-icon">

    <!-- Custom CSS for image preview -->
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
            <!-- Sidebar -->
            <div class="col-md-2 p-0">
                <?php include 'navbar.php'; ?>
            </div>

            <!-- Main content area -->
            <div class="col-md-10 p-0">
                <?php include 'header.php'; ?>

                <div class="container mt-4">
                    <div id="alertContainer"></div>
                    <?php
                    if (isset($_SESSION['flash_message'])) {
                        $flash = $_SESSION['flash_message'];
                        echo "<div class='alert alert-{$flash['type']} alert-dismissible fade show' role='alert'>
                                {$flash['message']}
                                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                              </div>";
                        unset($_SESSION['flash_message']);
                    }
                    ?>

                    <h1 class="mb-4">Modifier Produit</h1>
                    <form id="productForm" method="POST">

                        <div class="row mb-3">
                            <div class="col">
                                <label for="image1" class="form-label">Image 1:</label>
                                <input type="file" id="image1" name="image1" class="form-control" accept="image/*">
                                <div class="img-container">
                                    <img id="previewImage1" src="https://imgproduitnewvet.blob.core.windows.net/imagescontainer/<?php echo $id; ?>.webp" alt="Image 1" class="img-preview mt-2">
                                </div>
                            </div>

                            <div class="col">
                                <label for="image2" class="form-label">Image 2:</label>
                                <input type="file" id="image2" name="image2" class="form-control" accept="image/*">
                                <div class="img-container">
                                    <img id="previewImage2" src="https://imgproduitnewvet.blob.core.windows.net/imagescontainer/<?php echo $id; ?>_2.webp" alt="Image 2" class="img-preview mt-2">
                                </div>
                            </div>

                            <div class="col">
                                <label for="image3" class="form-label">Image 3:</label>
                                <input type="file" id="image3" name="image3" class="form-control" accept="image/*">
                                <div class="img-container">
                                    <img id="previewImage3" src="https://imgproduitnewvet.blob.core.windows.net/imagescontainer/<?php echo $id; ?>_3.webp" alt="Image 3" class="img-preview mt-2">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="nom" class="form-label">Nom:</label>
                            <input type="text" id="nom" name="nom" class="form-control" value="<?php echo htmlspecialchars($product['nom']); ?>">
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description:</label>
                            <textarea id="description" name="description" class="form-control" rows="3"><?php echo htmlspecialchars($product['description']); ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="prix" class="form-label">Prix:</label>
                            <input type="number" id="prix" name="prix" class="form-control" step="0.01" value="<?php echo htmlspecialchars($product['prix']); ?>">
                        </div>

                        <div class="mb-3">
                            <label for="stock" class="form-label">Stock:</label>
                            <input type="number" id="stock" name="stock" class="form-control" value="<?php echo htmlspecialchars($product['stock']); ?>">
                        </div>
                        <div class="mb-3">
                            <?php
                            $materials = getMaterials($con);
                            $associatedMaterialIds = getMaterialsIDByProduct($con, $product['id']);

                            foreach ($materials as $material) {
                                $idMat = $material['id'];
                                $nomMat = $material['nom'];

                                // Vérifiez si l'ID est dans le tableau des IDs associés
                                $checked = in_array($idMat, $associatedMaterialIds) ? 'checked' : '';
                                echo "<input type='checkbox' class='mx-2' id='material$idMat' name='material[]' value='$idMat' $checked>$nomMat</input>";
                            }
                            ?>
                        </div>

                        <div class="mb-3">
                            <label for="categorie_id" class="form-label">Catégorie:</label>
                            <select id="categorie_id" name="categorie_id" class="form-select">
                                <?php
                                $categories = getCategories($con);
                                foreach ($categories as $cat) {
                                    // Ajouter au select les catégories du produit
                                    echo "<option value='{$cat['id']}'" . ($cat['id'] == $product['categorie_id'] ? ' selected' : '') . ">{$cat['nom']}</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary">Mettre à jour</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="upload.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('productForm');
            form.addEventListener('submit', submitProductForm);
        });

        document.addEventListener('DOMContentLoaded', function() {
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
                        // Reset to the original image if no file is selected
                        const defaultSrc = `https://imgproduitnewvet.blob.core.windows.net/imagescontainer/${id.replace('image', id)}.webp`;
                        preview.src = defaultSrc;
                    }
                });
            });
        });
    </script>
</body>

</html>