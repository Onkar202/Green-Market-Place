<?php
session_start();
include '../include/db_con.php';

// Check if the user is logged in and has admin privileges
// You should implement proper authentication here

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $product_id = $_GET['id'];
    
    // Prepare the update statement instead of delete
    $stmt = $con->prepare("UPDATE products SET active = 0 WHERE pr_id = ?");
    $stmt->bind_param("i", $product_id);
    
    // Execute the statement
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Product successfully removed from active listing.";
    } else {
        $_SESSION['error_message'] = "Error removing product: " . $con->error;
    }
    
    $stmt->close();
} else {
    $_SESSION['error_message'] = "Invalid product ID.";
}

// Redirect back to the view products page
header("Location: viewproducts.php");
exit();
?>
