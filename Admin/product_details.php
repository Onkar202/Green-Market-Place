<?php
include('include/db_con.php');

// Get the product ID from the URL
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch product details from the database (replace this with your actual database query)
$product = null;
if ($product_id > 0) {
    // Example product data (replace this with your database query)
    $products = [
        1 => ["id" => 1, "image" => "images/products/fruits/apple.jpg", "title" => "Apple", "description" => "Fresh apples.", "price" => 1.99, "stock" => 100],
        2 => ["id" => 2, "image" => "images/products/Vegetables/spinich.jpeg", "title" => "Spinach", "description" => "Organic spinach.", "price" => 2.49, "stock" => 50],
        3 => ["id" => 3, "image" => "images/products/root/carrot.jpeg", "title" => "Carrot", "description" => "Crunchy carrots.", "price" => 1.79, "stock" => 75],
    ];

    $product = isset($products[$product_id]) ? $products[$product_id] : null;
}

// If product not found, redirect to the home page
if (!$product) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $product['title']; ?> - Green Market Place</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <style>
        .product-image {
            max-width: 100%;
            height: auto;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-6">
                <img src="<?php echo $product['image']; ?>" alt="<?php echo $product['title']; ?>" class="product-image">
            </div>
            <div class="col-md-6">
                <h1><?php echo $product['title']; ?></h1>
                <p class="lead"><?php echo $product['description']; ?></p>
                <p><strong>Price:</strong> $<?php echo number_format($product['price'], 2); ?></p>
                <p><strong>In Stock:</strong> <?php echo $product['stock']; ?></p>
                <form action="add_to_cart.php" method="post">
                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                    <div class="mb-3">
                        <label for="quantity" class="form-label">Quantity:</label>
                        <input type="number" class="form-control" id="quantity" name="quantity" value="1" min="1" max="<?php echo $product['stock']; ?>">
                    </div>
                    <button type="submit" class="btn btn-success">Add to Cart</button>
                </form>
                <a href="index.php" class="btn btn-secondary mt-3">Back to Products</a>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>