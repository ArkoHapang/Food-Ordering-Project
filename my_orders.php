<?php
session_start();
include 'db_connect.php';
if (!isset($_SESSION['user_id'])) { header("Location: user_login.php"); exit; }

$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM orders WHERE user_id = ? ORDER BY order_date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html>
<head>
    <title>My Orders | Foodies</title>
    <style>
        body { font-family: sans-serif; background: #f4f7f6; padding: 40px; }
        .order-container { max-width: 700px; margin: auto; }
        .order-card { 
            background: white; padding: 20px; border-radius: 12px; margin-bottom: 20px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05); display: flex; justify-content: space-between;
            border-left: 6px solid #3498db;
        }
        .status-pill { padding: 5px 15px; border-radius: 20px; font-weight: bold; font-size: 0.8em; }
        .Pending { background: #ffeaa7; color: #d6a01e; }
        .Ready { background: #55efc4; color: #00b894; }
        .Cancelled { background: #ff7675; color: #d63031; }
    </style>
</head>
<body>
    <div class="order-container">
        <h1>📋 My Order History</h1>
        <a href="index.php" style="text-decoration:none; color:#3498db;">&larr; Back to Menu</a><br><br>

        <?php if($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <div class="order-card">
                    <div>
                        <h3 style="margin:0;">Order #<?php echo $row['id']; ?></h3>
                        <p style="color:#7f8c8d;"><?php echo $row['food_details']; ?></p>
                        <small>Date: <?php echo $row['order_date']; ?></small>
                    </div>
                    <div style="text-align:right;">
                        <div class="status-pill <?php echo $row['status']; ?>"><?php echo $row['status']; ?></div>
                        <p style="font-weight:bold; margin-top:10px;">$<?php echo $row['total_amount']; ?></p>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>You haven't placed any orders yet!</p>
        <?php endif; ?>
    </div>
</body>
</html>