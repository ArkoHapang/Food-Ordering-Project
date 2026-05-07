<?php
session_start();
include 'db_connect.php';

// 1. Security Check: Only admins can delete
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    die("Unauthorized access.");
}

// 2. CSRF Validation
if (!isset($_GET['token']) || $_GET['token'] !== $_SESSION['csrf_token']) {
    die("Invalid security token. Please try again from the dashboard.");
}

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    // 3. Prepared Statement to prevent SQL Injection
    $stmt = $conn->prepare("DELETE FROM orders WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        header("Location: admin.php?msg=deleted");
    } else {
        echo "Error deleting record: " . $conn->error;
    }
    $stmt->close();
}
exit;