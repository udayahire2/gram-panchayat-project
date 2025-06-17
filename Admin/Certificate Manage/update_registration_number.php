<?php
// Database connection settings
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "kusumba_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => "Connection failed: " . $conn->connect_error]));
}

if(isset($_POST['id']) && isset($_POST['registration_number'])) {
    $id = $_POST['id'];
    $registration_number = $_POST['registration_number'];
    
    $sql = "UPDATE approved_birth_certificates SET registration_number = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $registration_number, $id);
    
    if($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => $conn->error]);
    }
    
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
}

$conn->close();
?>