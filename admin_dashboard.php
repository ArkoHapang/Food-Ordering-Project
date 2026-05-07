<?php
session_start();
include 'db_connect.php';

// Protect the page - Admin only
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    die("Access Denied: Admins Only.");
}

// Handle Status Updates
if (isset($_POST['update_status'])) {
    $oid = $_POST['order_id'];
    $new_status = $_POST['status'];
    $u_sql = "UPDATE orders SET status = ? WHERE id = ?";
    $u_stmt = $conn->prepare($u_sql);
    $u_stmt->bind_param("si", $new_status, $oid);
    $u_stmt->execute();
}

// Fetch Orders joining with Users to get the Customer Name
$sql = "SELECT orders.*, users.name FROM orders JOIN users ON orders.user_id = users.id ORDER BY order_date DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Kitchen Admin</title>
    <style>
        body { font-family: sans-serif; background: #2c3e50; color: white; padding: 20px; }
        .container { background: #ecf0f1; color: #2c3e50; padding: 20px; border-radius: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #bdc3c7; }
        th { background: #34495e; color: white; }
        .badge { padding: 5px 10px; border-radius: 4px; font-size: 12px; font-weight: bold; }
        .Pending { background: #f1c40f; }
        .Ready { background: #2ecc71; color: white; }
        .Cancelled { background: #e74c3c; color: white; }
    </style>
</head>
<body>
    <div class="container">
        <h1>👨‍🍳 Kitchen Control Panel</h1>
        <table>
            <thead>
                <tr>
                    <th>Order #</th>
                    <th>Customer</th>
                    <th>Food Items</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Update Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td>#<?php echo $row['id']; ?></td>
                    <td><?php echo $row['name']; ?></td>
                    <td><?php echo $row['food_details']; ?></td>
                    <td>$<?php echo $row['total_amount']; ?></td>
                    <td><span class="badge <?php echo $row['status']; ?>"><?php echo $row['status']; ?></span></td>
                    <td>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="order_id" value="<?php echo $row['id']; ?>">
                            <select name="status">
                                <option value="Pending">Pending</option>
                                <option value="Ready">Ready</option>
                                <option value="Cancelled">Cancelled</option>
                            </select>
                            <button type="submit" name="update_status">Update</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>