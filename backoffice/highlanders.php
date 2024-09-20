<?php
session_start();

include_once "../config.php";
include_once "../API/usersRequests.php";
include_once "../API/productsRequests.php";
include_once "../API/categoriesRequests.php";

$user = GetCurrentUser($pdo);

if($user === false){
    header('Location: ../index.php');
    exit;
}

if($user['est_admin'] == 0){
    header('Location: ../index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $category1 = $_POST['category1'];
    $category2 = $_POST['category2'];
    $category3 = $_POST['category3'];
    $category4 = $_POST['category4'];

    if ($category1 == $category2 || $category1 == $category3 || $category1 == $category4 || $category2 == $category3 || $category2 == $category4 || $category3 == $category4) {
        echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                Les catégories en avant doivent être différentes.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>';
    } else {
        if (SetHighlightCategories($pdo, $category1, $category2, $category3, $category4)) {
            echo "<div class='alert alert-success alert-dismissible fade show' role='alert'>
                    Catégories en avant modifiées avec succès.
                    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                </div>";
        } else {
            echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                    Erreur lors de la modification des catégories en avant.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                  </div>';
        }
    }

    $product1 = $_POST['product1'];
    $product2 = $_POST['product2'];
    $product3 = $_POST['product3'];
    $product4 = $_POST['product4'];

    if ($product1 == $product2 || $product1 == $product3 || $product1 == $product4 || $product2 == $product3 || $product2 == $product4 || $product3 == $product4) {
        echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                Les produits en avant doivent être différents.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>';
    } else {
        if (SetHighlightProducts($pdo, $product1, $product2, $product3, $product4)) {
            echo "<div class='alert alert-success alert-dismissible fade show' role='alert'>
                    Produits en avant modifiés avec succès.
                    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                </div>";
        } else {
            echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                    Erreur lors de la modification des produits en avant.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                  </div>';
        }
    }
}
?>


<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Highlanders</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <link rel="icon" type="image/x-icon" href="../assets/img/logo-black.png" />

    <style>
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
            <div class="col-md-2 p-0 bg-dark text-white">
                <?php include 'navbar.php'; ?>
            </div>

            <!-- Main content area -->
            <div class="col-md-10 p-0">
                <div id="alertContainer"></div>

                <?php include 'header.php'; ?>

                <div class="mx-2 mb-5">
                    <h1>Carroussel Accueil</h1>
                    <div class="mb-3">
                        <div id="alertImage1" class="alert-container mb-2"></div>
                        <label for="image1">Image 1</label>
                        <input type="file" name="image1" id="image1" class="form-control mb-3" accept="image/jpeg">
                        <img id="previewImage1" src="https://imgproduitnewvet.blob.core.windows.net/imagescarousel/img_carousel_1.jpg" alt="" class="img-preview mt-2">
                    </div>

                    <div class="mb-3">
                        <div id="alertImage2" class="alert-container mb-2"></div>
                        <label for="image2">Image 2</label>
                        <input type="file" name="image2" id="image2" class="form-control mb-3" accept="image/jpeg">
                        <img id="previewImage2" src="https://imgproduitnewvet.blob.core.windows.net/imagescarousel/img_carousel_2.jpg" alt="" class="img-preview mt-2">
                    </div>

                    <div class="mb-3">
                        <div id="alertImage3" class="alert-container mb-2"></div>
                        <label for="image3">Image 3</label>
                        <input type="file" name="image3" id="image3" class="form-control mb-3" accept="image/jpeg">
                        <img id="previewImage3" src="https://imgproduitnewvet.blob.core.windows.net/imagescarousel/img_carousel_3.jpg" alt="" class="img-preview mt-2">
                    </div>
                    <button id="uploadBtn" class="btn btn-dark">Upload les images</button>
                </div>

                <hr />

                <form class="container-fluid mt-4" method="POST">
                    <h1>Catégories en avant</h1>
                    <div class="table-responsive">
                        <?php
                        $categories = GetCategories($pdo);
                        $highlightCategories = GetHighlightCategories($pdo);
                        function generateOptions($categories, $highlightedId)
                        {
                            $options = "";
                            foreach ($categories as $category) {
                                $selected = $category['id'] == $highlightedId ? "selected" : "";
                                $options .= "<option value='" . $category['id'] . "' $selected>" . $category['nom'] . "</option>";
                            }
                            return $options;
                        }
                        ?>

                        <label for="category1" class="form-label">Catégorie 1 :</label>
                        <select class="form-select mb-3" name="category1">
                            <?php echo generateOptions($categories, $highlightCategories[0]["id"]); ?>
                        </select>

                        <label for="category2" class="form-label">Catégorie 2 :</label>
                        <select class="form-select mb-3" name="category2">
                            <?php echo generateOptions($categories, $highlightCategories[1]["id"]); ?>
                        </select>

                        <label for="category3" class="form-label">Catégorie 3 :</label>
                        <select class="form-select mb-3" name="category3">
                            <?php echo generateOptions($categories, $highlightCategories[2]["id"]); ?>
                        </select>

                        <label for="category4" class="form-label">Catégorie 4 :</label>
                        <select class="form-select mb-3" name="category4">
                            <?php echo generateOptions($categories, $highlightCategories[3]["id"]); ?>
                        </select>
                    </div>

                    <h1>Produits en avant</h1>
                    <div class="table-responsive">
                        <?php
                        $products = GetProducts($pdo);
                        $highlightProducts = getHighlightProducts($pdo);

                        function generateProductOptions($products, $highlightedId)
                        {
                            $options = "";
                            foreach ($products as $product) {
                                $selected = $product['id'] == $highlightedId ? "selected" : "";
                                $options .= "<option value='" . $product['id'] . "' $selected>" . $product['nom'] . "</option>";
                            }
                            return $options;
                        }
                        ?>

                        <label for="product1" class="form-label">Produit 1 :</label>
                        <select class="form-select mb-3" name="product1">
                            <?php echo generateProductOptions($products, $highlightProducts[0]['id']); ?>
                        </select>

                        <label for="product2" class="form-label">Produit 2 :</label>
                        <select class="form-select mb-3" name="product2">
                            <?php echo generateProductOptions($products, $highlightProducts[1]['id']); ?>
                        </select>

                        <label for="product3" class="form-label">Produit 3 :</label>
                        <select class="form-select mb-3" name="product3">
                            <?php echo generateProductOptions($products, $highlightProducts[2]['id']); ?>
                        </select>

                        <label for="product4" class="form-label">Produit 4 :</label>
                        <select class="form-select mb-3" name="product4">
                            <?php echo generateProductOptions($products, $highlightProducts[3]['id']); ?>
                        </select>

                    </div>

                    <button type="submit" class="btn btn-dark">Enregistrer</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    <script>
        //diable uploadbutton submit and add event listernet
        document.getElementById('uploadBtn').addEventListener('click', async (e) => {
            e.preventDefault();
            await uploadImages();
        });

        async function uploadImages() {

            const image1 = document.getElementById('image1').files[0];
            if (image1) {
                const image1Name = 'img_carousel_1.jpg';
                const alertContainer1 = document.getElementById('alertImage1');

                await uploadToAzureBlob(image1, image1Name, alertContainer1);
                document.getElementById('image1').value = '';
            }

            const image2 = document.getElementById('image2').files[0];
            if (image2) {
                const image2Name = 'img_carousel_2.jpg';
                const alertContainer2 = document.getElementById('alertImage2');

                await uploadToAzureBlob(image2, image2Name, alertContainer2);
                document.getElementById('image2').value = '';
            }

            const image3 = document.getElementById('image3').files[0];
            if (image3) {
                const image3Name = 'img_carousel_3.jpg';
                const alertContainer3 = document.getElementById('alertImage3');

                await uploadToAzureBlob(image3, image3Name, alertContainer3);
                document.getElementById('image3').value = '';
            }
        }

        async function uploadToAzureBlob(imageBlob, imageName, alertContainer) {
            const formData = new FormData();
            formData.append('file', imageBlob);
            formData.append('blobName', imageName);
            formData.append('container', 'imagescarousel');

            try {
                const response = await fetch('upload.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (response.ok) {
                    showAlert(alertContainer, 'Succès', `Image uploadée avec succès: ${result.message}`, 'success');
                } else {
                    showAlert(alertContainer, 'Erreur', `Erreur lors de l'upload: ${result.message}`, 'danger');
                }
            } catch (error) {
                showAlert(alertContainer, 'Erreur', `Erreur lors de l'upload de ${imageName}: ${error.message}`, 'danger');
            }
        }

        function showAlert(container, title, message, type) {
            container.innerHTML = `
    <div class="alert alert-${type} alert-dismissible fade show" role="alert">
        <strong>${title}:</strong> ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
`;
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
</body>

</html>