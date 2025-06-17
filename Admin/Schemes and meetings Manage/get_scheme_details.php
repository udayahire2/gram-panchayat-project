<?php
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

if (isset($_GET['id'])) {
    $scheme_id = $conn->real_escape_string($_GET['id']);
    
    $sql = "SELECT description, required_documents FROM scheme_db WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $scheme_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        header('Content-Type: application/json');
        echo json_encode($row);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Scheme not found']);
    }
    
    $stmt->close();
} else {
    http_response_code(400);
    echo json_encode(['error' => 'No scheme ID provided']);
}

$conn->close();
?>