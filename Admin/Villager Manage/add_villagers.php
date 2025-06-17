<?php
session_start();

// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$database = "kusumba_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize error and success messages
$error_message = "";
$success_message = "";

// Form submission handling
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate inputs
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $mobile = trim($_POST['mobile']);
    $address = trim($_POST['address']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validation checks
    if (empty($name) || empty($email) || empty($mobile) || empty($address) || empty($password)) {
        $error_message = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Invalid email format.";
    } elseif (!preg_match("/^[6-9]\d{9}$/", $mobile)) {
        $error_message = "Invalid mobile number. Must be 10 digits starting with 6-9.";
    } elseif (strlen($password) < 8) {
        $error_message = "Password must be at least 8 characters long.";
    } elseif ($password !== $confirm_password) {
        $error_message = "Passwords do not match.";
    } else {
        // Check if email already exists
        $check_email = $conn->prepare("SELECT * FROM villager WHERE email = ?");
        $check_email->bind_param("s", $email);
        $check_email->execute();
        $result = $check_email->get_result();

        if ($result->num_rows > 0) {
            $error_message = "Email already exists. Please use a different email.";
        } else {
            // Hash the password
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);

            // Prepare and execute insert statement
            $stmt = $conn->prepare("INSERT INTO villager (name, email, mobile, password, address) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $name, $email, $mobile, $hashed_password, $address);

            if ($stmt->execute()) {
                $success_message = "Villager added successfully! Redirecting...";
                // Optional: Redirect to manage_villagers.php after 2 seconds
                header("refresh:2;url=view_villagers.php");
            } else {
                $error_message = "Error adding villager: " . $stmt->error;
            }

            $stmt->close();
        }
        $check_email->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Villager - Gram Panchayat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f0f2f5;
            font-family: 'Segoe UI', sans-serif;
        }
        .dashboard-header {
            background: #1a237e;
            color: white;
            padding: 1rem;
        }
        .main-content {
            padding: 2rem 1rem;
        }
        .section-title {
            color: #1a237e;
            border-left: 4px solid #1a237e;
            padding-left: 10px;
            margin: 20px 0;
        }
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            transition: transform 0.2s, box-shadow 0.2s;
            margin-bottom: 24px;
            padding: 20px;
        }
        .form-label {
            font-weight: 600;
            color: #333;
        }
        .password-strength {
            font-size: 0.8em;
            margin-top: 5px;
        }
        .password-weak { color: #dc3545; }
        .password-medium { color: #ffc107; }
        .password-strong { color: #28a745; }
        .logout-btn {
            background-color: #ff5252;
            border: none;
        }
        .logout-btn:hover {
            background-color: #ff1744;
        }
        .btn-primary {
            background-color: #1a237e;
            border-color: #1a237e;
        }
        .btn-primary:hover {
            background-color: #3949ab;
            border-color: #3949ab;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="dashboard-header">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <img src="../Operator/logo.png" alt="Logo" style="height:48px; margin-right:15px; border-radius:50%;">
                    <h1 class="h3 mb-0">Add Villager</h1>
                </div>
                <div>
                    <a href="../Operator/operator_dashboard.php" class="btn btn-outline-light me-2">
                        <i class="bi bi-house-door"></i> Dashboard
                    </a>
                    <a href="../Operator/logout.php" class="btn btn-danger logout-btn">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <!-- Main Content -->
            <main class="col-12 main-content">
                <h2 class="section-title">Add New Villager</h2>
                
                <?php 
                // Display error or success messages
                if (!empty($error_message)) {
                    echo "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
                            $error_message
                            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                          </div>";
                }
                if (!empty($success_message)) {
                    echo "<div class='alert alert-success alert-dismissible fade show' role='alert'>
                            $success_message
                            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                          </div>";
                }
                ?>

                <div class="card">
                    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" id="addVillagerForm">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Full Name</label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       value="<?php echo isset($name) ? htmlspecialchars($name) : ''; ?>" 
                                       required pattern="[A-Za-z\s]+" 
                                       title="Only alphabets and spaces allowed">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" 
                                       required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="mobile" class="form-label">Mobile Number</label>
                                <input type="tel" class="form-control" id="mobile" name="mobile" 
                                       value="<?php echo isset($mobile) ? htmlspecialchars($mobile) : ''; ?>" 
                                       required pattern="[6-9]\d{9}" 
                                       title="10 digit mobile number starting with 6-9">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="address" class="form-label">Address</label>
                                <textarea class="form-control" id="address" name="address" required
                                          rows="1"><?php echo isset($address) ? htmlspecialchars($address) : ''; ?></textarea>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" 
                                       required minlength="8">
                                <div id="passwordStrength" class="password-strength"></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="confirm_password" class="form-label">Confirm Password</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" 
                                       required minlength="8">
                                <div id="passwordMatchStatus" class="password-strength"></div>
                            </div>
                        </div>

                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-primary btn-lg px-4">
                                <i class="bi bi-person-plus-fill me-2"></i>Add Villager
                            </button>
                            <a href="view_villagers.php" class="btn btn-secondary btn-lg ms-2">
                                <i class="bi bi-x-circle me-2"></i>Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const passwordInput = document.getElementById('password');
            const confirmPasswordInput = document.getElementById('confirm_password');
            const passwordStrengthDiv = document.getElementById('passwordStrength');
            const passwordMatchDiv = document.getElementById('passwordMatchStatus');

            function checkPasswordStrength(password) {
                const strengthRegex = {
                    weak: /^.{8,}$/,
                    medium: /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/,
                    strong: /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/
                };

                if (strengthRegex.strong.test(password)) {
                    passwordStrengthDiv.textContent = 'Strong password';
                    passwordStrengthDiv.className = 'password-strength password-strong';
                } else if (strengthRegex.medium.test(password)) {
                    passwordStrengthDiv.textContent = 'Medium strength password';
                    passwordStrengthDiv.className = 'password-strength password-medium';
                } else if (strengthRegex.weak.test(password)) {
                    passwordStrengthDiv.textContent = 'Weak password';
                    passwordStrengthDiv.className = 'password-strength password-weak';
                } else {
                    passwordStrengthDiv.textContent = 'Password must be at least 8 characters';
                    passwordStrengthDiv.className = 'password-strength password-weak';
                }
            }

            function checkPasswordMatch() {
                if (confirmPasswordInput.value === '') {
                    passwordMatchDiv.textContent = '';
                } else if (passwordInput.value === confirmPasswordInput.value) {
                    passwordMatchDiv.textContent = 'Passwords match';
                    passwordMatchDiv.className = 'password-strength password-strong';
                } else {
                    passwordMatchDiv.textContent = 'Passwords do not match';
                    passwordMatchDiv.className = 'password-strength password-weak';
                }
            }

            passwordInput.addEventListener('input', function() {
                checkPasswordStrength(this.value);
                if (confirmPasswordInput.value !== '') {
                    checkPasswordMatch();
                }
            });

            confirmPasswordInput.addEventListener('input', checkPasswordMatch);
        });
    </script>
</body>
</html>