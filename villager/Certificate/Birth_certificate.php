<?php
require_once '../login/session_check.php';

// Check login before allowing access
checkLogin();

// Database connection settings
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "kusumba_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch villager's email (assuming session contains user_id)
$userId = $_SESSION['user_id'] ?? null; // Adjust based on your session variable
if (!$userId) {
    // Log the session for debugging
    error_log("Session data: " . print_r($_SESSION, true));
    die('User not identified in session. Please log in again.');
}

// Fetch email using user_id (adjust column name as per your table)
$stmt = $conn->prepare("SELECT email FROM villager WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$villager = $result->fetch_assoc();

if (!$villager || empty($villager['email'])) {
    die('Villager email not found in database.');
}
$villagerEmail = $villager['email'];

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $name = $_POST['name'];
    $gender = $_POST['gender'];
    $dob = $_POST['dob'];
    $placeOfBirth = $_POST['placeOfBirth'];
    $proofOfBirth = $_POST['proofOfBirth'];
    $motherName = $_POST['motherName'];
    $motherAadhaar = $_POST['motherAadhaar'];
    $fatherName = $_POST['fatherName'];
    $fatherAadhaar = $_POST['fatherAadhaar'];
    $address = $_POST['address'];
    $registrationDate = $_POST['registrationDate'];

    // Generate a registration number in the format DYYYYMMDDHHMMSSUUUUUU
    $registrationNumber = 'B' . date('YmdHis') . str_pad(rand(1, 999999), 6, '0', STR_PAD_LEFT);

    // File upload handling
    $proofOfBirthFile = "";
    $motherAadharProof = "";
    $fatherAadharProof = "";
    $marriageCertificate = "";

    // Upload directory
    $uploadDir = "../uploads/birth_certificate/";

    // Create upload directory if it doesn't exist
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Function to handle file uploads
    function uploadFile($fileInput, $uploadDir)
    {
        if (isset($_FILES[$fileInput]) && $_FILES[$fileInput]['error'] == 0) {
            $fileName = time() . '_' . basename($_FILES[$fileInput]['name']);
            $targetFilePath = $uploadDir . $fileName;

            // Move uploaded file to target directory
            if (move_uploaded_file($_FILES[$fileInput]['tmp_name'], $targetFilePath)) {
                return $fileName;
            }
        }
        return "";
    }

    // Upload files
    $proofOfBirthFile = uploadFile('proofOfBirthFile', $uploadDir);
    $motherAadharProof = uploadFile('motherAadharProof', $uploadDir);
    $fatherAadharProof = uploadFile('fatherAadharProof', $uploadDir);
    $marriageCertificate = uploadFile('marriageCertificate', $uploadDir);

    // Prepare SQL statement
    $sql = "INSERT INTO birth_certificate (
                name, 
                gender, 
                dob, 
                place_of_birth, 
                proof_of_birth, 
                proof_of_birth_file, 
                mother_name, 
                mother_aadhaar, 
                mother_aadhaar_proof, 
                father_name, 
                father_aadhaar, 
                father_aadhaar_proof, 
                address, 
                registration_date, 
                marriage_certificate,
                registration_number,
                villager_email,
                created_at
            ) VALUES (
                ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW()
            )";

    // Prepare and bind parameters
    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "sssssssssssssssss",
        $name,
        $gender,
        $dob,
        $placeOfBirth,
        $proofOfBirth,
        $proofOfBirthFile,
        $motherName,
        $motherAadhaar,
        $motherAadharProof,
        $fatherName,
        $fatherAadhaar,
        $fatherAadharProof,
        $address,
        $registrationDate,
        $marriageCertificate,
        $registrationNumber,
        $villagerEmail
    );

    // Execute the statement
    if ($stmt->execute()) {
        // Set success message
        $successMsg = "Birth certificate registration successful!";
    } else {
        // Set error message
        $errorMsg = "Error: " . $stmt->error;
    }

    // Close statement
    $stmt->close();
}

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>जन्म प्रमाणपत्र / Birth Certificate</title>
    <link rel="stylesheet" href="../assets/css/birth_form.css">
</head>

