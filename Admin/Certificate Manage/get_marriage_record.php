<?php
header('Content-Type: application/json');

// Database connection settings
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "kusumba_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(['error' => "Connection failed: " . $conn->connect_error]));
}

// Get record ID from request
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    echo json_encode(['error' => 'Invalid record ID']);
    exit;
}

// Prepare and execute query
$sql = "SELECT * FROM marriage_certificates WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Return record as JSON
    $record = $result->fetch_assoc();
    echo json_encode($record);
} else {
    echo json_encode(['error' => 'Record not found']);
}

$stmt->close();
$conn->close();
?>