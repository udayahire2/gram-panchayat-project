<?php
session_start();
require_once 'db_config.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $_SESSION['email'] = $_POST['email'];
    $_SESSION['otp'] = rand(100000, 999999); // Generate a random OTP

    // Send OTP via Email
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; 
        $mail->SMTPAuth = true;
        $mail->Username = 'your-email@gmail.com'; 
        $mail->Password = 'your-email-password'; 
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('your-email@gmail.com', 'Village Portal');
        $mail->addAddress($_SESSION['email']);
        $mail->Subject = 'Your OTP for Signup';
        $mail->Body = "Your OTP for email verification is: " . $_SESSION['otp'];

        $mail->send();
        
        // Redirect to OTP verification page
        header("Location: verify_email.php");
        exit();
    } catch (Exception $e) {
        $error = "OTP email failed: " . $mail->ErrorInfo;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Email Verification</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Email Verification</h2>
        <?php if(isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
        <form method="POST">
            <div class="mb-3">
                <label>Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Send OTP</button>
        </form>
    </div>
</body>
</html>
