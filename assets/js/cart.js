document.addEventListener('DOMContentLoaded', () => {
    const updateCart = (productID, quantity, action = 'add') => {
        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'cart_manager.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = () => {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    const response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        console.log(response.message);
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
            document.querySelector(`p[id="${productID}"]`).textContent = `${totalPriceForProduct.toFixed(2)} €`;

            if (quantity === 0) {
                input.closest('tr').remove();
                updateCart(productID, 0, 'set');
                location.reload();
            } else {
                updateCart(productID, quantity, 'set');
            }
            updateTotals();
        });
    });

    const updateTotals = () => {
        const total = Array.from(document.querySelectorAll('p[id]'))
            .reduce((acc, priceElement) => acc + parseFloat(priceElement.textContent.replace(' €', '')), 0);
        document.querySelectorAll('.ca').forEach(el => el.textContent = `${total.toFixed(2)} €`);
        
    };
});
