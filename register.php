<?php
session_start();
include 'db_connect.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password']; // Keeping it plaintext to match your user_login.php
    $role = 'customer'; // Default role for new signups

    // 1. Check if the email is already registered
    $check_sql = "SELECT id FROM users WHERE email = ?";
    if ($check_stmt = $conn->prepare($check_sql)) {
        $check_stmt->bind_param("s", $email);
        $check_stmt->execute();
        $check_stmt->store_result();

        if ($check_stmt->num_rows > 0) {
            $message = "<div class='error-msg'>This email is already registered. Please login.</div>";
        } else {
            // 2. If email is unique, insert the new user
            $insert_sql = "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)";
            if ($stmt = $conn->prepare($insert_sql)) {
                $stmt->bind_param("ssss", $name, $email, $password, $role);

                if ($stmt->execute()) {
                    // Success! Send them to the login page
                    header("Location: user_login.php");
                    exit; // ALWAYS use exit after a header redirect
                } else {
                    $message = "<div class='error-msg'>Something went wrong: " . $conn->error . "</div>";
                }
                
                // Safe closure of statement
                $stmt->close();
            }
        }
        // Safe closure of check statement
        $check_stmt->close();
    } else {
        $message = "<div class='error-msg'>Database error: Could not prepare statement.</div>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sign Up | Foodies</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f7f6; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .register-container { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); width: 100%; max-width: 400px; text-align: center; }
        h2 { color: #2c3e50; margin-bottom: 20px; }
        input[type="text"], input[type="email"], input[type="password"] { width: 90%; padding: 12px; margin: 10px 0; border: 1px solid #ccc; border-radius: 5px; font-size: 16px; }
        .btn-submit { background: #27ae60; color: white; border: none; padding: 12px; width: 100%; border-radius: 5px; font-size: 16px; cursor: pointer; font-weight: bold; margin-top: 10px; }
        .btn-submit:hover { background: #219150; }
        .error-msg { color: #e74c3c; background: #fdeaea; padding: 10px; border-radius: 5px; margin-bottom: 15px; border: 1px solid #fad2d2; }
        .login-link { margin-top: 15px; display: block; color: #7f8c8d; text-decoration: none; }
        .login-link:hover { color: #27ae60; text-decoration: underline; }
    </style>
</head>
<body>

    <div class="register-container">
        <h2>🍔 Join Foodies!</h2>
        
        <?php echo $message; ?>

        <form method="POST" action="">
            <input type="text" name="name" placeholder="Full Name" required>
            <input type="email" name="email" placeholder="Email Address" required>
            <input type="password" name="password" placeholder="Create Password" required>
            <button type="submit" class="btn-submit">Sign Up</button>
        </form>

        <a href="user_login.php" class="login-link">Already have an account? Login here.</a>
    </div>

</body>
</html>