<body>
    <div class="page-wrapper">
        <div class="btn-container">
            <div class="btn-container-inner">
                <a href="../Main/index.html" class="back-btn">Back to Home</a>
            </div>
        </div>
        <div class="container">
            <?php if (isset($successMsg)): ?>
                <div class="alert alert-success">
                    <?php echo $successMsg; ?>
                </div>
            <?php endif; ?>

            <?php if (isset($errorMsg)): ?>
                <div class="alert alert-danger">
                    <?php echo $errorMsg; ?>
                </div>
            <?php endif; ?>

            <div class="form-section" id="formSection">
                <h1>जन्म प्रमाणपत्र / Birth Certificate Form</h1>
                <!-- Update the form to include method and action -->
                <form id="birthCertificateForm" method="POST"
                    action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
                    <!-- All your existing form fields -->
                    <!-- ... -->

                    <div class="form-group">
                        <label for="name">नाव / Name:</label>
                        <input type="text" id="name" name="name" required
                            placeholder="उदाहरण: राम शर्मा / Example: Ram Sharma">
                    </div>

                    <div class="form-group">
                        <label for="gender">लिंग / Gender:</label>
                        <select id="gender" name="gender" required>
                            <option value="">लिंग निवडा / Select Gender</option>
                            <option value="MALE">पुरुष / Male</option>
                            <option value="FEMALE">महिला / Female</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="dob">जन्म तारीख / Date of Birth:</label>
                        <input type="date" id="dob" name="dob" required>
                    </div>

                    <div class="form-group">
                        <label for="placeOfBirth">जन्म स्थान / Place of Birth:</label>
                        <input type="text" id="placeOfBirth" name="placeOfBirth" required
                            placeholder="उदाहरण: पुणे / Example: Pune">
                    </div>

                    <div class="form-group">
                        <label for="proofOfBirth">जन्म प्रमाण / Proof of Birth:</label>
                        <select id="proofOfBirth" name="proofOfBirth" required>
                            <option value="">प्रमाण निवडा / Select Proof</option>
                            <option value="hospital_certificate">अस्पताल प्रमाणपत्र / Hospital Certificate</option>
                            <option value="hospital_statement">अस्पताल वक्तव्य / Hospital Statement</option>
                            <option value="parental_affidavit">पालक शपथपत्र / Parental Affidavit</option>
                        </select>
                        <input type="file" id="proofOfBirthFile" name="proofOfBirthFile"
                            accept=".pdf,.jpg,.jpeg,.png" />
                    </div>

                    <div class="form-group">
                        <label for="motherName">आईचे नाव / Mother's Name:</label>
                        <input type="text" id="motherName" name="motherName" required
                            placeholder="उदाहरण: सीता शर्मा / Example: Sita Sharma">
                    </div>

                    <div class="form-group">
                        <label for="motherAadhaar">आईचा आधार क्रमांक / Mother's Aadhaar:</label>
                        <input type="text" id="motherAadhaar" name="motherAadhaar" pattern="[0-9]{12}"
                            placeholder="12-digit Aadhaar number">
                    </div>

                    <div class="form-group">
                        <label for="motherAadharProof">आईचा आधार प्रमाण / Mother's Aadhaar Proof:</label>
                        <input type="file" id="motherAadharProof" name="motherAadharProof"
                            accept=".pdf,.jpg,.jpeg,.png" />
                    </div>

                    <div class="form-group">
                        <label for="fatherName">वडिलांचे नाव / Father's Name:</label>
                        <input type="text" id="fatherName" name="fatherName" required
                            placeholder="उदाहरण: मोहन शर्मा / Example: Mohan Sharma">
                    </div>

                    <div class="form-group">
                        <label for="fatherAadhaar">वडिलांचा आधार क्रमांक / Father's Aadhaar:</label>
                        <input type="text" id="fatherAadhaar" name="fatherAadhaar" pattern="[0-9]{12}"
                            placeholder="12-digit Aadhaar number">
                    </div>

                    <div class="form-group">
                        <label for="fatherAadharProof">वडिलांचा आधार प्रमाण / Father's Aadhaar Proof:</label>
                        <input type="file" id="fatherAadharProof" name="fatherAadharProof"
                            accept=".pdf,.jpg,.jpeg,.png" />
                    </div>

                    <div class="form-group">
                        <label for="address">पत्ता / Address:</label>
                        <textarea id="address" name="address" required
                            placeholder="उदाहरण: पुणे, महाराष्ट्र / Example: Pune, Maharashtra"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="registrationDate">नोंदणी तारीख / Registration Date:</label>
                        <input type="date" id="registrationDate" name="registrationDate" required
                            value="<?php echo date('Y-m-d'); ?>">
                    </div>

                    <div class="form-group">
                        <label for="marriageCertificate">लग्न प्रमाणपत्र / Parents' Marriage Certificate:</label>
                        <input type="file" id="marriageCertificate" name="marriageCertificate"
                            accept=".pdf,.jpg,.jpeg,.png" />
                    </div>

                    <form id="myForm" method="POST" action="your-action-url.php">
                        <!-- Your form fields here -->

                        <!-- Button group with all three button types -->
                        <div class="btn-group">
                            <button type="submit" class="btn-primary">Submit</button>
                            <button type="reset" class="btn-reset">Reset</button>
                            <button type="button" class="btn-cancel"
                                onclick="window.location.href='previous-page.html'">Cancel</button>
                        </div>
                    </form>
                </form>
            </div>
        </div>
    </div>

    <!-- Optional: Add JavaScript for form validation -->
    <script>
        document.getElementById('birthCertificateForm').addEventListener('submit', function (e) {
            // You can add client-side validation here if needed
        });
    </script>
</body>

</html>