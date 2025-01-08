<?php
include '../include/db_con.php';

if (isset($_GET['id']) && isset($_GET['status'])) {
    $product_id = $_GET['id'];
    $current_status = $_GET['status'];
    
    // Toggle the status
    $new_status = $current_status ? 0 : 1;
    
    $query = "UPDATE products SET active = ? WHERE pr_id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("ii", $new_status, $product_id);
    
    if ($stmt->execute()) {
        header("Location: viewproducts.php?success=1");
    } else {
        header("Location: viewproducts.php?error=1");
    }
    
    $stmt->close();
} else {
    header("Location: viewproducts.php?error=2");
}

mysqli_close($con);
?>
