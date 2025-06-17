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
    die(json_encode([
        'success' => false,
        'message' => "Connection failed: " . $conn->connect_error
    ]));
}

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $status = isset($_POST['status']) ? $_POST['status'] : '';
    
    if ($id && in_array($status, ['approved', 'rejected'])) {
        $sql = "UPDATE scheme_recipients SET status = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $status, $id);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => "Error updating status: " . $stmt->error
            ]);
        }
        
        $stmt->close();
    } else {
        echo json_encode([
            'success' => false,
            'message' => "Invalid parameters"
        ]);
    }
}

$conn->close();
?>