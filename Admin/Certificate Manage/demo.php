<?php
require_once '../login/session_check.php';

// Check login before allowing access
checkLogin();

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', '/xampp/htdocs/CPP WEB/villager/Certificate/marriage_certificate_errors.log');

// Enhanced error handling function
function logAndReturnError($message, $details = null)
{
    $fullMessage = $message;
    if ($details !== null) {
        $fullMessage .= " Details: " . print_r($details, true);
    }

    error_log($fullMessage);

    // Log additional context
    error_log("POST Data: " . print_r($_POST, true));
    error_log("FILES Data: " . print_r($_FILES, true));

    echo json_encode([
        "status" => "error",
        "message" => $message,
        "details" => $details
    ]);
    exit;
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "kusumba_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    logAndReturnError("Connection failed: " . $conn->connect_error);
}

// Create database if not exists
$sql_create_db = "CREATE DATABASE IF NOT EXISTS $dbname";
if (!$conn->query($sql_create_db)) {
    logAndReturnError("Error creating database: " . $conn->error);
}

$conn->select_db($dbname);

// Create table
$sql_create_table = "CREATE TABLE IF NOT EXISTS marriage_certificate_db (
    id INT AUTO_INCREMENT PRIMARY KEY,
    husband_name VARCHAR(255) NOT NULL,
    husband_address TEXT NOT NULL,
    husband_dob DATE NOT NULL,
    husband_age INT NOT NULL,
    husband_cast VARCHAR(100) NOT NULL,
    husband_photo VARCHAR(255),
    husband_aadhar VARCHAR(255),
    husband_aadhar_no VARCHAR(12) NOT NULL,
    wife_name VARCHAR(255) NOT NULL,
    wife_address TEXT NOT NULL,
    wife_dob DATE NOT NULL,
    wife_age INT NOT NULL,
    wife_cast VARCHAR(100) NOT NULL,
    wife_photo VARCHAR(255),
    wife_aadhar VARCHAR(255),
    wife_aadhar_no VARCHAR(12) NOT NULL,
    marriage_date DATE NOT NULL,
    marriage_place VARCHAR(255) NOT NULL,
    registration_date DATE NOT NULL,
    witness1_name VARCHAR(255) NOT NULL,
    witness1_aadhar VARCHAR(255) NOT NULL,
    witness2_name VARCHAR(255) NOT NULL,
    witness2_aadhar VARCHAR(255) NOT NULL,
    witness3_name VARCHAR(255) NOT NULL,
    witness3_aadhar VARCHAR(255) NOT NULL,
    marriage_priest_name VARCHAR(255) NOT NULL,
    marriage_priest_aadhar VARCHAR(255) NOT NULL,
    marriage_card VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if (!$conn->query($sql_create_table)) {
    logAndReturnError("Error creating table: " . $conn->error);
}

function uploadFile($file, $prefix) {
    $target_dir = "../../Admin/Certificate Manage/uploads/marriage/"; // Changed from birth to marriage
    if (!is_dir($target_dir)) {
        if (!mkdir($target_dir, 0755, true)) {
            logAndReturnError("Failed to create upload directory: " . $target_dir);
        }
    }

    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        logAndReturnError("File upload error: " . print_r($file, true));
    }

    $allowed_types = ['image/jpeg', 'image/png', 'application/pdf'];
    $max_size = 5 * 1024 * 1024; // 5MB

    $file_type = $file['type'];
    $file_size = $file['size'];

    if (!in_array($file_type, $allowed_types) || $file_size > $max_size) {
        logAndReturnError("Invalid file type or size: Type = $file_type, Size = $file_size");
    }

    $safe_name = preg_replace("/[^a-zA-Z0-9.]/", "", basename($file['name']));
    $unique_name = $prefix . '_' . uniqid() . '_' . $safe_name;
    $target_file = $target_dir . $unique_name;

    if (move_uploaded_file($file['tmp_name'], $target_file)) {
        chmod($target_file, 0644);
        return $unique_name;
    }

    logAndReturnError("Failed to move uploaded file to: " . $target_file);
}

function validateFormData($data)
{
    $errors = [];

    // ... existing validation code ...

    // Validate Aadhar numbers
    $aadhar_fields = [
        'husband_aadhar_no' => "Husband's Aadhar",
        'wife_aadhar_no' => "Wife's Aadhar",
        'witness1_aadhar' => "Witness 1 Aadhar",
        'witness2_aadhar' => "Witness 2 Aadhar",
        'witness3_aadhar' => "Witness 3 Aadhar",
        'priest_aadhar' => "Priest Aadhar"
    ];

    foreach ($aadhar_fields as $field => $label) {
        if (!preg_match("/^\d{12}$/", $data[$field])) {
            $errors[] = "$label must be a 12-digit number";
        }
    }

    return $errors;
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // ... (keep your existing form data collection and validation) ...

    // Upload files
    $husband_photo = uploadFile($_FILES['husband_photo'], 'husband_photo');
    $husband_aadhar = uploadFile($_FILES['husband_aadhar'], 'husband_aadhar');
    $wife_photo = uploadFile($_FILES['wife_photo'], 'wife_photo');
    $wife_aadhar = uploadFile($_FILES['wife_aadhar'], 'wife_aadhar');
    $marriage_card = uploadFile($_FILES['marriage_card'], 'marriage_card');

    // Prepare the SQL statement
    $sql = "INSERT INTO marriage_certificate_db (
        husband_name, husband_address, husband_dob, husband_age, husband_cast, husband_photo, husband_aadhar, husband_aadhar_no,
        wife_name, wife_address, wife_dob, wife_age, wife_cast, wife_photo, wife_aadhar, wife_aadhar_no,
        marriage_date, marriage_place, registration_date,
        witness1_name, witness1_aadhar, witness2_name, witness2_aadhar, witness3_name, witness3_aadhar,
        marriage_priest_name, marriage_priest_aadhar, marriage_card
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);  // Added statement preparation
    if (!$stmt) {
        logAndReturnError("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param(
        "sssisssssssisssssssssssssss",
        $formData['husband_name'],
        $formData['husband_address'],
        $formData['husband_dob'],
        $formData['husband_age'],
        $formData['husband_cast'],
        $husband_photo,
        $husband_aadhar,
        $formData['husband_aadhar_no'],
        $formData['wife_name'],
        $formData['wife_address'],
        $formData['wife_dob'],
        $formData['wife_age'],
        $formData['wife_cast'],
        $wife_photo,
        $wife_aadhar,
        $formData['wife_aadhar_no'],
        $formData['marriage_date'],
        $formData['marriage_place'],
        $formData['registration_date'],
        $formData['witness1_name'],
        $formData['witness1_aadhar'],
        $formData['witness2_name'],
        $formData['witness2_aadhar'],
        $formData['witness3_name'],
        $formData['witness3_aadhar'],
        $formData['priest_name'],
        $formData['priest_aadhar'],
        $marriage_card
    );

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Marriage Certificate submitted successfully"]);
    } else {
        logAndReturnError("Error executing statement: " . $stmt->error);
    }

    $stmt->close();
    $conn->close();
}
?>