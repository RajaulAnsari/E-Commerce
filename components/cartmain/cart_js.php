<script>
function updateCartQuantity(pid) {
    const quantity = document.getElementById('quantity_' + pid).value;

    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'updatecart.php', true);
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
        if (xhr.status === 200) {
            const response = xhr.responseText;
            if (response === 'success') {
                // Quantity updated successfully, optionally update UI
            } else {
                // Handle error
                console.error('Failed to update quantity');
            }
        } else {
            // Handle network error
            console.error('Error updating quantity');
        }
    };
    xhr.onerror = function() {
        // Handle network error
        console.error('Network error occurred');
    };
    xhr.send('product_id=' + pid + '&quantity=' + quantity);
}
</script>