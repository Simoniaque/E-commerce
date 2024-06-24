function uploadCarouselImage(filename) {
    let fileInputId;
    let buttonId;

    switch (filename) {
        case 'img_carousel_1.jpg':
            fileInputId = 'imgCarousel1';
            buttonId = 'btnAddCarousel1';
            break;
        case 'img_carousel_2.jpg':
            fileInputId = 'imgCarousel2';
            buttonId = 'btnAddCarousel2';
            break;
        case 'img_carousel_3.jpg':
            fileInputId = 'imgCarousel3';
            buttonId = 'btnAddCarousel3';
            break;
        default:
            alert('Nom de fichier non reconnu.');
            return;
    }

    const fileInput = document.getElementById(fileInputId);
    const file = fileInput.files[0];
    const submitButton = document.getElementById(buttonId);

    if (file) {
        const allowedTypes = ['image/jpeg', 'image/jpg'];
        const maxSize = 3 * 1024 * 1024; // 3 MB

        if (!allowedTypes.includes(file.type)) {
            alert('Veuillez sélectionner une image de type JPEG ou JPG.');
            return;
        }

        if (file.size > maxSize) {
            alert('La taille de l\'image ne doit pas dépasser 5 Mo.');
            return;
        }

        // Désactiver le bouton pendant l'envoi
        submitButton.disabled = true;

        const formData = new FormData();
        formData.append('file', file);
        formData.append('uploadType', 'carousel');

        let url = `upload.php?filename=${encodeURIComponent(filename)}`;

        fetch(url, {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            console.log(data);
            alert(data);
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Erreur lors du téléchargement du fichier.');
        })
        .finally(() => {
            // Réactiver le bouton après l'envoi (qu'il réussisse ou échoue)
            submitButton.disabled = false;
        });
    } else {
        alert('Veuillez choisir un fichier.');
    }
}

function uploadProduct() {
    const productName = document.getElementById('productName').value;
    const productImg = document.getElementById('productImg').files[0];
    const productCategory = document.getElementById('productCategory').value;
    const productPrice = document.getElementById('productPrice').value;
    const productStock = document.getElementById('productStock').value;
    const productDescription = document.getElementById('productDescription').value;

    const formData = new FormData();
    formData.append('productName', productName);
    formData.append('productImg', productImg);
    formData.append('productCategory', productCategory);
    formData.append('productPrice', productPrice);
    formData.append('productStock', productStock);
    formData.append('productDescription', productDescription);
    formData.append('uploadType', 'product');

    fetch('upload.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        console.log(data);
        alert(data);
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Erreur lors de l\'ajout du produit.');
    });
}