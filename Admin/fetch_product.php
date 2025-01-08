<?php
include '../include/db_con.php'; // Include your database connection

if (isset($_GET['prId'])) {
    $prId = $_GET['prId'];
    $query = "SELECT * FROM products WHERE pr_id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("s", $prId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
        echo json_encode($product); // Return product data as JSON
    } else {
        echo json_encode(null); // No product found
    }

    $stmt->close();
}
?>