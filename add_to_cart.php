<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // FIX (High): Cast all inputs to their expected types immediately.
    // item_id and item_price from hidden fields must be integers/floats — never trust raw POST.
    $id    = (int)$_POST['item_id'];
    $price = (float)$_POST['item_price'];
    $qty   = (int)($_POST['quantity'] ?? 1);

    // FIX (High): Reject any invalid values before they touch the cart.
    // A quantity of 0 or negative, or a non-positive id/price, is nonsensical.
    if ($id <= 0 || $qty <= 0 || $price <= 0) {
        header("Location: index.php");
        exit;
    }

    // Sanitize the name for display — it came from a hidden field so strip tags
    // to prevent any stored XSS sneaking in through the cart session.
    $name = htmlspecialchars(strip_tags($_POST['item_name'] ?? ''), ENT_QUOTES, 'UTF-8');

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Check if item is already in cart — update quantity, else add new entry
    if (isset($_SESSION['cart'][$id])) {
        $_SESSION['cart'][$id]['quantity'] += $qty;
    } else {
        $_SESSION['cart'][$id] = [
            'name'     => $name,
            'price'    => $price,
            'quantity' => $qty,
        ];
    }

    header("Location: index.php");
    exit;

} else {
    header("Location: index.php");
    exit;
}
?>
