<?php
session_start();
include 'db_connect.php';

// FIX (High): Redirect to correct login page — was 'login.php' which doesn't exist
if (!isset($_SESSION['user_id'])) {
    header("Location: user_login.php");
    exit;
}

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header('Location: index.php');
    exit;
}

$user_id = (int)$_SESSION['user_id'];

// FIX (High): Cart is now stored as an associative array (name, price, quantity)
// by add_to_cart.php — not as a bare integer quantity.
// Calculate total from the session values directly (price already stored in cart).
$total_amount = 0.0;
foreach ($_SESSION['cart'] as $item) {
    if (is_array($item)) {
        $total_amount += (float)$item['price'] * (int)$item['quantity'];
    }
}

// Insert the main order using a prepared statement
// FIX (Critical): Was string-interpolated — now fully parameterised
$stmt = $conn->prepare(
    "INSERT INTO orders (user_id, total_amount, status) VALUES (?, ?, 'pending')"
);
$stmt->bind_param("id", $user_id, $total_amount);

if ($stmt->execute()) {
    $order_id = $conn->insert_id;
    $stmt->close();

    // Clear the cart now that the order is saved
    unset($_SESSION['cart']);

    // FIX (Medium): Use the real logged-in username, not the hardcoded "Test Customer"
    $customer_name = htmlspecialchars($_SESSION['username'] ?? 'Customer', ENT_QUOTES, 'UTF-8');
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Order Success | Foodies</title>
        <style>
            body { font-family: Arial, sans-serif; background-color: #f4f4f4; text-align: center; padding: 50px; }
            .receipt-box { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); max-width: 500px; margin: 0 auto; }
            h1 { color: #27ae60; }
            .btn { background-color: #3498db; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; display: inline-block; margin-top: 20px; }
        </style>
    </head>
    <body>
        <div class="receipt-box">
            <h1>🎉 Order Placed Successfully!</h1>
            <p>Thank you for your order, <strong><?php echo $customer_name; ?></strong>!</p>
            <p>Your order number is: <strong>#<?php echo (int)$order_id; ?></strong></p>
            <h2>Total: $<?php echo number_format($total_amount, 2); ?></h2>
            <p>Your food is currently marked as <em>Pending</em>.</p>
            <a href="index.php" class="btn">Back to Menu</a>
        </div>
    </body>
    </html>
    <?php
} else {
    error_log("Order insert failed: " . $conn->error);
    echo "An error occurred while placing your order. Please try again.";
    $stmt->close();
}
?>
