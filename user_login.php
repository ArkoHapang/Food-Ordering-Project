<?php
session_start();
include 'db_connect.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Use trim() to kill invisible spaces from autofill/copy-pasting
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // 2. Use Prepared Statements to securely fetch the user
    $sql = "SELECT id, name, password, role FROM users WHERE email = ?";
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            
            // 3. Exact password match check
            if ($password === $row['password']) {
                // Success! Set session variables
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['user_name'] = $row['name'];
                $_SESSION['user_role'] = $row['role'];

                // Redirect based on role (sends admins to admin panel, customers to index)
                if ($row['role'] == 'admin') {
                    header("Location: admin_dashboard.php"); // Adjust if your admin page has a different name
                } else {
                    header("Location: index.php");
                }
                exit;
            } else {
                $error = "<div class='error-msg'>INVALID PASS: Passwords do not match.</div>";
            }
        } else {
            $error = "<div class='error-msg'>No account found with this email.</div>";
        }
        $stmt->close();
    } else {
        $error = "<div class='error-msg'>Database Error: " . $conn->error . "</div>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login | Foodies</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            background: #f4f7f6; 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            height: 100vh; 
            margin: 0; 
        }
        .login-container { 
            background: white; 
            padding: 40px; 
            border-radius: 8px; 
            box-shadow: 0 4px 15px rgba(0,0,0,0.05); 
            width: 100%; 
            max-width: 400px; 
            text-align: center; 
        }
        h2 { 
            color: #2c3e50; 
            margin-bottom: 25px; 
            font-size: 24px;
        }
        input[type="email"], input[type="password"] { 
            width: 100%; 
            padding: 12px; 
            margin: 10px 0; 
            border: 1px solid #ccc; 
            border-radius: 5px; 
            font-size: 15px; 
            box-sizing: border-box;
        }
        .btn-submit { 
            background: #3498db; 
            color: white; 
            border: none; 
            padding: 12px; 
            width: 100%; 
            border-radius: 5px; 
            font-size: 16px; 
            cursor: pointer; 
            font-weight: bold; 
            margin-top: 15px; 
        }
        .btn-submit:hover { 
            background: #2980b9; 
        }
        .error-msg { 
            color: #e74c3c; 
            background: #fdeaea; 
            padding: 10px; 
            border-radius: 5px; 
            margin-bottom: 15px; 
            border: 1px solid #fad2d2; 
            font-size: 14px;
        }
        .register-link { 
            margin-top: 20px; 
            display: block; 
            color: #7f8c8d; 
            text-decoration: none; 
            font-size: 14px;
        }
        .register-link:hover { 
            color: #3498db; 
            text-decoration: underline; 
        }
        .admin-link {
            display: inline-block;
            margin-top: 30px;
            color: #bdc3c7;
            text-decoration: none;
            font-size: 12px;
        }
        .admin-link:hover {
            color: #95a5a6;
        }
    </style>
</head>
<body>

    <div class="login-container">
        <h2>👋 Welcome Back!</h2>
        
        <?php echo $error; ?>

        <form method="POST" action="">
            <input type="email" name="email" placeholder="Enter your email" required>
            <input type="password" name="password" placeholder="Enter your password" required>
            <button type="submit" class="btn-submit">Login</button>
        </form>

        <a href="register.php" class="register-link">Don't have an account? Sign up here.</a>
        
        <a href="admin_login.php" class="admin-link">Staff Access</a>
    </div>

</body>
</html>