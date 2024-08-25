function addToCart(productID, quantity, action = 'add') {
    // Créer une nouvelle requête AJAX
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'cart_manager.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    
    // Préparer les données à envoyer
    const data = `productID=${encodeURIComponent(productID)}&quantity=${encodeURIComponent(quantity)}&action=${encodeURIComponent(action)}`;
    
    // Fonction appelée lorsque la requête est terminée
    xhr.onreadystatechange = function() {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                // La réponse du serveur est réussie
                alert('Le produit a été ajouté au panier.');
                
                // Mettre à jour le header après l'ajout au panier
                window.location.reload();

            } else {
                alert('Une erreur est survenue lors de l\'ajout du produit au panier.');
            }
        }
    };
    
    // Envoyer la requête avec les données
    xhr.send(data);
}
