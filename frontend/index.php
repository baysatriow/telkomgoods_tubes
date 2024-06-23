<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Online Store</title>
    <link rel="stylesheet" href="style.css">
    <script>
        function editProduct(id, name, price, description) {
            document.getElementById('id').value = id;
            document.getElementById('name').value = name;
            document.getElementById('price').value = price;
            document.getElementById('description').value = description;
            window.scrollTo(0, document.body.scrollHeight); // Scroll to the form
        }

        function deleteProduct(id) {
    if (confirm('Are you sure you want to delete this product?')) {
                fetch(`http://localhost:8080/api/products?id=${id}`, { method: 'DELETE' })
                .then(response => {
                    if (response.ok) {
                        alert('Product deleted successfully');
                        window.location.reload(); // Reload the page to reflect the changes
                    } else {
                        alert('Failed to delete product');
                    }
                })
                .catch(error => alert('Error deleting product: ' + error));
            }
        }

    </script>
</head>
<body>
<h1>Welcome to Our Online Store</h1>
<h2>Products List</h2>
<ul id="products-list">
    <!-- Products will be listed here -->
</ul>

<h2>Add/Edit Product</h2>
<form action="add_product.php" method="POST" enctype="multipart/form-data"> <!-- Add enctype attribute -->
    <input type="hidden" id="id" name="id" value=""> <!-- Always empty for new products -->
    <label for="name">Name:</label>
    <input type="text" id="name" name="name" required><br>
    <label for="price">Price:</label>
    <input type="number" id="price" name="price" step="0.01" required><br>
    <label for="description">Description:</label>
    <textarea id="description" name="description" required></textarea><br>
    <label for="image">Image:</label> <!-- New image input field -->
    <input type="file" id="image" name="image" required><br>
    <input type="submit" value="Submit">
</form>



<script>
    function formatPrice(priceID) {
        return new Intl.NumberFormat('id-ID').format(priceID);
    }

    document.addEventListener("DOMContentLoaded", function() {
        fetch('http://localhost:8080/api/products')
        .then(response => response.json())
        .then(products => {
            const list = document.getElementById('products-list');
            products.forEach(product => {
                const item = document.createElement('li');
                const productPrice = formatPrice(product.price); // Correctly call formatPrice and assign its return value
                item.innerHTML = `${product.name} - Rp. ${productPrice} - ${product.description} 
                                  <img src="data:image/png;base64,${product.image}" alt="${product.name}" /> <!-- Display the base64 image -->
                                  <button onclick="editProduct('${product.id}', '${product.name}', '${product.price}', '${product.description}')">Edit</button>
                                  <button onclick="deleteProduct('${product.id}')">Delete</button>`;
                list.appendChild(item);
            });
        })
        .catch(error => console.error('Error fetching products:', error));
    });
</script>
</body>
</html>
