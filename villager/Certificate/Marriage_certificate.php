<?php

require_once '../login/session_check.php';
checkLogin();
// Database connection  
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "kusumba_db";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Add email column if it doesn't exist
    $sql = "ALTER TABLE marriage_certificates ADD COLUMN IF NOT EXISTS email VARCHAR(255)";
    $conn->exec($sql);
    
    // Get villager's email using session user_id
    $userId = getUserId();
    $stmt = $conn->prepare("SELECT email FROM villager WHERE id = ?");
    $stmt->execute([$userId]);
    $villager = $stmt->fetch(PDO::FETCH_ASSOC);
    $villagerEmail = $villager ? $villager['email'] : null;
    
    if (!$villagerEmail) {
        throw new Exception('Villager email not found');
    }
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Generate certificate number
        $stmt = $conn->query("SELECT MAX(certificate_no) as max_cert FROM marriage_certificates");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $certificate_no = $result['max_cert'] ? $result['max_cert'] + 1 : 1001;

        // Define upload directory
        $uploadsDir = "../uploads/marriage_certificates/";
        
        // Create upload directory if it doesn't exist
        if (!file_exists($uploadsDir)) {
            mkdir($uploadsDir, 0755, true);
        }
        
        // Process file uploads
        $husband_photo = processFileUpload('husband_photo', $uploadsDir);
        $husband_aadhar_doc = processFileUpload('husband_aadhar_doc', $uploadsDir);
        $wife_photo = processFileUpload('wife_photo', $uploadsDir);
        $wife_aadhar_doc = processFileUpload('wife_aadhar_doc', $uploadsDir);
        $witness1_aadhar_doc = processFileUpload('witness1_aadhar_doc', $uploadsDir);
        $witness2_aadhar_doc = processFileUpload('witness2_aadhar_doc', $uploadsDir);
        $witness3_aadhar_doc = processFileUpload('witness3_aadhar_doc', $uploadsDir);

        $sql = "INSERT INTO marriage_certificates (
            certificate_no, husband_name, husband_address, husband_dob, husband_age, 
            husband_cast, husband_photo, husband_aadhar_no, husband_aadhar_doc,
            wife_name, wife_address, wife_dob, wife_age, wife_cast, wife_photo,
            wife_aadhar_no, wife_aadhar_doc, marriage_date, marriage_place,
            witness1_name, witness1_aadhar_doc, witness2_name, witness2_aadhar_doc,
            witness3_name, witness3_aadhar_doc, registration_date, created_at, status, email
        ) VALUES (
            :certificate_no, :husband_name, :husband_address, :husband_dob, :husband_age,
            :husband_cast, :husband_photo, :husband_aadhar_no, :husband_aadhar_doc,
            :wife_name, :wife_address, :wife_dob, :wife_age, :wife_cast, :wife_photo,
            :wife_aadhar_no, :wife_aadhar_doc, :marriage_date, :marriage_place,
            :witness1_name, :witness1_aadhar_doc, :witness2_name, :witness2_aadhar_doc,
            :witness3_name, :witness3_aadhar_doc, CURDATE(), NOW(), NULL, :email
        )";

        $stmt = $conn->prepare($sql);
        $stmt->execute([
            'certificate_no' => $certificate_no,
            'husband_name' => $_POST['husband_name'],
            'husband_address' => $_POST['husband_address'],
            'husband_dob' => $_POST['husband_dob'],
            'husband_age' => $_POST['husband_age'],
            'husband_cast' => $_POST['husband_cast'],
            'husband_photo' => $husband_photo,
            'husband_aadhar_no' => $_POST['husband_aadhar_no'],
            'husband_aadhar_doc' => $husband_aadhar_doc,
            'wife_name' => $_POST['wife_name'],
            'wife_address' => $_POST['wife_address'],
            'wife_dob' => $_POST['wife_dob'],
            'wife_age' => $_POST['wife_age'],
            'wife_cast' => $_POST['wife_cast'],
            'wife_photo' => $wife_photo,
            'wife_aadhar_no' => $_POST['wife_aadhar_no'],
            'wife_aadhar_doc' => $wife_aadhar_doc,
            'marriage_date' => $_POST['marriage_date'],
            'marriage_place' => $_POST['marriage_place'],
            'witness1_name' => $_POST['witness1_name'],
            'witness1_aadhar_doc' => $witness1_aadhar_doc,
            'witness2_name' => $_POST['witness2_name'],
            'witness2_aadhar_doc' => $witness2_aadhar_doc,
            'witness3_name' => $_POST['witness3_name'],
            'witness3_aadhar_doc' => $witness3_aadhar_doc,
            'email' => $villagerEmail
        ]);

        echo "<script>alert('Marriage certificate application submitted successfully!');</script>";
    } catch(PDOException $e) {
        echo "<script>alert('Error: " . str_replace("'", "\'", $e->getMessage()) . "');</script>";
    } catch(Exception $e) {
        echo "<script>alert('Error: " . str_replace("'", "\'", $e->getMessage()) . "');</script>";
    }
}

