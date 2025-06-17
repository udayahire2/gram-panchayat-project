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

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $record_id = $_POST['record_id'];
    $property_number = $_POST['property_number'];
    $owner_name = $_POST['owner_name'];
    $house_type = $_POST['house_type'];
    $area_sqft = $_POST['area_sqft'];
    $total_home_tax = $_POST['total_home_tax'];

    // Prepare SQL update statement
    $sql = "UPDATE property_taxes SET 
            property_number = ?, 
            owner_name = ?, 
            house_type = ?, 
            area_sqft = ?, 
            total_home_tax = ? 
            WHERE id = ?";

    // Prepare and bind parameters
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssddi", $property_number, $owner_name, $house_type, $area_sqft, $total_home_tax, $record_id);

    // Execute the statement
    if ($stmt->execute()) {
        // Check if any rows were actually updated
        if ($stmt->affected_rows > 0) {
            // Redirect back to view_home.php with success message
            header("Location: view_home.php?update_success=1");
        } else {
            // No rows updated (might mean record not found)
            header("Location: view_home.php?update_error=1&error_details=no_rows_updated");
        }
    } else {
        // Execution failed
        header("Location: view_home.php?update_error=1&error_details=execution_failed");
    }

    // Close statement
    $stmt->close();
    exit();
}

// Close connection
$conn->close();
?>