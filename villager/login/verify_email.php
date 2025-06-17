<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $entered_otp = $_POST['otp'];

    if ($entered_otp == $_SESSION['otp']) {
        $_SESSION['email_verified'] = true;
        header("Location: register.php"); // Redirect to registration form
        exit();
    } else {
        $error = "Invalid OTP. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Verify OTP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Verify OTP</h2>
        <?php if(isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
        <form method="POST">
            <div class="mb-3">
                <label>Enter OTP</label>
                <input type="text" name="otp" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Verify</button>
        </form>
    </div>
</body>
</html>
