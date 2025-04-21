<?php
session_start();
include('db.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT id, username, password, role FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            $_SESSION['login_user'] = $row['username'];
            $_SESSION['role'] = $row['role'];

            if ($row['role'] == 'admin') {
                header("Location: adminpage.php");
            } elseif ($row['role'] == 'user') {
                header("Location: userpage.php");
            }
            exit();
        } else {
            $error = "Incorrect password";
        }
    } else {
        $error = "User not found";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>A&B Store - Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <style>
        /* Color palette */
        :root {
            --primary-dark: #2C3E50;
            --primary-medium: #34495E;
            --primary-light: #7F8C8D;
            --secondary: #E74C3C;
            --background: #ECF0F1;
            --error: #C0392B;
            --text-dark: #1D1D1D;
            --text-light: #FFFFFF;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Verdana, sans-serif;
        }
        
        body {
            background-color: var(--background);
            color: var(--text-dark);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
            background-image: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M50,35 C57,21 68,15 80,15 C94,15 100,25 100,35 C100,45 94,55 80,55 C68,55 57,49 50,35 Z' fill='%237F8C8D20'/%3E%3C/svg%3E");
            background-size: 200px;
        }
        
        /* Background animation */
        body::before {
            content: "";
            position: fixed;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(rgba(44, 62, 80, 0.05), transparent 65%);
            z-index: -1;
            animation: float 15s infinite alternate;
        }
        
        @keyframes float {
            0% {
                transform: translate(0, 0);
            }
            100% {
                transform: translate(-10%, -10%);
            }
        }
        
        .login-container {
            background-color: white;
            width: 90%;
            max-width: 420px;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            position: relative;
            z-index: 1;
        }
        
        .header {
            background: linear-gradient(135deg, var(--primary-medium), var(--primary-dark));
            color: var(--text-light);
            padding: 30px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .header h1 {
            font-size: 28px;
            margin: 0;
            font-weight: 600;
            letter-spacing: 1px;
            position: relative;
            z-index: 1;
        }
        
        /* Pattern effect */
        .header::after {
            content: "";
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='100' height='100' viewBox='0 0 100 100'%3E%3Cpath fill='white' fill-opacity='0.1' d='M30,40 C35,20 45,10 50,10 C55,10 65,20 70,40 C75,60 65,80 50,80 C35,80 25,60 30,40z'/%3E%3C/svg%3E");
            background-size: 80px;
            width: 100%;
            height: 100%;
            position: absolute;
            top: 0;
            left: 0;
            opacity: 0.1;
            animation: patternMove 8s infinite linear;
        }
        
        @keyframes patternMove {
            0% {
                transform: translateY(0) rotate(0deg);
            }
            100% {
                transform: translateY(-20%) rotate(5deg);
            }
        }
        
        .tagline {
            margin-top: 10px;
            font-size: 16px;
            opacity: 0.8;
            font-style: italic;
            position: relative;
            z-index: 1;
        }
        
        /* A&B Logo */
        .logo {
            width: 80px;
            height: 80px;
            margin: 0 auto 15px;
            position: relative;
            z-index: 1;
        }
        
        .logo-circle {
            position: absolute;
            width: 70px;
            height: 70px;
            background: var(--text-light);
            border-radius: 50%;
            top: 5px;
            left: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 28px;
            color: var(--primary-dark);
            border: 2px solid var(--secondary);
        }
        
        .login-form {
            padding: 35px 30px;
        }
        
        .form-group {
            margin-bottom: 25px;
            position: relative;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 10px;
            font-weight: 500;
            color: var(--primary-dark);
            font-size: 15px;
        }
        
        .form-control {
            width: 100%;
            padding: 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s;
            background-color: #FAFAFA;
        }
        
        .form-control:focus {
            border-color: var(--primary-light);
            outline: none;
            box-shadow: 0 0 0 3px rgba(127, 140, 141, 0.2);
        }
        
        .login-btn {
            width: 100%;
            padding: 15px;
            background: var(--primary-dark);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        
        .login-btn:hover {
            background: var(--primary-medium);
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }
        
        .error-message {
            color: var(--error);
            text-align: center;
            margin-top: 15px;
            font-size: 14px;
            font-weight: 500;
        }
        
        .register-link {
            text-align: center;
            margin-top: 25px;
            font-size: 14px;
            color: #777;
        }
        
        .register-link a {
            color: var(--primary-medium);
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s;
        }
        
        .register-link a:hover {
            color: var(--primary-dark);
            text-decoration: underline;
        }
        
        /* Decorative elements */
        .decoration {
            position: absolute;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: -1;
        }
        
        .shape {
            position: absolute;
            background: var(--primary-dark);
            opacity: 0.05;
            border-radius: 50%;
        }
        
        .shape:nth-child(1) {
            top: 10%;
            left: 10%;
            width: 80px;
            height: 80px;
            transform: scale(1.5);
        }
        
        .shape:nth-child(2) {
            top: 20%;
            right: 15%;
            width: 60px;
            height: 60px;
            transform: scale(1.2);
        }
        
        .shape:nth-child(3) {
            bottom: 15%;
            left: 15%;
            width: 70px;
            height: 70px;
            transform: scale(1.3);
        }
        
        .shape:nth-child(4) {
            bottom: 20%;
            right: 10%;
            width: 50px;
            height: 50px;
            transform: scale(1.4);
        }
        
        /* Responsive adjustments */
        @media (max-width: 480px) {
            .login-container {
                width: 95%;
            }
            
            .header {
                padding: 25px 20px;
            }
            
            .login-form {
                padding: 25px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="decoration">
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
    </div>
    
    <div class="login-container">
        <div class="header">
            <div class="logo">
                <div class="logo-circle">A&B</div>
            </div>
            <h1>A&B Store</h1>
            <div class="tagline">Your favourite beverage shop.</div>
        </div>
        
        <div class="login-form">
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" class="form-control" placeholder="Enter your username" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="Enter your password" required>
                </div>
                
                <button type="submit" class="login-btn">Sign In</button>
                
                <?php
                if(isset($error)) {
                    echo "<div class='error-message'>$error</div>";
                }
                ?>
                
                <div class="register-link">
                    Don't have an account? <a href="register.php">Create One!</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>