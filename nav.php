<style>
    .navbar { background-color: #2c3e50; padding: 15px 20px; display: flex; justify-content: space-between; align-items: center; color: white; margin-bottom: 20px; border-radius: 5px; }
    .nav-brand { font-size: 1.5em; font-weight: bold; text-decoration: none; color: white; }
    .nav-links { display: flex; gap: 15px; align-items: center; }
    .nav-links a { color: white; text-decoration: none; padding: 8px 12px; border-radius: 4px; transition: background 0.2s; }
    .nav-links a:hover { background-color: #34495e; }
    .cart-badge { background: #e74c3c; padding: 2px 8px; border-radius: 10px; font-size: 0.9em; font-weight: bold; }
    .logout-btn { background: #c0392b; color: white !important; }
    .logout-btn:hover { background: #e74c3c !important; }
</style>

<div class="navbar">
    <a href="index.php" class="nav-brand">🍔 Foodies</a>

    <div class="nav-links">
        <a href="index.php">Home / Menu</a>

        <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
            <a href="admin.php">⚙️ Dashboard</a>
            <a href="manage_menu.php">📝 Manage Menu</a>
            <!-- FIX (Low): Added null-coalescing on both greeting spans for safety -->
            <span style="color: #f1c40f; margin-left: 10px; font-weight: bold;">
                Hello, <?php echo htmlspecialchars($_SESSION['username'] ?? 'Admin', ENT_QUOTES, 'UTF-8'); ?>!
            </span>
            <a href="logout.php" class="logout-btn">Logout</a>

        <?php elseif (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'user'): ?>
            <a href="my_orders.php">📦 My Orders</a>
            <a href="cart.php">🛒 Cart
                <?php
                if (!empty($_SESSION['cart'])) {
                    echo "<span class='cart-badge'>" . count($_SESSION['cart']) . "</span>";
                }
                ?>
            </a>
            <!-- FIX (Low): Null-coalescing fallback prevents PHP notice if key missing -->
            <span style="color: #2ecc71; margin-left: 10px; font-weight: bold;">
                Hello, <?php echo htmlspecialchars($_SESSION['username'] ?? 'Guest', ENT_QUOTES, 'UTF-8'); ?>!
            </span>
            <a href="logout.php" class="logout-btn">Logout</a>

        <?php else: ?>
            <a href="user_login.php">Login</a>
            <a href="register.php" style="background: #27ae60;">Sign Up</a>
        <?php endif; ?>
    </div>
</div>
