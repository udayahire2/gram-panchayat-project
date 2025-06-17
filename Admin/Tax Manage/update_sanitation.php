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
    // Collect and sanitize form data
    $property_number = $conn->real_escape_string($_POST['property_number']);
    $owner_name = $conn->real_escape_string($_POST['owner_name']);
    $house_type = $conn->real_escape_string($_POST['house_type']);
    $previous_sanitation_tax = floatval($_POST['previous_sanitation_tax']);
    $sanitation_tax = floatval($_POST['sanitation_tax']);
    $total_sanitation_tax = floatval($_POST['total_sanitation_tax']);

    // Prepare update statement
    $stmt = $conn->prepare("UPDATE property_tax SET 
        owner_name = ?, 
        house_type = ?, 
        previous_sanitation_tax = ?, 
        sanitation_tax = ?, 
        total_sanitation_tax = ? 
        WHERE property_number = ?");

    $stmt->bind_param("ssddds", 
        $owner_name, 
        $house_type, 
        $previous_sanitation_tax, 
        $sanitation_tax, 
        $total_sanitation_tax, 
        $property_number
    );

    // Execute the statement
    if ($stmt->execute()) {
        // Redirect back to view page with success message
        header("Location: view_sanitation.php?update_success=1");
        exit();
    } else {
        // Redirect back with error message
        header("Location: view_sanitation.php?update_error=" . urlencode($stmt->error));
        exit();
    }

    
}

// Close connection
$conn->close();
?>