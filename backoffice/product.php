<?php
session_start();

include_once "../config.php";
include_once "../API/usersRequests.php";
include_once "../API/productsRequests.php";
include_once "../API/categoriesRequests.php";
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

if (!isset($_GET['id'])) {
    die('Produit non trouvé.');
}

$productID = intval($_GET['id']);
$product = GetProductById($pdo, $productID, 0);


if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    //check if everything is in post array
    if (!isset($_POST['nom']) || !isset($_POST['description']) || !isset($_POST['prix']) || !isset($_POST['stock']) || !isset($_POST['categorie_id']) || !isset($_POST['isActive'])) {
        DisplayDismissibleAlert("Veuillez remplir tous les champs.");
        $product = GetProductById($pdo, $productID, 0);
    }else{
        $nom = $_POST['nom'];
        $description = $_POST['description'];
        $prix = $_POST['prix'];
        $stock = $_POST['stock'];
        $categorie_id = $_POST['categorie_id'];
        $material = isset($_POST['material']) ? $_POST['material'] : [];
        $isActive = $_POST['isActive'];
    
    
        $result = UpdateProduct($pdo, $productID, $nom, $description, $prix, $stock, $categorie_id, $material, $isActive);
    
        if ($result) {
            DisplayDismissibleSuccess("Produit modifié avec succès.");
        } else {
            DisplayDismissibleAlert("Erreur lors de la modification du produit.");
        }
    
        $product = GetProductById($pdo, $productID, 0);
    }
}


?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Produit</title>

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

                    <h1 class="mb-4">Modifier Produit</h1>
                    <form id="productForm" method="POST" enctype="multipart/form-data">
                        <div class="row mb-3">
                            <div class="col">
                                <label for="image1" class="form-label">Image 1:</label>
                                <input type="file" id="image1" name="image1" class="form-control" accept="image/webp">
                                <div class="img-container">
                                    <img id="previewImage1" src="<?php echo PATH_PRODUCTS_IMAGES. $productID; ?>.webp" alt="" class="img-preview mt-2">
                                </div>
                            </div>
                            <div class="col">
                                <label for="image2" class="form-label">Image 2:</label>
                                <input type="file" id="image2" name="image2" class="form-control" accept="image/webp">
                                <div class="img-container">
                                    <img id="previewImage2" src="<?php echo PATH_PRODUCTS_IMAGES. $productID; ?>_2.webp" alt="" class="img-preview mt-2">
                                </div>
                            </div>
                            <div class="col">
                                <label for="image3" class="form-label">Image 3:</label>
                                <input type="file" id="image3" name="image3" class="form-control" accept="image/webp">
                                <div class="img-container">
                                    <img id="previewImage3" src="<?php echo PATH_PRODUCTS_IMAGES. $productID; ?>_3.webp" alt="" class="img-preview mt-2">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="nom" class="form-label">Nom:</label>
                            <input type="text" id="nom" name="nom" class="form-control" value="<?php echo $product['nom']; ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description:</label>
                            <textarea id="description" name="description" class="form-control" rows="3" required><?php echo $product['description']; ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="prix" class="form-label">Prix:</label>
                            <input type="number" id="prix" name="prix" class="form-control" step="0.01" required value="<?php echo $product['prix']; ?>">
                        </div>

                        <div class="mb-3">
                            <label for="stock" class="form-label">Stock:</label>
                            <input type="number" id="stock" name="stock" class="form-control" required value="<?php echo $product['stock']; ?>">
                        </div>

                        <div class="mb-3">
                            <label for='material' class='form-label'>Matériaux:</label><br />
                            <?php
                            $materials = GetMaterials($pdo,0);
                            $associatedMaterialIds = GetMaterialsIDByProduct($pdo, $productID);

                            if($associatedMaterialIds == false){
                                $associatedMaterialIds = [];
                            }

                            foreach ($materials as $material) {
                                $idMat = $material['id'];
                                $nomMat = $material['nom'];
                                $checked = in_array($idMat, $associatedMaterialIds) ? 'checked' : '';
                                echo "<input type='checkbox' class='mx-2' id='material$idMat' name='material[]' value='$idMat' $checked>$nomMat</input>";
                            }
                            ?>
                        </div>

                        <div class="mb-3">
                            <label for="categorie_id" class="form-label">Catégorie:</label>
                            <select id="categorie_id" name="categorie_id" class="form-select">
                                <?php
                                $categories = GetCategories($pdo);
                                foreach ($categories as $cat) {
                                    $selected = ($cat['id'] == $product['categorie_id']) ? ' selected' : '';
                                    echo "<option value='{$cat['id']}'$selected>{$cat['nom']}</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="mb-5">
                            <label for="isActive" class="form-label">Est Actif:</label>
                            <select id="isActive" name="isActive" class="form-select">
                                <option value="1" <?php echo $product['est_actif'] == 1 ? 'selected' : ''; ?>>Oui</option>
                                <option value="0" <?php echo $product['est_actif'] == 0 ? 'selected' : ''; ?>>Non</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-dark mb-3">Modifier le Produit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>


    <script>
        document.getElementById('productForm').addEventListener('submit', (e) => {
            e.preventDefault();
            document.querySelector('button[type="submit"]').disabled = true;
            
            uploadImages();
        });

        async function uploadImages() {

            const image1 = document.getElementById('image1').files[0];
            if (image1) {
                const image1Name = '<?php echo $productID; ?>.webp';

                await uploadToAzureBlob(image1, image1Name);
            }

            const image2 = document.getElementById('image2').files[0];
            if (image2) {
                const image2Name = '<?php echo $productID; ?>_2.webp';

                await uploadToAzureBlob(image2, image2Name);
            }

            const image3 = document.getElementById('image3').files[0];
            if (image3) {
                const image3Name = '<?php echo $productID; ?>_3.webp';

                await uploadToAzureBlob(image3, image3Name);
            }

            document.getElementById('productForm').submit();
        }

        async function uploadToAzureBlob(imageBlob, imageName) {
            const formData = new FormData();
            formData.append('file', imageBlob);
            formData.append('blobName', imageName);
            formData.append('container', '<?php echo PRODUCT_IMAGES_CONTAINER; ?>');

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
                alert(`Erreur lors de l'upload de ${imageName} vers <?php echo PRODUCT_IMAGES_CONTAINER?>: ${error.message}`);
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
        document.getElementById('image2').addEventListener('change', (e) => previewImage(e, 'previewImage2'));
        document.getElementById('image3').addEventListener('change', (e) => previewImage(e, 'previewImage3'));
    </script>
    <script>
        if ( window.history.replaceState ) {
            window.history.replaceState( null, null, window.location.href );
        }
    </script>
</body>

</html>