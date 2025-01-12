<?php
session_start();
include('include/db_con.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';

// Debugging function
function debug_to_console($data) {
    echo "<script>console.log(" . json_encode($data) . ");</script>";
}

// Debugging: Output session contents at the start
debug_to_console("Session contents at start of checkout.php: " . json_encode($_SESSION));

ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);

if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart']) || empty($_SESSION['cart'])) {
    echo "<p>Your cart is empty. <a href='index.php'>Go to Homepage</a></p>";
    exit();
}

$error = '';
$success = '';

function getUserDetails($user_id) {
    global $con;

    // Check database connection
    if ($con->connect_error) {
        error_log("Database connection failed: " . $con->connect_error);
        return false;
    }

    // Fetch user details from the database
    $sql = "SELECT email, first_name, last_name FROM users WHERE user_id = ?";
    $user_stmt = $con->prepare($sql);
    if ($user_stmt === false) {
        error_log("Prepare failed: " . $con->error);
        error_log("SQL: " . $sql);
        error_log("User ID: " . $user_id);
        return false;
    }
    
    if (!$user_stmt->bind_param("i", $user_id)) {
        error_log("Binding parameters failed: " . $user_stmt->error);
        return false;
    }
    
    if (!$user_stmt->execute()) {
        error_log("Execute failed: " . $user_stmt->error);
        return false;
    }
    
    $user_result = $user_stmt->get_result();
    $user = $user_result->fetch_assoc();

    if (!$user) {
        error_log("User not found for user_id: " . $user_id);
        return false;
    }

    return $user;
}

function sendOrderConfirmationEmail($user_id, $order_id, $total_quantity, $total_price) {
    $user = getUserDetails($user_id);
    if (!$user) {
        error_log("Failed to get user details for User ID: $user_id");
        return false;
    }

    $user_email = $user['email'];
    $user_name = $user['first_name'] . ' ' . $user['last_name'];

    // Create a new PHPMailer instance
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'your gmail id';
        $mail->Password   = 'your ';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Recipients
        $mail->setFrom('your gmail id', 'Green Market Place');
        $mail->addAddress($user_email, $user_name);

        // Content
        $mail->isHTML(true);
        $mail->Subject = "Order Confirmation - Your Order #$order_id";
        $mail->Body    = "
            <html>
            <body>
                <h2>Thank you for your order from Green Market Place!</h2>
                <p>Dear $user_name,</p>
                <p>Your order has been successfully placed.</p>
                <p>Order Details:</p>
                <ul>
                    <li>Order ID: $order_id</li>
                    <li>Total Quantity: $total_quantity</li>
                    <li>Total Price: ₹$total_price</li>
                </ul>
                <p>Your order will be shipped soon.</p>
                <p>Thank you for shopping with Green Market Place!</p>
                <p>Best regards,<br>The Green Market Place Team</p>
            </body>
            </html>
        ";

        $mail->send();
        error_log("Email sent successfully to $user_email");
        return true;
    } catch (Exception $e) {
        error_log("Email sending failed. Mailer Error: {$mail->ErrorInfo}");
        return false;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $payment_method = isset($_POST['payment_method']) ? $_POST['payment_method'] : null;
    $address = isset($_POST['address']) ? $_POST['address'] : null;

    if ($payment_method && $address) {
        if (!isset($_SESSION['user_id'])) {
            $error = "User not logged in.";
        } else {
            $user_id = $_SESSION['user_id'];

            // Start transaction
            $con->begin_transaction();

            try {
                // Insert order into the database
                $stmt = $con->prepare("INSERT INTO orders (user_id, payment_method, address, created_at) VALUES (?, ?, ?, NOW())");
                $stmt->bind_param("iss", $user_id, $payment_method, $address);

                if ($stmt->execute()) {
                    $order_id = $stmt->insert_id;

                    $total_quantity = 0;
                    $total_price = 0;

                    // Insert each cart item into order_items
                    foreach ($_SESSION['cart'] as $product_id => $item) {
                        // Ensure $item is an array with a 'quantity' key
                        if (!is_array($item)) {
                            $item = ['quantity' => $item];
                        }

                        // Fetch product details from the database
                        $product_stmt = $con->prepare("SELECT pr_name, pr_price FROM products WHERE pr_id = ?");
                        $product_stmt->bind_param("i", $product_id);
                        $product_stmt->execute();
                        $product_result = $product_stmt->get_result();
                        $product = $product_result->fetch_assoc();

                        if (!$product) {
                            throw new Exception("Product not found: " . $product_id);
                        }

                        $price = $product['pr_price'];
                        $quantity = isset($item['quantity']) ? $item['quantity'] : 1;
                        $item_total = $price * $quantity;

                        $item_stmt = $con->prepare("INSERT INTO order_items (order_id, product_id, quantity, price, total_price) VALUES (?, ?, ?, ?, ?)");
                        $item_stmt->bind_param("iiidi", $order_id, $product_id, $quantity, $price, $item_total);
                        
                        if (!$item_stmt->execute()) {
                            throw new Exception("Error inserting order item: " . $item_stmt->error);
                        }

                        // Update product quantity in products table
                        $update_stmt = $con->prepare("UPDATE products SET pr_quantity = pr_quantity - ? WHERE pr_id = ?");
                        $update_stmt->bind_param("ii", $quantity, $product_id);
                        
                        if (!$update_stmt->execute()) {
                            throw new Exception("Error updating product quantity: " . $update_stmt->error);
                        }

                        $total_quantity += $quantity;
                        $total_price += $item_total;
                    }

                    // Update the order with total quantity and price
                    $update_order_stmt = $con->prepare("UPDATE orders SET total_quantity = ?, total_price = ? WHERE order_id = ?");
                    $update_order_stmt->bind_param("idi", $total_quantity, $total_price, $order_id);
                    
                    if (!$update_order_stmt->execute()) {
                        throw new Exception("Error updating order totals: " . $update_order_stmt->error);
                    }

                    // Commit transaction
                    $con->commit();

                    // Clear the cart after successful order
                    unset($_SESSION['cart']);

                    // Send email to user
                    if (sendOrderConfirmationEmail($user_id, $order_id, $total_quantity, $total_price)) {
                        $success = "Your order has been placed successfully! A confirmation email has been sent.";
                    } else {
                        // Updated error handling for email sending
                        $message = "Message could not be sent. Please check your email settings.";
                        error_log("Failed to send order confirmation email. User ID: $user_id, Order ID: $order_id");
                        $success = "Your order has been placed successfully! However, there was an issue sending the confirmation email. $message";
                    }
                } else {
                    throw new Exception("Error creating order: " . $stmt->error);
                }
            } catch (Exception $e) {
                // Rollback transaction on error
                $con->rollback();
                $error = "There was an error processing your order: " . $e->getMessage();
                error_log("Order processing error: " . $e->getMessage());
            }
        }
    } else {
        $error = "Please fill in all required fields.";
    }
}

