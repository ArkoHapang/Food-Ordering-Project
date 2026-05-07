<?php
session_start();
include 'db_connect.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Strictly ensure only users with the 'admin' role can log in here
    $sql = "SELECT id, name, password, role FROM users WHERE email = ? AND role = 'admin'";
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            
            // Password check
            if ($password === $row['password']) {
                // Set admin-specific sessions
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['user_name'] = $row['name'];
                $_SESSION['user_role'] = $row['role'];
                
                // Redirect to your admin dashboard (change 'admin.php' if your file is named differently)
                header("Location: admin.php"); 
                exit;
            } else {
                $error = "<div class='error-msg'>Invalid email or password.</div>";
            }
        } else {
            $error = "<div class='error-msg'>Invalid email or password.</div>";
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
    <title>Admin Login | Foodies</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            background: #34495e; /* Dark blue/slate background from your screenshot */
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
            box-shadow: 0 4px 15px rgba(0,0,0,0.2); 
            width: 100%; 
            max-width: 400px; 
            text-align: center; 
        }
        h2 { 
            color: #e74c3c; /* Red text for Staff & Admin Login */
            margin-bottom: 25px; 
            font-size: 24px;
        }
        input[type="email"], input[type="password"] { 
            width: 100%; 
            padding: 12px; 
            margin: 10px 0; 
            border: 1px solid #ccc; 
            border-radius: 5px; 
            font-size: 14px; 
            box-sizing: border-box;
        }
        .btn-submit { 
            background: #e74c3c; /* Red button */
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
            background: #c0392b; 
        }
        .error-msg { 
            color: #e74c3c; 
            font-weight: bold;
            margin-bottom: 15px; 
            font-size: 14px;
        }
        .back-link { 
            margin-top: 20px; 
            display: block; 
            color: #7f8c8d; 
            text-decoration: underline; 
            font-size: 13px;
        }
        .back-link:hover { 
            color: #34495e; 
        }
    </style>
</head>
<body>

    <div class="login-container">
        <h2>⚙️ Staff & Admin Login</h2>
        
        <?php echo $error; ?>

        <form method="POST" action="">
            <input type="email" name="email" placeholder="Admin Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" class="btn-submit">Access Dashboard</button>
        </form>

        <a href="user_login.php" class="back-link">&larr; Back to Customer Login</a>
    </div>

</body>
</html>