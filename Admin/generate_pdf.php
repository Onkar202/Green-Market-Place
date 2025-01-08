<?php
include('../include/db_con.php');
mysqli_set_charset($con, 'utf8'); // Set the character set to UTF-8

require('../vendor/tecnickcom/tcpdf/tcpdf.php'); // Missing semicolon added here

if (isset($_GET['id'])) {
    $order_id = mysqli_real_escape_string($con, $_GET['id']);
    
    // Fetch order details including user information
    $query = "SELECT o.*, u.Name, u.user_name, u.Email, u.Mobile, u.address as user_address, u.city, s.state_name, u.zip
              FROM orders o 
              JOIN users u ON o.user_id = u.id 
              JOIN states s ON u.state = s.state_id
              WHERE o.order_id = '$order_id'";
    $result = mysqli_query($con, $query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $order = mysqli_fetch_assoc($result);
        
        // Fetch order items
        $items_query = "SELECT oi.quantity, oi.price, p.pr_name, p.pr_id
                        FROM order_items oi 
                        JOIN products p ON oi.product_id = p.pr_id
                        WHERE oi.order_id = '$order_id'";
        $items_result = mysqli_query($con, $items_query);
        
        // Create new PDF document
        $pdf = new TCPDF();
        $pdf->AddPage();
        $pdf->SetFont('helvetica', '', 12);
        
        // Add order details
        $pdf->Cell(0, 10, 'Order Details', 0, 1, 'C');
        $pdf->Cell(0, 10, 'Order ID: ' . $order['order_id'], 0, 1);
        $pdf->Cell(0, 10, 'Order Date: ' . date('Y-m-d H:i:s', strtotime($order['created_at'])), 0, 1);
        $pdf->Cell(0, 10, 'Payment Method: ' . $order['payment_method'], 0, 1);
        $pdf->Cell(0, 10, 'Total Quantity: ' . $order['total_quantity'], 0, 1);
        $pdf->Cell(0, 10, 'Total Price: ₹' . $order['total_price'], 0, 1);
        $pdf->Cell(0, 10, 'Customer Name: ' . $order['Name'], 0, 1);
        $pdf->Cell(0, 10, 'Email: ' . $order['Email'], 0, 1);
        $pdf->Cell(0, 10, 'Mobile: ' . $order['Mobile'], 0, 1);
        $pdf->Cell(0, 10, 'Address: ' . $order['user_address'] . ', ' . $order['city'] . ', ' . $order['state_name'] . ' - ' . $order['zip'], 0, 1);
        
        // Add items to the PDF
        $pdf->Cell(0, 10, 'Ordered Products:', 0, 1);
        while ($item = mysqli_fetch_assoc($items_result)) {
            $total = $item['quantity'] * $item['price'];
            $pdf->Cell(0, 10, 'Product ID: ' . $item['pr_id'] . ', Name: ' . $item['pr_name'] . ', Quantity: ' . $item['quantity'] . ', Price: ₹' . $item['price'] . ', Total: ₹' . $total, 0, 1);
        }

        // Output the PDF
        $pdf->Output('order_details.pdf', 'D');
    } else {
        echo "Order not found.";
    }
} else {
    echo "Invalid request.";
}

mysqli_close($con);
?>
