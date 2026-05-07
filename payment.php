<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id']) || empty($_SESSION['cart'])) {
    header("Location: index.php"); exit;
}

$total = 0; $details = "";
foreach($_SESSION['cart'] as $item) {
    $total += $item['price'];
    $details .= $item['name'] . ", ";
}
$details = rtrim($details, ", ");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $sql = "INSERT INTO orders (user_id, food_details, total_amount, payment_method, status) VALUES (?, ?, ?, ?, 'Pending')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isds", $_SESSION['user_id'], $details, $total, $_POST['payment_method']);
    if ($stmt->execute()) {
        unset($_SESSION['cart']);
        echo "<script>alert('Order Successful!'); window.location.href='index.php';</script>";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Secure Payment</title>
    <style>
        body { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
            height: 100vh; display: flex; align-items: center; justify-content: center; font-family: sans-serif; margin: 0;
        }
        .pay-card { 
            background: white; padding: 40px; border-radius: 25px; 
            box-shadow: 0 20px 50px rgba(0,0,0,0.2); width: 350px; text-align: center;
        }
        .total-box { font-size: 40px; font-weight: bold; color: #2ecc71; margin: 20px 0; }
        select { width: 100%; padding: 12px; border-radius: 10px; border: 1px solid #ddd; margin-bottom: 25px; }
        .confirm-btn { 
            background: #2ecc71; color: white; border: none; padding: 15px; 
            width: 100%; border-radius: 12px; font-weight: bold; cursor: pointer; transition: 0.3s;
        }
        .confirm-btn:hover { background: #27ae60; transform: translateY(-3px); }
    </style>
</head>
<body>
    <div class="pay-card">
        <h2 style="color:#2c3e50;">Checkout</h2>
        <p style="color:#7f8c8d;">Final Amount to Pay:</p>
        <div class="total-box">$<?php echo number_format($total, 2); ?></div>
        <form method="POST">
            <select name="payment_method" required>
                <option value="Cash on Delivery">🚚 Cash on Delivery</option>
                <option value="Online">💳 Credit / Debit Card / Bkash </option>
            </select>
            <button type="submit" class="confirm-btn">CONFIRM ORDER</button>
        </form>
    </div>
</body>
</html>