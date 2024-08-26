// Fonction pour afficher une alerte Bootstrap
const showAlert = (message, type) => {
    const alertContainer = document.getElementById('alertContainer');
    alertContainer.innerHTML = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss='alert' aria-label='Close'></button>
        </div>
    `;
};

// Fonction pour convertir une image en WebP
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

// Fonction pour uploader une image
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
        // Convertir l'image en WebP
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

// Fonction pour gérer les uploads en fonction des containers
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

// Fonction de soumission du formulaire
const submitProductForm = async (event) => {
    event.preventDefault();

    const idProduit = new URLSearchParams(window.location.search).get('id');
    const fileInputs = ['image1', 'image2', 'image3'];

    // Gestion des uploads vers le container "imagescontainer"
    await handleUploads(idProduit, fileInputs, 'imagescontainer');

    // Soumettre le formulaire
    document.getElementById('productForm').submit();
};
