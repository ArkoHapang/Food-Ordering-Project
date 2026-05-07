<?php
session_start();
include 'db_connect.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        // Verify the encrypted password
        if (password_verify($password, $user['password'])) {
            // Save user details in the session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $user['role'];

            // Send them to the right place based on their role
            if ($user['role'] == 'admin') {
                header("Location: admin.php");
            } else {
                header("Location: index.php");
            }
            exit;
        } else {
            $message = "Incorrect password.";
        }
    } else {
        $message = "No account found with that email.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login | Food Ordering</title>
    <style>
        body { font-family: Arial; background: #f4f4f4; padding: 50px; text-align: center; }
        .form-box { background: white; padding: 20px; max-width: 300px; margin: 0 auto; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        input { width: 90%; padding: 10px; margin: 10px 0; border: 1px solid #ddd; border-radius: 4px; }
        .btn { background: #3498db; color: white; padding: 10px; border: none; width: 100%; cursor: pointer; border-radius: 4px; }
    </style>
</head>
<body>
    <div class="form-box">
        <h2>Login</h2>
        <p style="color:red;"><?php echo $message; ?></p>
        <form method="POST" action="">
            <input type="email" name="email" placeholder="Email Address" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" class="btn">Log In</button>
        </form>
        <p>Don't have an account? <a href="register.php">Register</a></p>
    </div>
</body>
</html>