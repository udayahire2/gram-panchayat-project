<?php
require_once 'db_config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $mobile = $_POST['mobile'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);//hash password
    $address = $_POST['address'];

    $stmt = $conn->prepare("INSERT INTO villager (name, email, mobile, password, address) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $name, $email, $mobile, $password, $address);

    if ($stmt->execute()) {
        // Redirect to login page with success message
        header("Location: login.php?signup=success");
        exit();
    } else {
        $error = "Registration failed. Please try again.";
    }
    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Villager Signup</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Villager Signup</h2>
        <?php if(isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
        <form method="POST">
            <div class="mb-3">
                <label>Full Name</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Mobile Number</label>
                <input type="tel" name="mobile" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Address</label>
                <textarea name="address" class="form-control" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Signup</button>
        </form>
    </div>
</body>
</html>