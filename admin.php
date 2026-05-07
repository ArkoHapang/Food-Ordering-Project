<?php
session_start();
include 'db_connect.php';
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') { header("Location: user_login.php"); exit; }

// Handle Status Update
if (isset($_POST['update_status'])) {
    $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $_POST['status'], $_POST['order_id']);
    $stmt->execute();
}

$orders = $conn->query("SELECT orders.*, users.name FROM orders JOIN users ON orders.user_id = users.id ORDER BY id DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Kitchen Admin</title>
    <style>
        body { font-family: sans-serif; background: #f0f2f5; margin: 0; padding: 20px; }
        .nav { background: #34495e; color: white; padding: 15px 30px; border-radius: 10px; display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .nav a { color: white; text-decoration: none; margin-left: 20px; }
        table { width: 100%; background: white; border-radius: 10px; overflow: hidden; border-collapse: collapse; }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #eee; }
        th { background: #2c3e50; color: white; }
    </style>
</head>
<body>

<div class="nav">
    <h2>🛠️ Admin Panel</h2>
    <div>
        <span>👋 Welcome, <strong><?php echo $_SESSION['user_name']; ?></strong></span>
        <a href="manage_menu.php" style="background:#2ecc71; padding:8px 12px; border-radius:5px;">Manage Menu</a>
        <a href="logout.php" style="background:#e74c3c; padding:8px 12px; border-radius:5px;">Logout</a>
    </div>
</div>

<table>
    <tr>
        <th>ID</th>
        <th>Customer</th>
        <th>Details</th>
        <th>Status</th>
        <th>Action</th>
    </tr>
    <?php while($row = $orders->fetch_assoc()): ?>
    <tr>
        <td>#<?php echo $row['id']; ?></td>
        <td><?php echo htmlspecialchars($row['name']); ?></td>
        <td><?php echo htmlspecialchars($row['food_details']); ?></td>
        <td><strong><?php echo $row['status']; ?></strong></td>
        <td>
            <form method="POST">
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
</table>

</body>
</html>