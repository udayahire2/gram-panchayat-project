<?php
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "kusumba_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(['error' => 'Connection failed: ' . $conn->connect_error]));
}

if (!isset($_GET['id'])) {
    echo json_encode(['error' => 'No record ID provided']);
    exit;
}

$record_id = $_GET['id'];
$sql = "SELECT * FROM marriage_certificates WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $record_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo json_encode($row);
} else {
    echo json_encode(['error' => 'Record not found']);
}

$stmt->close();
$conn->close();
?>