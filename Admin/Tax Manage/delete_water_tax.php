<?php
// Include session check to ensure only authorized users can access
require_once 'session_check.php';

// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$database = "kusumba_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    // Log connection error
    error_log("Database Connection Failed: " . $conn->connect_error);
    
    // Redirect with error message
    header("Location: view_water.php?error=database_connection");
    exit();
}

// Validate and sanitize input
$property_number = isset($_GET['property_number']) ? $conn->real_escape_string($_GET['property_number']) : null;

// Validate property number
if (empty($property_number)) {
    // Log invalid input
    error_log("Delete Water Tax: Invalid or missing property number");
    
    // Redirect with error message
    header("Location: view_water.php?error=invalid_property");
    exit();
}

// Begin transaction for data integrity
$conn->begin_transaction();

try {
    // Prepare delete statement with parameterized query
    $delete_stmt = $conn->prepare("DELETE FROM property_tax WHERE property_number = ?");
    $delete_stmt->bind_param("s", $property_number);
    
    // Execute delete
    if (!$delete_stmt->execute()) {
        // If deletion fails, throw an exception
        throw new Exception("Failed to delete record: " . $delete_stmt->error);
    }
    
    // Check if any rows were actually deleted
    if ($delete_stmt->affected_rows === 0) {
        // No matching record found
        throw new Exception("No water tax record found for property number: " . $property_number);
    }
    
    // Close statement
    $delete_stmt->close();
    
    // Commit transaction
    $conn->commit();
    
    // Log successful deletion
    error_log("Water Tax Record Deleted: Property Number " . $property_number);
    
    // Redirect with success message
    header("Location: view_water.php?delete_success=1&property_number=" . urlencode($property_number));
    exit();

} catch (Exception $e) {
    // Rollback transaction in case of error
    $conn->rollback();
    
    // Log the full error
    error_log("Delete Water Tax Error: " . $e->getMessage());
    
    // Redirect with specific error
    header("Location: view_water.php?error=" . urlencode($e->getMessage()));
    exit();
} finally {
    // Always close the connection
    $conn->close();
}
?>