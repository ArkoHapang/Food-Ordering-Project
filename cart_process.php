<?php
session_start();

if (isset($_POST['add_to_cart'])) {
    // Check if user is logged in before allowing cart
    if (!isset($_SESSION['user_id'])) {
        echo "<script>alert('Please login first!'); window.location.href='user_login.php';</script>";
        exit;
    }

    $item = [
        'id' => $_POST['food_id'],
        'name' => $_POST['food_name'],
        'price' => $_POST['food_price'],
        'quantity' => 1
    ];

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Add item to cart
    $_SESSION['cart'][] = $item;

    header("Location: payment.php");
    exit();
}
?>