<?php
// Product.php
session_start();
include('../config.php');
include('../functions.php');

$response = array('success' => false, 'message' => '');

$productID = isset($_GET['id']) ? intval($_GET['id']) : 0;
$product = getProductById($con, $productID);

if (!$product) {
    die('Produit non trouvé.');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nom = htmlspecialchars(trim($_POST['nom']));
    $description = htmlspecialchars(trim($_POST['description']));
    $prix = floatval($_POST['prix']);
    $stock = intval($_POST['stock']);
    $categorie_id = intval($_POST['categorie_id']);
    $materialsId = isset($_POST['material']) ? array_map('intval', $_POST['material']) : array();

    // Mise à jour du produit dans la base de données
    $result = updateProduct($con, $productID, $nom, $description, $prix, $stock, $categorie_id, $materialsId);

    if ($result) {
        $response['success'] = true;
        $response['message'] = 'Produit modifié avec succès!';
        $response['product_id'] = $productID;
    } else {
        $response['message'] = 'Erreur lors de la modification du produit: ' . $con->error;
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
    <title>Modifier Produit</title>
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

                    <h1 class="mb-4">Modifier le Produit</h1>
                    <form id="productForm" method="POST" enctype="multipart/form-data">
                        <div class="row mb-3">
                            <div class="col">
                                <label for="image1" class="form-label">Image 1:</label>
                                <input type="file" id="image1" name="image1" class="form-control" accept="image/*">
                                <div class="img-container">
                                    <img id="previewImage1" src="https://imgproduitnewvet.blob.core.windows.net/imagescontainer/<?php echo htmlspecialchars($productID); ?>.webp" alt="" class="img-preview mt-2">
                                </div>
                            </div>
                            <div class="col">
                                <label for="image2" class="form-label">Image 2:</label>
                                <input type="file" id="image2" name="image2" class="form-control" accept="image/*">
                                <div class="img-container">
                                    <img id="previewImage2" src="https://imgproduitnewvet.blob.core.windows.net/imagescontainer/<?php echo htmlspecialchars($productID); ?>_2.webp" alt="" class="img-preview mt-2">
                                </div>
                            </div>
                            <div class="col">
                                <label for="image3" class="form-label">Image 3:</label>
                                <input type="file" id="image3" name="image3" class="form-control" accept="image/*">
                                <div class="img-container">
                                    <img id="previewImage3" src="https://imgproduitnewvet.blob.core.windows.net/imagescontainer/<?php echo htmlspecialchars($productID); ?>_3.webp" alt="" class="img-preview mt-2">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="nom" class="form-label">Nom:</label>
                            <input type="text" id="nom" name="nom" class="form-control" value="<?php echo htmlspecialchars($product['nom']); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description:</label>
                            <textarea id="description" name="description" class="form-control" rows="3" required><?php echo htmlspecialchars($product['description']); ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="prix" class="form-label">Prix:</label>
                            <input type="number" id="prix" name="prix" class="form-control" step="0.01" required value="<?php echo htmlspecialchars($product['prix']); ?>">
                        </div>

                        <div class="mb-3">
                            <label for="stock" class="form-label">Stock:</label>
                            <input type="number" id="stock" name="stock" class="form-control" required value="<?php echo htmlspecialchars($product['stock']); ?>">
                        </div>

                        <div class="mb-3">
                            <label for='material' class='form-label'>Matériaux:</label><br/>
                            <?php
                            $materials = getMaterials($con);
                            $associatedMaterialIds = getMaterialsIDByProduct($con, $productID);

                            foreach ($materials as $material) {
                                $idMat = $material['id'];
                                $nomMat = htmlspecialchars($material['nom']);
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
                                    $selected = ($cat['id'] == $product['categorie_id']) ? ' selected' : '';
                                    echo "<option value='{$cat['id']}'$selected>{$cat['nom']}</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary">Modifier le Produit</button>
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

        const convertToWebP = (file) => {
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
                                reject(new Error('Erreur lors de la conversion en WebP.'));
                            }
                        }, 'image/webp');
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
                const convertedFile = await convertToWebP(file);

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

        document.getElementById('productForm').addEventListener('submit', async (event) => {
            event.preventDefault();
            const formData = new FormData(event.target);

            const productID = '<?php echo $productID; ?>';
            const files = [document.getElementById('image1').files[0], document.getElementById('image2').files[0], document.getElementById('image3').files[0]];
            const blobNames = [`${productID}.webp`, `${productID}_2.webp`, `${productID}_3.webp`];
            const container = 'imagescontainer';

            for (let i = 0; i < files.length; i++) {
                await uploadImage(files[i], container, blobNames[i]);
            }

            try {
                const response = await fetch(window.location.href, {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();

                if (result.success) {
                    showAlert(result.message, 'success');
                    setTimeout(() => {
                        window.location.href = 'products.php';
                    }, 2000);
                } else {
                    showAlert(result.message, 'danger');
                }
            } catch (error) {
                showAlert('Erreur lors de la modification du produit: ' + error.message, 'danger');
            }
        });

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
        document.getElementById('image2').addEventListener('change', (e) => previewImage(e, 'previewImage2'));
        document.getElementById('image3').addEventListener('change', (e) => previewImage(e, 'previewImage3'));
    </script>
</body>
</html>
