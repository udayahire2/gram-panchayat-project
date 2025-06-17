<?php
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "kusumba_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

if (isset($_GET['id'])) {
    $record_id = intval($_GET['id']);
    $sql = "SELECT * FROM death_certificate WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $record_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
        echo json_encode($data);
    } else {
        echo json_encode(['error' => 'Record not found']);
    }
    
    $stmt->close();
} else {
    echo json_encode(['error' => 'Invalid request']);
}

$conn->close();
?>