<?php
session_start();
include('include/db_con.php');

// Debugging function
function debug_to_console($data) {
    echo "<script>console.log(" . json_encode($data) . ");</script>";
}

// Debugging: Output session contents at the start
debug_to_console("Session contents at start of cart.php: " . json_encode($_SESSION));

// Initialize cart if not set
if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
    debug_to_console("Cart initialized as empty array in cart.php");
}

// Handle adding, updating, or removing items from the cart
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_to_cart'])) {
        $product_id = $_POST['product_id'];
        $quantity = intval($_POST['quantity']);
        
        if ($quantity > 0) {
            if (!isset($_SESSION['cart'][$product_id])) {
                $_SESSION['cart'][$product_id] = $quantity;
            } else {
                $_SESSION['cart'][$product_id] += $quantity;
            }
            debug_to_console("Item added to cart in cart.php");
        }
    } elseif (isset($_POST['update_cart'])) {
        foreach ($_POST['quantity'] as $product_id => $quantity) {
            $quantity = intval($quantity);
            if ($quantity > 0) {
                $_SESSION['cart'][$product_id] = $quantity;
            } else {
                unset($_SESSION['cart'][$product_id]);
            }
        }
        debug_to_console("Cart updated in cart.php");
    } elseif (isset($_POST['remove_item'])) {
        $product_id = $_POST['remove_item'];
        unset($_SESSION['cart'][$product_id]);
        debug_to_console("Item removed from cart in cart.php");
    }
}

// Calculate cart total and fetch product details
$cart_total = 0;
$cart_items = [];

if (!empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $product_id => $item) {
        $product_stmt = $con->prepare("SELECT pr_id, pr_name, pr_price FROM products WHERE pr_id = ?");
        $product_stmt->bind_param("i", $product_id);
        $product_stmt->execute();
        $product_result = $product_stmt->get_result();
        $product = $product_result->fetch_assoc();
        
        if ($product) {
            $quantity = is_array($item) ? $item['quantity'] : $item; // Handle both array and integer cases
            $total = $product['pr_price'] * $quantity;
            $cart_total += $total;
            $cart_items[] = [
                'pr_id' => $product['pr_id'],
                'pr_name' => $product['pr_name'],
                'pr_price' => $product['pr_price'],
                'quantity' => $quantity,
                'total' => $total
            ];
        }
    }
}

// Debugging: Output final cart contents
debug_to_console("Final cart contents in cart.php: " . json_encode($_SESSION['cart']));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }
        h1 {
            color: #333;
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        input[type="number"] {
            width: 60px;
            padding: 5px;
        }
        button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 15px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #45a049;
        }
        button[name="remove_item"] {
            background-color: #f44336;
        }
        button[name="remove_item"]:hover {
            background-color: #da190b;
        }
        a {
            display: inline-block;
            margin-top: 10px;
            padding: 10px 15px;
            background-color: #008CBA;
            color: white;
            text-decoration: none;
            transition: background-color 0.3s;
        }
        a:hover {
            background-color: #007B9A;
        }
    </style>
</head>
<body>
    <h1>Shopping Cart</h1>
    
    <?php if (empty($cart_items)): ?>
        <p>Your cart is empty.</p>
    <?php else: ?>
        <form method="post" action="">
            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Total</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cart_items as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['pr_name']); ?></td>
                            <td>₹<?php echo number_format($item['pr_price'], 2); ?></td>
                            <td>
                                <input type="number" name="quantity[<?php echo $item['pr_id']; ?>]" value="<?php echo $item['quantity']; ?>" min="0">
                            </td>
                            <td>₹<?php echo number_format($item['total'], 2); ?></td>
                            <td>
                                <button type="submit" name="remove_item" value="<?php echo $item['pr_id']; ?>">Remove</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3">Total:</td>
                        <td>₹<?php echo number_format($cart_total, 2); ?></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
            <button type="submit" name="update_cart">Update Cart</button>
        </form>
        <a href="checkout.php">Proceed to Checkout</a>
    <?php endif; ?>
    
    <a href="index.php">Continue Shopping</a>
</body>
</html>
