<?php
// Database connection settings
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "kusumba_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(['error' => "Connection failed: " . $conn->connect_error]));
}

if (isset($_GET['id'])) {
    $record_id = $_GET['id'];
    
    // Prepare the SQL statement
    $stmt = $conn->prepare("SELECT 
        marriage_certificate_file,
        husband_photo,
        husband_aadhar_doc,
        wife_photo,
        wife_aadhar_doc,
        witness1_aadhar_doc,
        witness2_aadhar_doc,
        witness3_aadhar_doc
        FROM marriage_certificates 
        WHERE id = ?");
    
    $stmt->bind_param("i", $record_id);
    
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $documents = [];
            
            // Check and add each document
            if (!empty($row['marriage_certificate_file'])) {
                $documents[] = [
                    'type' => 'Marriage Certificate',
                    'file' => $row['marriage_certificate_file'],
                    'path' => '/CPP WEB/villager/uploads/marriage_certificates/'
                ];
            }
            
            if (!empty($row['husband_photo'])) {
                $documents[] = [
                    'type' => 'Husband Photo',
                    'file' => $row['husband_photo'],
                    'path' => '/CPP WEB/villager/uploads/marriage_certificates/'
                ];
            }
            
            if (!empty($row['husband_aadhar_doc'])) {
                $documents[] = [
                    'type' => 'Husband Aadhar',
                    'file' => $row['husband_aadhar_doc'],
                    'path' => '/CPP WEB/villager/uploads/marriage_certificates/'
                ];
            }
            
            if (!empty($row['wife_photo'])) {
                $documents[] = [
                    'type' => 'Wife Photo',
                    'file' => $row['wife_photo'],
                    'path' => '/CPP WEB/villager/uploads/marriage_certificates/'
                ];
            }
            
            if (!empty($row['wife_aadhar_doc'])) {
                $documents[] = [
                    'type' => 'Wife Aadhar',
                    'file' => $row['wife_aadhar_doc'],
                    'path' => '/CPP WEB/villager/uploads/marriage_certificates/'
                ];
            }
            
            if (!empty($row['witness1_aadhar_doc'])) {
                $documents[] = [
                    'type' => 'Witness 1 Document',
                    'file' => $row['witness1_aadhar_doc'],
                    'path' => '/CPP WEB/villager/uploads/marriage_certificates/'
                ];
            }
            
            if (!empty($row['witness2_aadhar_doc'])) {
                $documents[] = [
                    'type' => 'Witness 2 Document',
                    'file' => $row['witness2_aadhar_doc'],
                    'path' => '/CPP WEB/villager/uploads/marriage_certificates/'
                ];
            }
            
            if (!empty($row['witness3_aadhar_doc'])) {
                $documents[] = [
                    'type' => 'Witness 3 Document',
                    'file' => $row['witness3_aadhar_doc'],
                    'path' => '/CPP WEB/villager/uploads/marriage_certificates/'
                ];
            }
            
            echo json_encode(['success' => true, 'documents' => $documents]);
        } else {
            echo json_encode(['error' => 'Record not found']);
        }
    } else {
        echo json_encode(['error' => 'Failed to fetch documents']);
    }
    
    $stmt->close();
} else {
    echo json_encode(['error' => 'No record ID provided']);
}

$conn->close();
?>