// Debugging: Output final session contents
debug_to_console("Final session contents in checkout.php: " . json_encode($_SESSION));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }
        h1, h2 {
            color: #333;
        }
        form {
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            margin: 0 auto;
        }
        label {
            display: block;
            margin-top: 10px;
        }
        select, textarea {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        textarea {
            height: 100px;
        }
        button {
            display: block;
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 20px;
        }
        button:hover {
            background-color: #45a049;
        }
        ul {
            list-style-type: none;
            padding: 0;
        }
        li {
            background-color: #fff;
            margin-bottom: 5px;
            padding: 10px;
            border-radius: 4px;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        }
        a {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 15px;
            background-color: #008CBA;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s;
        }
        a:hover {
            background-color: #007B9A;
        }
        .success {
            color: green;
            font-weight: bold;
        }
        .error {
            color: red;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <h1>Checkout</h1>

    <?php if ($error): ?>
        <p class="error"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <?php if ($success): ?>
        <p class="success"><?php echo htmlspecialchars($success); ?></p>
    <?php else: ?>
        <form method="POST" action="">
            <label for="payment_method">Payment Method:</label>
            <select name="payment_method" id="payment_method" required>
                <option value="">Select Payment Method</option>
                <option value="Credit Card">Credit Card</option>
                <option value="Debit Card">Debit Card</option>
                <option value="PayPal">PayPal</option>
                <option value="Cash on Delivery">Cash on Delivery</option>
            </select>

            <label for="address">Delivery Address:</label>
            <textarea name="address" id="address" rows="4" required></textarea>

            <button type="submit">Place Order</button>
        </form>

        <h2>Order Summary</h2>
        <ul>
            <?php foreach ($_SESSION['cart'] as $product_id => $item): ?>
                <?php if (is_array($item) && isset($item['quantity'])): ?>
                    <li><?php echo htmlspecialchars((string)$item['quantity']); ?> x Product ID: <?php echo htmlspecialchars((string)$product_id); ?></li>
                <?php else: ?>
                    <li>1 x Product ID: <?php echo htmlspecialchars((string)$product_id); ?></li>
                <?php endif; ?>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <a href="cart.php">Back to Cart</a>
</body>
</html>
