<?php
session_start();
include 'db_connect.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Foodies | Home</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background: #f8f9fa; margin: 0; padding: 20px; }
        .navbar { 
            display: flex; justify-content: space-between; align-items: center; 
            background: #2c3e50; color: white; padding: 15px 40px; 
            border-radius: 15px; margin-bottom: 40px; 
        }
        .nav-links a { color: white; text-decoration: none; margin-left: 20px; font-weight: 400; }
        .logout-btn { background: #e74c3c; padding: 8px 15px; border-radius: 8px; }
        
        /* Landscape Grid */
        .menu-grid { display: flex; flex-wrap: wrap; gap: 30px; justify-content: center; }
        .food-card { 
            background: white; border-radius: 20px; width: 280px; padding: 20px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.05); transition: 0.4s; text-align: center;
        }
        .food-card:hover { transform: translateY(-10px); box-shadow: 0 15px 30px rgba(0,0,0,0.1); }
        .food-card img { width: 100%; height: 180px; object-fit: cover; border-radius: 15px; }
        .btn-add { background: #2ecc71; color: white; border: none; padding: 12px; width: 100%; border-radius: 10px; cursor: pointer; font-weight: 600; margin-top: 10px; }
    </style>
</head>
<body>

<div class="navbar">
    <h2>🍔 Foodies</h2>
    <div class="nav-links">
        <span>👋 Hello, <strong><?php echo $_SESSION['user_name'] ?? 'Guest'; ?></strong></span>
        <?php if(isset($_SESSION['user_id'])): ?>
            <a href="my_orders.php">📋 My Orders</a>
            <a href="logout.php" class="logout-btn">Logout</a>
        <?php else: ?>
            <a href="user_login.php">Login</a>
        <?php endif; ?>
    </div>
</div>

<div class="menu-grid">
    <?php
    $res = $conn->query("SELECT * FROM food_items ORDER BY id DESC");
    while($row = $res->fetch_assoc()) { ?>
        <div class="food-card">
            <img src="images/<?php echo htmlspecialchars($row['image']); ?>" onerror="this.src='images/default.jpg'">
            <h3><?php echo htmlspecialchars($row['name']); ?></h3>
            <p style="color:#7f8c8d; font-size: 0.9em;"><?php echo htmlspecialchars($row['description']); ?></p>
            <div style="color: #27ae60; font-weight: bold; font-size: 1.2em;">$<?php echo number_format($row['price'], 2); ?></div>
            <form method="POST" action="cart_process.php">
                <input type="hidden" name="food_id" value="<?php echo $row['id']; ?>">
                <input type="hidden" name="food_name" value="<?php echo $row['name']; ?>">
                <input type="hidden" name="food_price" value="<?php echo $row['price']; ?>">
                <button type="submit" name="add_to_cart" class="btn-add">Add to Cart</button>
            </form>
        </div>
    <?php } ?>
</div>

</body>
</html>