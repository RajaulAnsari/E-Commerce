<script>
// Function to update cart quantity
function updateCartQuantity(pid) {
    const quantity = document.getElementById('quantity_' + pid).value;

    // Send AJAX request to updatecart.php
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'updatecart.php', true);
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
        if (xhr.status === 200) {
            const response = JSON.parse(xhr.responseText);
            if (response.success) {
                // Quantity updated successfully
                // Now update the total price
                updateTotalPrice();
            } else {
                // Failed to update quantity
                console.error('Failed to update quantity');
            }
        } else {
            console.error('Error updating quantity');
        }
    };
    xhr.send(`quantity=${quantity}&pid=${pid}`);
}

// Function to update total price
function updateTotalPrice() {
    // Send AJAX request to fetch updated total price
    const xhr = new XMLHttpRequest();
    xhr.open('GET', 'gettotal.php', true);
    xhr.onload = function() {
        if (xhr.status === 200) {
            const total = xhr.responseText;
            document.getElementById('cart-total').innerHTML = total;
        } else {
            console.error('Error fetching total price');
        }
    };
    xhr.send();
}
</script>