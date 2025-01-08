<?php
session_start();
include('../include/db_con.php');

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Function to check if user is logged in as admin
function isAdminLoggedIn() {
    return isset($_SESSION['admin_id']) && !empty($_SESSION['admin_id']);
}

// Check if the user is logged in and is an admin
if (!isAdminLoggedIn()) {
    echo "Unauthorized access";
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            padding: 20px;
        }
        .order-details, .order-items {
            background-color: #ffffff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 20px;
        }
        h3 {
            color: #007bff;
            margin-bottom: 20px;
        }
        .table {
            margin-top: 20px;
        }
        .table th {
            background-color: #f8f9fa;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php
        if (isset($_GET['id'])) {
            $order_id = mysqli_real_escape_string($con, $_GET['id']);
            
            // Fetch order details including user information
            $query = "SELECT o.*, u.Name, u.user_name, u.Email, u.Mobile, u.address as user_address, u.city, s.state_name, u.zip
                      FROM orders o 
                      JOIN users u ON o.user_id = u.id 
                      JOIN states s ON u.state = s.state_id
                      WHERE o.order_id = '$order_id'
                      ORDER BY o.created_at DESC";  // Add this line to sort in descending order
            $result = mysqli_query($con, $query);
            
            if (!$result) {
                echo "<div class='alert alert-danger'>Error in main query: " . mysqli_error($con) . "</div>";
                exit();
            }
            
            if (mysqli_num_rows($result) > 0) {
                $order = mysqli_fetch_assoc($result);
                
                // Display order details
                echo "<div class='order-details'>";
                echo "<h3>Order Details</h3>";
                echo "<div class='row'>";
                echo "<div class='col-md-6'>";
                echo "<p><strong>Order ID:</strong> {$order['order_id']}</p>";
                echo "<p><strong>Order Date:</strong> " . date('Y-m-d H:i:s', strtotime($order['created_at'])) . "</p>";  // Format the date and time
                echo "<p><strong>Payment Method:</strong> {$order['payment_method']}</p>";
                echo "<p><strong>Total Quantity:</strong> {$order['total_quantity']}</p>";
                echo "<p><strong>Total Price:</strong> Rs.{$order['total_price']}</p>";
                echo "</div>";
                echo "<div class='col-md-6'>";
                echo "<p><strong>Customer Name:</strong> {$order['Name']}</p>";
                echo "<p><strong>Email:</strong> {$order['Email']}</p>";
                echo "<p><strong>Mobile:</strong> {$order['Mobile']}</p>";
                echo "<p><strong>Address:</strong> {$order['user_address']}, {$order['city']}, {$order['state_name']} - {$order['zip']}</p>";
                echo "</div>";
                echo "</div>";
                
                // Display address details
                echo "<div class='address-details'>";
                echo "<h3>Address Details</h3>";
                echo "<div class='row'>";
                echo "<div class='col-md-6'>";
                echo "<h4>Billing Address</h4>";
                echo "<p>{$order['Name']}</p>";
                echo "<p>{$order['user_address']}</p>";
                echo "<p>{$order['city']}, {$order['state_name']} - {$order['zip']}</p>";
                echo "</div>";
                echo "<div class='col-md-6'>";
                echo "<h4>Shipping Address</h4>";
                echo "<p>{$order['Name']}</p>";
                echo "<p>{$order['address']}</p>";
                echo "</div>";
                echo "</div>";
                
                // Fetch order items
                $items_query = "SELECT oi.quantity, oi.price, p.pr_name, p.pr_id
                                FROM order_items oi 
                                JOIN products p ON oi.product_id = p.pr_id
                                WHERE oi.order_id = '$order_id'";
                $items_result = mysqli_query($con, $items_query);
                
                if (!$items_result) {
                    echo "<div class='alert alert-danger'>Error in items query: " . mysqli_error($con) . "</div>";
                    exit();
                }
                
                $num_items = mysqli_num_rows($items_result);
                
                echo "<div class='order-items'>";
                echo "<h3>Ordered Products</h3>";
                echo "<p>Number of items: $num_items</p>";
                
                if ($num_items > 0) {
                    echo "<table class='table table-striped'>";
                    echo "<thead class='thead-light'><tr><th>Product ID</th><th>Product Name</th><th>Quantity</th><th>Price</th><th>Total</th></tr></thead>";
                    echo "<tbody>";
                    $grand_total = 0;
                    while ($item = mysqli_fetch_assoc($items_result)) {
                        $total = $item['quantity'] * $item['price'];
                        $grand_total += $total;
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($item['pr_id']) . "</td>";
                        echo "<td>" . htmlspecialchars($item['pr_name']) . "</td>";
                        echo "<td>" . $item['quantity'] . "</td>";
                        echo "<td>Rs." . number_format($item['price'], 2) . "</td>";
                        echo "<td>Rs." . number_format($total, 2) . "</td>";
                        echo "</tr>";
                    }
                    echo "</tbody>";
                    echo "<tfoot><tr><th colspan='4' class='text-right'>Grand Total</th><th>Rs." . number_format($grand_total, 2) . "</th></tr></tfoot>";
                    echo "</table>";
                } else {
                    echo "<p class='alert alert-warning'>No items found for this order.</p>";
                }
                echo "</div>";
            } else {
                echo "<div class='alert alert-warning'>Order not found</div>";
            }
        } else {
            echo "<div class='alert alert-danger'>Invalid request</div>";
        }

        // Close the database connection
        mysqli_close($con);
        ?>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
        <script>
            const { jsPDF } = window.jspdf;

            function downloadPDF() {
                const doc = new jsPDF();
                doc.setFontSize(16);
                doc.text("Order Details", 10, 10);
                doc.setFontSize(12);
                
                // Fetching order details from the PHP variables
                const orderDetails = <?php echo json_encode($order); ?>; // Ensure $order is defined in the PHP scope
                const items = <?php echo json_encode(mysqli_fetch_all($items_result, MYSQLI_ASSOC)); ?>; // Fetch all items

                // Adding order information to the PDF
                doc.text(`Order ID: ${orderDetails.order_id}`, 10, 20);
                doc.text(`Order Date: ${new Date(orderDetails.created_at).toLocaleString()}`, 10, 30);
                doc.text(`Payment Method: ${orderDetails.payment_method}`, 10, 40);
                doc.text(`Total Quantity: ${orderDetails.total_quantity}`, 10, 50);
                doc.text(`Total Price: Rs.${orderDetails.total_price}`, 10, 60);
                doc.text(`Customer Name: ${orderDetails.Name}`, 10, 70);
                doc.text(`Email: ${orderDetails.Email}`, 10, 80);
                doc.text(`Mobile: ${orderDetails.Mobile}`, 10, 90);
                doc.text(`Address: ${orderDetails.user_address}, ${orderDetails.city}, ${orderDetails.state_name} - ${orderDetails.zip}`, 10, 100);
                
                // Adding items to the PDF
                doc.text("Ordered Products:", 10, 110);
                let y = 120;
                items.forEach(item => {
                    const total = item.quantity * item.price;
                    doc.text(`Product ID: ${item.pr_id}, Name: ${item.pr_name}, Quantity: ${item.quantity}, Price: Rs.${item.price}, Total: Rs.${total}`, 10, y);
                    y += 10; // Move down for the next item
                });

                // Save the PDF
                doc.save("order_details.pdf");
            }
        </script>
        <div class="text-right mb-3">
            <a class="btn btn-primary" href="generate_pdf.php?id=<?php echo $order_id; ?>">Download PDF</a>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
