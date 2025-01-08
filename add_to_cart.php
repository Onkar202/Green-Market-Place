<?php
session_start();
include('include/db_con.php');

if (isset($_GET['product_id']) && isset($_GET['quantity'])) {
    $product_id = $_GET['product_id'];
    $quantity = $_GET['quantity'];
    $category = isset($_GET['category']) ? $_GET['category'] : '';

    // Fetch product price from the database
    $sql = "SELECT pr_price FROM products WHERE pr_id = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();

    if ($product) {
        $price = $product['pr_price'];
        $total_price = $price * $quantity;

        // Update session cart
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        // Add product to cart
        $_SESSION['cart'][$product_id] = [
            'quantity' => $quantity,
            'total_price' => $total_price
        ];

        // Calculate total items and total price
        $_SESSION['total_items'] = array_sum(array_column($_SESSION['cart'], 'quantity'));
        $_SESSION['total_price'] = array_sum(array_column($_SESSION['cart'], 'total_price'));
    }

    // After adding to cart, redirect back to the index page with the category
    $redirect_url = 'index.php';
    if ($category) {
        $redirect_url .= "?category=$category";
    }
    header("Location: $redirect_url");
    exit();
} else {
    // Handle error
    header("Location: index.php");
    exit();
}
?>
