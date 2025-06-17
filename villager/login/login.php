<?php
session_start();
require_once 'db_config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $return_to = $_GET['return_to'] ?? '../Main/index.html';

    $stmt = $conn->prepare("SELECT id, name, email, password FROM villager WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            
            // Redirect to home page with a script to set login state
            echo "<script>
                sessionStorage.setItem('isLoggedIn', 'true');
                window.location.href = '../Main/index.html';
            </script>";
            exit();
        } else {
            $error = "Invalid password";
        }
    } else {
        $error = "User not found";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Villager Login | Kusumba Grampanchayat</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #1a5276; /* Deeper blue */
            --secondary-color: #2ecc71; /* Green accent */
            --accent-color: #e67e22; /* Orange accent */
            --light-color: #f5f7fa; /* Lighter background */
            --dark-color: #2c3e50; /* Darker text */
            --text-color: #34495e; /* Softer text color */
            --border-color: #d5dbe0; /* Softer border */
            --shadow: 0 4px 20px rgba(0, 0, 0, 0.08); /* Softer shadow */
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: linear-gradient(135deg, var(--light-color), #d5e8f7);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .header {
            background: linear-gradient(90deg, var(--primary-color), #2980b9);
            padding: 1rem 0;
            box-shadow: var(--shadow);
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1.5rem;
        }

        .logo-container {
            display: flex;
            align-items: center;
            gap: 1rem;
            text-decoration: none;
            color: white;
            transition: var(--transition);
        }

        .logo-container:hover {
            transform: scale(1.02);
        }

        .logo-img {
            height: 55px;
            border-radius: 50%;
            border: 2px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        }

        .logo-text {
            font-size: 1.3rem;
            font-weight: 600;
            margin: 0;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);
        }

        .back-btn {
            color: white;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: 30px;
            background-color: rgba(255, 255, 255, 0.1);
            transition: var(--transition);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .back-btn:hover {
            color: white;
            background-color: rgba(255, 255, 255, 0.2);
            transform: translateX(-3px);
        }

        .login-container {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 2rem;
        }

        .login-card {
            background: white;
            border-radius: 15px;
            box-shadow: var(--shadow);
            width: 100%;
            max-width: 420px;
            padding: 2.5rem;
            transition: var(--transition);
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .login-card:hover {
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
            transform: translateY(-5px);
        }

        .login-header {
            text-align: center;
            margin-bottom: 2.5rem;
        }

        .login-header h2 {
            color: var(--primary-color);
            font-weight: 600;
            margin-bottom: 8px;
        }

        .login-header p {
            color: #7f8c8d;
        }

        .form-group {
            margin-bottom: 1.75rem;
        }

        .form-label {
            color: var(--text-color);
            font-weight: 500;
            margin-bottom: 0.6rem;
            display: block;
        }

        .form-control {
            border: 2px solid var(--border-color);
            border-radius: 8px;
            padding: 0.85rem 1rem;
            transition: var(--transition);
            font-size: 1rem;
            background-color: #f8fafc;
        }

        .form-control:focus {
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 0.2rem rgba(46, 204, 113, 0.25);
            background-color: white;
        }

        .login-btn {
            background: linear-gradient(to right, var(--secondary-color), #27ae60);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 0.85rem;
            width: 100%;
            font-weight: 500;
            transition: var(--transition);
            letter-spacing: 0.5px;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 0.5rem;
        }

        .login-btn:hover {
            background: linear-gradient(to right, #27ae60, var(--secondary-color));
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(46, 204, 113, 0.3);
        }

        .login-btn:active {
            transform: translateY(0);
        }

        .signup-link {
            display: block;
            text-align: center;
            margin-top: 1.5rem;
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            transition: var(--transition);
        }

        .signup-link:hover {
            color: var(--secondary-color);
        }

        .alert {
            border-radius: 8px;
            margin-bottom: 1.5rem;
            padding: 1rem;
        }

        .alert-danger {
            background-color: #fee2e2;
            border-left: 4px solid #ef4444;
            color: #b91c1c;
        }

        @media (max-width: 768px) {
            .login-card {
                margin: 1rem;
                padding: 2rem;
            }

            .header-content {
                padding: 0 1rem;
            }

            .logo-text {
                font-size: 1.1rem;
            }
        }
    </style>
</head>

<body>
    <header class="header">
        <div class="header-content">
            <a href="../Main/index.html" class="logo-container">
                <img src="../image/logo.png" alt="Gram Panchayat Logo" class="logo-img">
                <h1 class="logo-text">Kusumba Grampanchayat</h1>
            </a>
            <a href="../Main/index.html" class="back-btn">
                <i class="bi bi-arrow-left"></i>
                Back to Home
            </a>
        </div>
    </header>

    <main class="login-container">
        <div class="login-card">
            <div class="login-header">
                <h2>Villager Login</h2>
            </div>
            
            <?php if(isset($error)): ?>
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <?php echo $error; ?>
            </div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" name="email" id="email" class="form-control" 
                           placeholder="Enter your email" required>
                </div>
                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group">
                        <input type="password" name="password" id="password" class="form-control" 
                               placeholder="Enter your password" required>
                      
                    </div>
                </div>
                <button type="submit" class="login-btn">
                    <i class="bi bi-box-arrow-in-right"></i>
                    Sign In
                </button>
                <a href="signup.php" class="signup-link">
                    <i class="bi bi-person-plus"></i> Create a new account
                </a>
            </form>
        </div>
    </main>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.querySelector('.password-toggle i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('bi-eye');
                toggleIcon.classList.add('bi-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('bi-eye-slash');
                toggleIcon.classList.add('bi-eye');
            }
        }
    </script>
</body>
</html>