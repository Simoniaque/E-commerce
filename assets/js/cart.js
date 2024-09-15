document.addEventListener('DOMContentLoaded', () => {
    const updateCart = (productID, quantity, action = 'add', callback) => {
        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'cart_manager.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = () => {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    const response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        if (callback) callback();
                    } else {
                        console.error(response.message);
                        location.reload();
                    }
                } else {
                    const response = JSON.parse(xhr.responseText);
                    console.error(response.message);
                    location.reload();
                }
            }
        };
        xhr.send(`productID=${encodeURIComponent(productID)}&quantity=${encodeURIComponent(quantity)}&action=${encodeURIComponent(action)}`);
    };

    document.querySelectorAll('input[name="quantity"]').forEach(input => {
        input.addEventListener('change', () => {
            const quantity = parseInt(input.value, 10);
            const productID = input.dataset.productId;
            const maxStock = parseInt(input.max, 10);
            
            if (isNaN(quantity) || quantity < 0 || quantity > maxStock) {
                input.value = maxStock;
                return;
            }

            const pricePerUnit = parseFloat(input.dataset.pricePerUnit);
            const totalPriceForProduct = quantity * pricePerUnit;
            document.querySelector(`p[id="${productID}"]`).textContent = `${totalPriceForProduct.toFixed(2).replace('.', ',')} €`;

            if (quantity === 0) {
                input.closest('tr').remove();
                updateCart(productID, 0, 'set', () => {
                    location.reload(); // Recharger la page après succès
                });
            } else {
                updateCart(productID, quantity, 'set');
            }
            updateTotals();
        });
    });

    const updateTotals = () => {
        const total = Array.from(document.querySelectorAll('p[id]'))
            .reduce((acc, priceElement) => {
                const priceText = priceElement.textContent.trim().replace(' €', '').replace(',', '.');
                const priceValue = parseFloat(priceText);
                
                return acc + (isNaN(priceValue) ? 0 : priceValue);
            }, 0);
        
        document.querySelectorAll('.ca').forEach(el => el.textContent = `${total.toFixed(2).replace('.', ',')} €`);
    };
});