/**
 * Process file upload using $_FILES
 * @param string $inputName The name of the file input field
 * @param string $targetDir The directory where the file should be saved
 * @return string The filename of the uploaded file
 * @throws Exception If there's an error with the file upload
 */
function processFileUpload($inputName, $targetDir) {
    // Check if file was uploaded
    if (!isset($_FILES[$inputName]) || $_FILES[$inputName]['error'] != 0) {
        $errorMessage = "No file uploaded or error occurred";
        if (isset($_FILES[$inputName])) {
            switch ($_FILES[$inputName]['error']) {
                case 1: $errorMessage = "File exceeds upload_max_filesize"; break;
                case 2: $errorMessage = "File exceeds MAX_FILE_SIZE"; break;
                case 3: $errorMessage = "File was only partially uploaded"; break;
                case 4: $errorMessage = "No file was uploaded"; break;
                case 6: $errorMessage = "Missing a temporary folder"; break;
                case 7: $errorMessage = "Failed to write file to disk"; break;
                case 8: $errorMessage = "A PHP extension stopped the file upload"; break;
            }
        }
        throw new Exception("Error uploading $inputName: $errorMessage");
    }
    
    // Get file details
    $fileName = $_FILES[$inputName]['name'];
    $fileSize = $_FILES[$inputName]['size'];
    $fileTmpName = $_FILES[$inputName]['tmp_name'];
    $fileType = $_FILES[$inputName]['type'];
    
    // Validate file size (limit to 5MB)
    $maxSize = 5 * 1024 * 1024; // 5MB in bytes
    if ($fileSize > $maxSize) {
        throw new Exception("File size too large for $inputName. Maximum size is 5MB.");
    }
    
    // Validate file type
    $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf'];
    if (!in_array($fileType, $allowedTypes)) {
        throw new Exception("Invalid file type for $inputName. Allowed types: JPG, PNG, PDF");
    }
    
    // Generate unique filename to prevent overwriting
    $newFileName = time() . '_' . basename($fileName);
    $targetFilePath = $targetDir . $newFileName;
    
    // Move uploaded file to target directory
    if (move_uploaded_file($fileTmpName, $targetFilePath)) {
        return $newFileName;
    } else {
        throw new Exception("Failed to save file $inputName");
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Marriage Certificate Application</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/marriage_form.css">
</head>
<body>
 <div>
    <div class="container form-container" enctype="multipart/form-data">
    <div class="btn-container">
            <div class="btn-container-inner">
                <a href="../Main/index.html" class="back-btn">Back to Home</a>
            </div>
        </div>
        <h2 class="form-title">Marriage Certificate Application Form</h2>
        <form action="" method="POST" enctype="multipart/form-data" id="marriageForm">
            <!-- Husband Details -->
            <h4 class="mb-3">Husband Details</h4>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label>Full Name <span class="required">*</span></label>
                    <input type="text" class="form-control" name="husband_name" required>
                </div>
                <div class="form-group col-md-6">
                    <label>Address <span class="required">*</span></label>
                    <input type="text" class="form-control" name="husband_address" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label>Date of Birth <span class="required">*</span></label>
                    <input type="date" class="form-control" name="husband_dob" required onchange="calculateAge(this.value, 'husband_age')">
                </div>
                <div class="form-group col-md-4">
                    <label>Age <span class="required">*</span></label>
                    <input type="number" class="form-control" name="husband_age" readonly required>
                </div>
                <div class="form-group col-md-4">
                    <label>Caste <span class="required">*</span></label>
                    <input type="text" class="form-control" name="husband_cast" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label>Photo <span class="required">*</span></label>
                    <input type="file" class="form-control-file" name="husband_photo" required accept="image/*">
                </div>
                <div class="form-group col-md-6">
                    <label>Aadhar Number <span class="required">*</span></label>
                    <input type="text" class="form-control" name="husband_aadhar_no" required pattern="[0-9]{12}" title="Please enter valid 12 digit Aadhar number">
                </div>
            </div>
            <div class="form-group">
                <label>Aadhar Card Document <span class="required">*</span></label>
                <input type="file" class="form-control-file" name="husband_aadhar_doc" required accept=".pdf,.jpg,.jpeg,.png">
            </div>

            <!-- Wife Details -->
            <h4 class="mb-3 mt-4">Wife Details</h4>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label>Full Name <span class="required">*</span></label>
                    <input type="text" class="form-control" name="wife_name" required>
                </div>
                <div class="form-group col-md-6">
                    <label>Address <span class="required">*</span></label>
                    <input type="text" class="form-control" name="wife_address" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label>Date of Birth <span class="required">*</span></label>
                    <input type="date" class="form-control" name="wife_dob" required onchange="calculateAge(this.value, 'wife_age')">
                </div>
                <div class="form-group col-md-4">
                    <label>Age <span class="required">*</span></label>
                    <input type="number" class="form-control" name="wife_age" readonly required>
                </div>
                <div class="form-group col-md-4">
                    <label>Caste <span class="required">*</span></label>
                    <input type="text" class="form-control" name="wife_cast" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label>Photo <span class="required">*</span></label>
                    <input type="file" class="form-control-file" name="wife_photo" required accept="image/*">
                </div>
                <div class="form-group col-md-6">
                    <label>Aadhar Number <span class="required">*</span></label>
                    <input type="text" class="form-control" name="wife_aadhar_no" required pattern="[0-9]{12}" title="Please enter valid 12 digit Aadhar number">
                </div>
            </div>
            <div class="form-group">
                <label>Aadhar Card Document <span class="required">*</span></label>
                <input type="file" class="form-control-file" name="wife_aadhar_doc" required accept=".pdf,.jpg,.jpeg,.png">
            </div>

            <!-- Marriage Details -->
            <h4 class="mb-3 mt-4">Marriage Details</h4>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label>Date of Marriage <span class="required">*</span></label>
                    <input type="date" class="form-control" name="marriage_date" required>
                </div>
                <div class="form-group col-md-6">
                    <label>Place of Marriage <span class="required">*</span></label>
                    <input type="text" class="form-control" name="marriage_place" required>
                </div>
            </div>

            <!-- Witness Details -->
            <h4 class="mb-3 mt-4">Witness Details</h4>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label>Witness 1 Name <span class="required">*</span></label>
                    <input type="text" class="form-control" name="witness1_name" required>
                </div>
                <div class="form-group col-md-6">
                    <label>Witness 1 Aadhar Document <span class="required">*</span></label>
                    <input type="file" class="form-control-file" name="witness1_aadhar_doc" required accept=".pdf,.jpg,.jpeg,.png">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label>Witness 2 Name <span class="required">*</span></label>
                    <input type="text" class="form-control" name="witness2_name" required>
                </div>
                <div class="form-group col-md-6">
                    <label>Witness 2 Aadhar Document <span class="required">*</span></label>
                    <input type="file" class="form-control-file" name="witness2_aadhar_doc" required accept=".pdf,.jpg,.jpeg,.png">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label>Witness 3 Name <span class="required">*</span></label>
                    <input type="text" class="form-control" name="witness3_name" required>
                </div>
                <div class="form-group col-md-6">
                    <label>Witness 3 Aadhar Document <span class="required">*</span></label>
                    <input type="file" class="form-control-file" name="witness3_aadhar_doc" required accept=".pdf,.jpg,.jpeg,.png">
                </div>
            </div>

            <!-- Submit Button -->
            <div class="btn-group">
                        <button type="submit" class="btn-primary">नोंदणी करा / Register</button>
                        <button type="reset" class="btn-reset">रीसेट / Reset</button>
                        <button type="button" class="btn-cancel" id="cancelButton">रद्द करा / Cancel</button>
                    </div>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    
    <script src="../assets/js/marriage_form.js"></script>
</body>
</html>