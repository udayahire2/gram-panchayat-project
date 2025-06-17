<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Log file for debugging
$log_file = 'update_debug.log';

// Function to log messages
function debug_log($message) {
    global $log_file;
    file_put_contents($log_file, date('[Y-m-d H:i:s] ') . $message . PHP_EOL, FILE_APPEND);
}

// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$database = "kusumba_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    debug_log("Connection failed: " . $conn->connect_error);
    die("Connection failed: " . $conn->connect_error);
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Log all received POST data
    debug_log("Received POST data: " . print_r($_POST, true));

    // Sanitize and validate inputs
    $record_id = $conn->real_escape_string($_POST['record_id']);
    $property_number = $conn->real_escape_string($_POST['property_number']);
    $owner_name = $conn->real_escape_string($_POST['owner_name']);
    $water_tax_type = $conn->real_escape_string($_POST['water_tax_type']);
    $previous_water_tax = floatval($_POST['previous_water_tax']);
    $water_tax = floatval($_POST['water_tax']);
    $total_water_tax = floatval($_POST['total_water_tax']);

    // Prepare SQL update statement
    $sql = "UPDATE property_taxes SET 
            property_number = '$property_number', 
            owner_name = '$owner_name', 
            water_tax_type = '$water_tax_type', 
            previous_water_tax = $previous_water_tax, 
            water_tax = $water_tax, 
            total_water_tax = $total_water_tax 
            WHERE id = $record_id";

    debug_log("SQL Query: " . $sql);

    if ($conn->query($sql) === TRUE) {
        debug_log("Update successful");
        // Redirect back to view_water.php with success message
        header("Location: view_water.php?update_success=1");
        exit();
    } else {
        debug_log("Update failed: " . $conn->error);
        // Redirect back with error
        header("Location: view_water.php?update_error=1");
        exit();
    }
} else {
    debug_log("No POST data received");
}

$conn->close();
?>