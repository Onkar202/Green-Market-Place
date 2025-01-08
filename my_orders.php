<?php
session_start(); // Start the session
include('include/db_con.php'); // Update this line to match your actual file path

// At the top of the file, after including db_con.php
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch orders for the current user
$sql = "SELECT o.*, u.state, s.state_name 
        FROM orders o
        JOIN users u ON o.user_id = u.id
        JOIN states s ON u.state = s.state_id
        WHERE o.user_id = ?
        ORDER BY o.created_at DESC";

$stmt = $con->prepare($sql);
if ($stmt === false) {
    die("Prepare failed: " . $con->error);
}

if (!$stmt->bind_param("i", $user_id)) {
    die("Binding parameters failed: " . $stmt->error);
}

if (!$stmt->execute()) {
    die("Execute failed: " . $stmt->error);
}

$orders_result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>My Orders - Green Market Place</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-4">My Orders</h2>
        <?php if ($orders_result->num_rows > 0): ?>
            <?php while ($order = $orders_result->fetch_assoc()): ?>
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Order #<?php echo $order['order_id']; ?></h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Order Date:</strong> <?php echo date('F j, Y', strtotime($order['created_at'])); ?></p>
                        <p><strong>Total Amount:</strong> ₹<?php echo number_format($order['total_price'], 2); ?></p>
                        <p><strong>Total Quantity:</strong> <?php echo $order['total_quantity']; ?></p>
                        <p><strong>Payment Method:</strong> <?php echo $order['payment_method']; ?></p>
                        <p><strong>Shipping Address:</strong> <?php echo $order['address']; ?>, <?php echo $order['state_name']; ?></p>
                        
                        <h6 class="mt-4">Order Items:</h6>
                        <?php
                        $items_sql = "SELECT oi.*, p.pr_name 
                                      FROM order_items oi
                                      JOIN products p ON oi.product_id = p.pr_id
                                      WHERE oi.order_id = ?";
                        $items_stmt = $con->prepare($items_sql);
                        $items_stmt->bind_param("i", $order['order_id']);
                        $items_stmt->execute();
                        $items_result = $items_stmt->get_result();
                        ?>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th>Price</th>
                                    <th>Total Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($item = $items_result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo $item['pr_name']; ?></td>
                                        <td><?php echo $item['quantity']; ?></td>
                                        <td>₹<?php echo number_format($item['price'], 2); ?></td>
                                        <td>₹<?php echo number_format($item['total_price'], 2); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>You haven't placed any orders yet.</p>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
