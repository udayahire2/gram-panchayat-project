<?php
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$database = "login";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die(json_encode(['error' => 'Database connection failed']));
}
// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Not logged in']);
    exit();
}

// Fetch recent notifications for the user
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("
    SELECT title, message, timestamp 
    FROM notifications 
    WHERE user_id = ? OR is_global = 1 
    ORDER BY timestamp DESC 
    LIMIT 5
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$notifications = [];
while ($row = $result->fetch_assoc()) {
    $notifications[] = $row;
}

echo json_encode($notifications);

$stmt->close();
$conn->close();
?>