<?php
include('db.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    if (!preg_match('/[a-zA-Z]/', $username)) {
        $error = "Username must contain at least one letter.";
    } 
    elseif (!preg_match('/^(?=.*[a-zA-Z])(?=.*\d)(?=.*[\W_]).+$/', $password)) {
        $error = "Password must contain at least one letter, one number, and one special character.";
    } else {
        $password = password_hash($password, PASSWORD_DEFAULT);
        $check_sql = "SELECT username FROM users WHERE username = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("s", $username);
        $check_stmt->execute();
        $check_stmt->store_result();

        if ($check_stmt->num_rows > 0) {
            $error = "Username already exists. Please choose a different username.";
        } else {
            $sql = "INSERT INTO users (username, password, role) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sss", $username, $password, $role);

            if ($stmt->execute()) {
                header("Location: login.php");
                exit();
            } else {
                $error = "Registration error: " . $stmt->error;
            }

            $stmt->close();
        }

        $check_stmt->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>A&B Store - Create Account</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: var(--background);
            color: var(--text-dark);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M30,15 C35,5 45,0 50,10 C55,20 45,30 30,30 C15,30 5,20 10,10 C15,0 25,5 30,15Z' fill='%237F8C8D15'/%3E%3C/svg%3E");
            background-size: 120px;
        }
        
        /* Pattern elements */
        body::before {
            content: "";
            position: fixed;
            width: 300px;
            height: 300px;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 200 200'%3E%3Cpath fill='%2334495E20' d='M50,10 C70,10 100,40 100,100 S70,190 50,190 C30,190 0,160 0,100 S30,10 50,10z'/%3E%3C/svg%3E");
            background-size: contain;
            top: -50px;
            right: -100px;
            transform: rotate(15deg);
            z-index: -1;
        }
        
        body::after {
            content: "";
            position: fixed;
            width: 200px;
            height: 200px;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 200 200'%3E%3Cpath fill='%2334495E20' d='M50,10 C70,10 100,40 100,100 S70,190 50,190 C30,190 0,160 0,100 S30,10 50,10z'/%3E%3C/svg%3E");
            background-size: contain;
            bottom: -50px;
            left: -50px;
            transform: rotate(195deg) scale(0.7);
            z-index: -1;
        }
        
        .register-container {
            background-color: white;
            width: 90%;
            max-width: 480px;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
            position: relative;
            z-index: 1;
        }
        
        .header {
            background: linear-gradient(135deg, var(--primary-medium), var(--primary-dark));
            color: var(--text-light);
            padding: 30px 25px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .header h2 {
            font-size: 28px;
            font-weight: 600;
            margin: 0 0 10px;
            position: relative;
            z-index: 1;
        }
        
        .tagline {
            font-size: 16px;
            opacity: 0.9;
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
        
        /* Pattern in header */
        .header::after {
            content: "";
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            background-image: url("data:image/svg+xml,%3Csvg width='40' height='40' viewBox='0 0 40 40' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M20,10 C25,0 35,5 30,15 C25,25 15,20 20,10z' fill='white' fill-opacity='0.1'/%3E%3C/svg%3E");
            background-size: 60px;
            opacity: 0.2;
        }
        
        .register-form {
            padding: 35px 30px;
        }
        
        .form-group {
            margin-bottom: 25px;
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
            transition: all 0.3s ease;
            background-color: #FAFAFA;
        }
        
        .form-control:focus {
            border-color: var(--primary-medium);
            outline: none;
            box-shadow: 0 0 0 3px rgba(52, 73, 94, 0.2);
        }
        
        .password-requirements {
            font-size: 12px;
            color: #666;
            margin-top: 8px;
            line-height: 1.5;
        }
        
        .form-select {
            width: 100%;
            padding: 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s ease;
            appearance: none;
            background-color: #FAFAFA;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='%232C3E50' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 15px center;
            background-size: 16px;
        }
        
        .form-select:focus {
            border-color: var(--primary-medium);
            outline: none;
            box-shadow: 0 0 0 3px rgba(52, 73, 94, 0.2);
        }
        
        .register-btn {
            display: block;
            width: 100%;
            padding: 15px;
            background: var(--primary-dark);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        
        .register-btn:hover {
            background: var(--primary-medium);
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }
        
        .login-link {
            text-align: center;
            margin-top: 25px;
            font-size: 15px;
            color: #666;
        }
        
        .login-link a {
            color: var(--primary-medium);
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s;
        }
        
        .login-link a:hover {
            color: var(--primary-dark);
            text-decoration: underline;
        }
        
        .error-message {
            background-color: #ffebee;
            color: var(--error);
            padding: 12px 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 14px;
            border-left: 4px solid var(--error);
        }
        
        /* Responsive adjustments */
        @media (max-width: 480px) {
            .register-container {
                width: 95%;
            }
            
            .header {
                padding: 25px 20px;
            }
            
            .register-form {
                padding: 25px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="header">
            <div class="logo">
                <div class="logo-circle">A&B</div>
            </div>
            <h2>Join A&B Store</h2>
            <div class="tagline">Create your account to start shopping</div>
        </div>
        
        <div class="register-form">
            <?php if(isset($error)): ?>
                <div class="error-message">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <form method="post">
                <div class="form-group">
                    <label for="username">Choose a Username</label>
                    <input type="text" id="username" name="username" class="form-control" placeholder="Enter username" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Create a Password</label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="Enter password" required>
                    <div class="password-requirements">
                        Password must include at least one letter, one number, and one special character.
                    </div>
                </div>
                
               <div class="form-group">
                    <label for="role">Account Type</label>
                    <select id="role" name="role" class="form-select" required>
                        <option value="user">Customer</option>
                        <!-- <option value="admin">Admin</option> -->
                    </select>
                </div> 
                
                <button type="submit" class="register-btn">Create My Account</button>
                
                <div class="login-link">
                    Already a member? <a href="login.php">Sign in here</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>