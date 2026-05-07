<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: user_login.php");
    exit;
}

$user_id      = (int)$_SESSION['user_id'];
$order_placed = false;

// CSRF token for the place-order form
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

// Handle Place Order
if (isset($_POST['place_order']) && !empty($_SESSION['cart'])) {

    // FIX (Critical): Verify CSRF token on any state-changing POST
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
        die("Invalid request.");
    }

    $grand_total = 0.0;
    foreach ($_SESSION['cart'] as $item) {
        if (is_array($item)) {
            // FIX (High): Ensure numeric types — these values came from session
            // (originally from POST), so cast defensively before arithmetic.
            $grand_total += (float)$item['price'] * (int)$item['quantity'];
        }
    }

    // FIX (Critical): Use a prepared statement — the original interpolated
    // $user_id and $grand_total directly into the query string.
    // FIX (High): Status stored as lowercase 'pending' to match normalised values.
    $stmt = $conn->prepare(
        "INSERT INTO orders (user_id, total_amount, status) VALUES (?, ?, 'pending')"
    );
    $stmt->bind_param("id", $user_id, $grand_total);

    if ($stmt->execute()) {
        unset($_SESSION['cart']);
        $order_placed = true;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Your Cart | Foodies</title>
    <style>
        body { font-family: sans-serif; background: #f4f4f4; padding: 20px; text-align: center; }
        .cart-container { background: white; padding: 20px; max-width: 600px; margin: auto; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { padding: 10px; border-bottom: 1px solid #ddd; text-align: left; }
        .btn-order { background: #27ae60; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; }
    </style>
</head>
<body>

    <?php include 'nav.php'; ?>

    <div class="cart-container">
        <?php if ($order_placed): ?>
            <div style="color: green;">
                <h2>🎉 Success!</h2>
                <p>Your order has been sent to the kitchen.</p>
                <a href="index.php">Back to Menu</a>
            </div>
        <?php elseif (!empty($_SESSION['cart'])): ?>
            <h2>🛒 Your Cart</h2>
            <table>
                <tr><th>Item</th><th>Price</th><th>Qty</th><th>Total</th></tr>
                <?php
                $total = 0.0;
                foreach ($_SESSION['cart'] as $item):
                    if (is_array($item)):
                        $sub    = (float)$item['price'] * (int)$item['quantity'];
                        $total += $sub;
                ?>
                <tr>
                    <!-- FIX (Medium): Escape output to prevent stored XSS -->
                    <td><?php echo htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td>$<?php echo number_format((float)$item['price'], 2); ?></td>
                    <td><?php echo (int)$item['quantity']; ?></td>
                    <td>$<?php echo number_format($sub, 2); ?></td>
                </tr>
                <?php endif; endforeach; ?>
            </table>
            <h3>Grand Total: $<?php echo number_format($total, 2); ?></h3>
            <form method="POST">
                <!-- FIX (Critical): CSRF token on every state-changing form -->
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                <button type="submit" name="place_order" class="btn-order">Place Order</button>
            </form>
        <?php else: ?>
            <h2>Your cart is empty.</h2>
            <a href="index.php">Go to Menu</a>
        <?php endif; ?>
    </div>

</body>
</html>
