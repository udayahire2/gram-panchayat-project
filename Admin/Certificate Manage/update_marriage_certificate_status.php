<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "certificate_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode([
        'success' => false, 
        'message' => 'Database connection failed: ' . $conn->connect_error
    ]));
}

// Ensure the marriage_certificate_db table has status columns
$alter_table_sql = "ALTER TABLE marriage_certificate_db 
                    ADD COLUMN status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
                    ADD COLUMN rejection_reason TEXT";
$conn->query($alter_table_sql);

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);
    $status = $conn->real_escape_string($_POST['status']);
    $reason = isset($_POST['reason']) ? $conn->real_escape_string($_POST['reason']) : '';

    $update_sql = "UPDATE marriage_certificate_db 
                   SET status = '$status'" . 
                   ($status === 'rejected' ? ", rejection_reason = '$reason'" : "") . 
                   " WHERE id = $id";

    if ($conn->query($update_sql) === TRUE) {
        echo json_encode([
            'success' => true, 
            'message' => 'Status updated successfully'
        ]);
    } else {
        echo json_encode([
            'success' => false, 
            'message' => 'Error updating status: ' . $conn->error
        ]);
    }
}

$conn->close();
?>