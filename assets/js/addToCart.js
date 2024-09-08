function addToCart(productID, quantity) {
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'cart_manager.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    
    const data = `productID=${encodeURIComponent(productID)}&quantity=${encodeURIComponent(quantity)}`;
    
    xhr.onreadystatechange = function() {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            try {
                const response = JSON.parse(xhr.responseText);
                
                if (xhr.status === 200) {
                    alert(response.message || 'Le produit a été ajouté au panier.');
                    
                    window.location.reload();
                    
                } else {
                    alert(`Erreur: ${response.error}`);
                }
            } catch (e) {
                alert('Une erreur inattendue est survenue.');
                console.error('Réponse non valide reçue du serveur:', xhr.responseText);
            }
        }
    };
    
    xhr.send(data);
}