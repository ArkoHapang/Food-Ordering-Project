<?php
session_start();
include 'db_connect.php';

// 1. Security Check
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin'):
    header("Location: user_login.php");
    exit;
endif;

// 2. CSRF Token Logic
if (empty($_SESSION['csrf_token'])):
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
endif;
$csrf_token = $_SESSION['csrf_token'];

// 3. Handle Actions
// Handle Delete
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['delete_item'])):
    if (hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')):
        $id = (int)$_POST['item_id'];
        $stmt = $conn->prepare("DELETE FROM food_items WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()):
            $stmt->close();
            header("Location: manage_menu.php?msg=deleted");
            exit;
        endif;
    endif;
endif;

// Handle Add Item (File Upload version)
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['add_item'])):
    if (hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')):
        $name = trim($_POST['name']);
        $desc = trim($_POST['description']);
        $price = (float)$_POST['price'];
        
        $image_name = $_FILES['food_image']['name'];
        $target_file = "images/" . basename($image_name);

        if (move_uploaded_file($_FILES['food_image']['tmp_name'], $target_file)):
            $stmt = $conn->prepare("INSERT INTO food_items (name, description, price, image) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssds", $name, $desc, $price, $image_name);
            $stmt->execute();
            $stmt->close();
            header("Location: manage_menu.php?msg=added");
            exit;
        endif;
    endif;
endif;

// 4. Fetch Items
$items = $conn->query("SELECT * FROM food_items ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Menu | Admin</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f0f2f5; margin: 0; padding: 40px; }
        .admin-card { background: white; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); padding: 30px; max-width: 1000px; margin: auto; }
        .header-row { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #eee; padding-bottom: 15px; margin-bottom: 25px; }
        .add-form { background: #fafafa; padding: 20px; border-radius: 10px; margin-bottom: 30px; border: 1px solid #eee; }
        .grid-inputs { display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 15px; margin-bottom: 15px; }
        input, textarea { padding: 12px; border: 1px solid #ddd; border-radius: 8px; width: 100%; box-sizing: border-box; }
        .btn-save { background: #2ecc71; color: white; border: none; padding: 12px 25px; border-radius: 8px; cursor: pointer; font-weight: bold; width: 100%; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { text-align: left; color: #7f8c8d; padding: 12px; border-bottom: 2px solid #eee; }
        td { padding: 15px; border-bottom: 1px solid #f9f9f9; }
        .img-thumb { width: 55px; height: 55px; border-radius: 8px; object-fit: cover; background: #eee; }
        .btn-del { background: #ff7675; color: white; border: none; padding: 8px 15px; border-radius: 5px; cursor: pointer; font-weight: bold; }
        .alert { background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #c3e6cb; }
    </style>
</head>
<body>

<div class="admin-card">
    <div class="header-row">
        <h2 style="margin:0;">🍱 Manage Food Menu</h2>
        <a href="admin.php" style="text-decoration:none; color:#3498db; font-weight:bold;">&larr; Back to Dashboard</a>
    </div>

    <?php if(isset($_GET['msg'])): ?>
        <div class="alert">✅ Action completed successfully!</div>
    <?php endif; ?>

    <div class="add-form">
        <h4 style="margin-top:0;">➕ Add New Dish</h4>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            <div class="grid-inputs">
                <input type="text" name="name" placeholder="Dish Name" required>
                <input type="number" step="0.01" name="price" placeholder="Price ($)" required>
                <input type="file" name="food_image" accept="image/*" required>
            </div>
            <textarea name="description" placeholder="Short description..." rows="2" style="margin-bottom:15px;"></textarea>
            <button type="submit" name="add_item" class="btn-save">Save to Menu</button>
        </form>
    </div>

    <table>
        <thead>
            <tr>
                <th>Preview</th>
                <th>Dish Details</th>
                <th>Price</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($items && $items->num_rows > 0): ?>
                <?php while($row = $items->fetch_assoc()): ?>
                <tr>
                    <td><img src="images/<?php echo htmlspecialchars($row['image']); ?>" class="img-thumb" onerror="this.src='images/default.jpg'"></td>
                    <td>
                        <strong><?php echo htmlspecialchars($row['name']); ?></strong><br>
                        <small style="color:#888;"><?php echo htmlspecialchars($row['description']); ?></small>
                    </td>
                    <td>$<?php echo number_format($row['price'], 2); ?></td>
                    <td>
                        <form method="POST" onsubmit="return confirm('Confirm deletion?');">
                            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                            <input type="hidden" name="item_id" value="<?php echo (int)$row['id']; ?>">
                            <button type="submit" name="delete_item" class="btn-del">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="4" style="text-align:center; padding:20px;">No dishes found. Add your first item!</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>