// Fonction pour ajouter un produit au panier
function addToCart(productID, quantity) {
    // Créer une nouvelle requête AJAX
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'cart_manager.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    
    // Préparer les données à envoyer
    const data = `productID=${encodeURIComponent(productID)}&quantity=${encodeURIComponent(quantity)}`;
    
    // Fonction appelée lorsque la requête est terminée
    xhr.onreadystatechange = function() {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                // La réponse du serveur est réussie
                console.log('Produit ajouté au panier avec succès.');
                // Ici, tu pourrais appeler une fonction pour mettre à jour l'interface utilisateur si nécessaire
            } else {
                // La réponse du serveur indique une erreur
                console.error('Erreur lors de l\'ajout du produit au panier.');
            }
        }
    };
    
    // Envoyer la requête avec les données
    xhr.send(data);
}

// Exemple d'appel de la fonction (à mettre en place lorsque le bouton est cliqué)
// addToCart('123', 2); // Remplacer '123' par l'ID du produit et 2 par la quantité souhaitée
