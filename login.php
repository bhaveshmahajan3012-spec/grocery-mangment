<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - FreshMart</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, sans-serif;
            background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-box {
            background: white;
            border-radius: 20px;
            padding: 50px 40px;
            width: 420px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        .login-header { text-align: center; margin-bottom: 35px; }
        .login-header .icon { font-size: 50px; display: block; margin-bottom: 10px; }
        .login-header h2 { font-size: 28px; color: #2c3e50; }
        .login-header p { color: #777; font-size: 14px; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-weight: 600; color: #444; margin-bottom: 8px; font-size: 14px; }
        .input-group { position: relative; }
        .input-group i { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #aaa; }
        .input-group input {
            width: 100%;
            padding: 14px 14px 14px 45px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 15px;
            transition: border-color 0.3s;
            outline: none;
        }
        .input-group input:focus { border-color: #2c3e50; }
        .btn-login {
            width: 100%;
            padding: 15px;
            background: #2c3e50;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
            margin-top: 10px;
        }
        .btn-login:hover { background: #1a252f; }
        .alert { padding: 12px 15px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; }
        .alert-danger { background: #fde8e8; color: #c0392b; border: 1px solid #f5c6cb; }
        .back-link { text-align: center; margin-top: 20px; }
        .back-link a { color: #2c3e50; text-decoration: none; font-size: 14px; }
        .demo-info { background: #eaf4fd; border-radius: 8px; padding: 12px; margin-bottom: 20px; font-size: 13px; color: #555; }
        .demo-info strong { color: #2c3e50; }
    </style>
</head>
<body>
<?php
require_once '../config.php';
if(isset($_SESSION['admin_id'])) { header("Location: dashboard.php"); exit(); }

$error = '';
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $query = "SELECT * FROM admin WHERE email='$email'";
    $result = mysqli_query($conn, $query);

    if($result && mysqli_num_rows($result) == 1) {
        $admin = mysqli_fetch_assoc($result);
        if(password_verify($password, $admin['password'])) {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_name'] = $admin['name'];
            header("Location: dashboard.php");
            exit();
        } else { $error = "Invalid email or password!"; }
    } else { $error = "Invalid email or password!"; }
}
?>
    <div class="login-box">
        <div class="login-header">
            <span class="icon">👨‍💼</span>
            <h2>Admin Login</h2>
            <p>FreshMart Management System</p>
        </div>
        <div class="demo-info">
            <strong>Demo Credentials:</strong><br>
            Email: admin@grocery.com | Password: admin123
        </div>
        <?php if($error): ?>
        <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?= $error ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label>Email Address</label>
                <div class="input-group">
                    <i class="fas fa-envelope"></i>
                    <input type="email" name="email" placeholder="Enter admin email" required value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
                </div>
            </div>
            <div class="form-group">
                <label>Password</label>
                <div class="input-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="password" placeholder="Enter password" required>
                </div>
            </div>
            <button type="submit" class="btn-login"><i class="fas fa-sign-in-alt"></i> Login to Admin Panel</button>
        </form>
        <div class="back-link">
            <a href="../index.php"><i class="fas fa-arrow-left"></i> Back to Home</a>
        </div>
    </div>
</body>
</html>
