<?php 
session_start();
require_once 'config.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $error = "Please fill in all fields";
    } else {
        $stmt = mysqli_prepare($conn, "SELECT user_id, username, password, role FROM users WHERE username = ?");
        mysqli_stmt_bind_param($stmt, 's', $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];  
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            
            if ($user['role'] === 'admin') {
                header('Location: admin/dashboard.php');
            } elseif ($user['role'] === 'user') {
                header('Location: user/dashboard.php');
            }
            exit();
        } else {
            $error = "Invalid username or password";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login - WorkPulse</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: #0a0a0a;
            color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .login-container {
            background: #111;
            padding: 40px;
            border-radius: 12px;
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        .logo {
            width: 80px;
            height: 80px;
            margin-bottom: 20px;
        }

        h1 {
            margin-bottom: 10px;
            font-size: 24px;
        }

        .subtitle {
            color: #888;
            margin-bottom: 30px;
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #fff;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 12px;
            background: #1a1a1a;
            border: none;
            border-radius: 6px;
            color: #fff;
            font-size: 14px;
        }

        .forgot-password {
            text-align: right;
            margin-top: -15px;
            margin-bottom: 20px;
        }

        .forgot-password a {
            color: #888;
            text-decoration: none;
            font-size: 14px;
        }

        button {
            width: 100%;
            padding: 12px;
            background: #fff;
            color: #000;
            border: none;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
            margin-bottom: 20px;
        }

        .demo-credentials {
            color: #888;
            font-size: 14px;
            margin-top: 20px;
        }

        .error-message {
            color: #f44336;
            margin-bottom: 20px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <img src="assets/logo.png" alt="Logo" class="logo">
        <h1>WorkPulse</h1>
        <p class="subtitle">Enter your credentials to access your account</p>
        
        <?php if (isset($error)): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" placeholder="Enter your username">
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Enter your password">
            </div>
            <div class="forgot-password">
                <a href="#">Forgot password?</a>
            </div>
            <button type="submit">Sign in</button>
        </form>


    </div>
</body>
</html>