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
$userId = $_SESSION['user_id'] ?? null;
if (!$userId) {
    error_log("Session data: " . print_r($_SESSION, true));
    die('User not identified in session. Please log in again.');
}

// Fetch email using user_id
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
    $nameOfDeceased = $_POST['nameOfDeceased'];
    $gender = $_POST['gender'];
    $birthDate = $_POST['birthDate'];
    $age = $_POST['age'];
    $aadhaar = $_POST['aadhaar'];
    $dateOfDeath = $_POST['dateOfDeath'];
    $placeOfDeath = $_POST['placeOfDeath'];
    $address = $_POST['address'];
    $registrationDate = $_POST['registrationDate'];

    // Generate a certificate number
    $certificateNumber = 'DC' . date('YmdHis') . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);

    // File upload handling
    $aadhaarDocument = "";

    // Upload directory
    $uploadDir = "../uploads/death_certificate/";

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

            if (move_uploaded_file($_FILES[$fileInput]['tmp_name'], $targetFilePath)) {
                return $fileName;
            }
        }
        return "";
    }

    // Upload Aadhaar document
    $aadhaarDocument = uploadFile('aadhaarUpload', $uploadDir);

    // Prepare SQL statement
    $sql = "INSERT INTO death_certificate (
                certificate_number,
                name_of_deceased,
                gender,
                birth_date,
                age,
                aadhaar_number,
                aadhaar_document,
                date_of_death,
                place_of_death,
                address,
                registration_date,
                villager_email,
                created_at
            ) VALUES (
                ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW()
            )";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "ssssisssssss",
        $certificateNumber,
        $nameOfDeceased,
        $gender,
        $birthDate,
        $age,
        $aadhaar,
        $aadhaarDocument,
        $dateOfDeath,
        $placeOfDeath,
        $address,
        $registrationDate,
        $villagerEmail
    );

    if ($stmt->execute()) {
        $successMsg = "Death certificate registration successful!";
    } else {
        $errorMsg = "Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Death Certificate Generator</title>
    <link rel="stylesheet" href="../assets/css/death_form.css">
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
                <h1>मृत्यू प्रमाणपत्र फॉर्म / Death Certificate Form</h1>
                <form id="deathCertificateForm" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="nameOfDeceased" class="required">मृत व्यक्तीचे नाव / Name of Deceased:</label>
                            <input type="text" id="nameOfDeceased" name="nameOfDeceased" required>
                        </div>
                        <div class="form-group">
                            <label for="gender" class="required">लिंग / Gender:</label>
                            <select id="gender" name="gender" required>
                                <option value="">Select Gender</option>
                                <option value="MALE">पुरुष / Male</option>
                                <option value="FEMALE">महिला / Female</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="birthDate" class="required">जन्म तारीख / Birth Date:</label>
                            <input type="date" id="birthDate" name="birthDate" required>
                        </div>
                        <div class="form-group">
                            <label for="age" class="required">मृत्यूवेळी वय / Age at Death:</label>
                            <input type="number" id="age" name="age" min="0" max="150" step="1" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="aadhaar" class="required">आधार क्रमांक / Aadhaar Number:</label>
                            <input type="text" id="aadhaar" name="aadhaar" pattern="[0-9]{12}" placeholder="12 digits" required>
                        </div>
                        <div class="form-group">
                            <label for="aadhaarUpload" class="required">आधार दस्तऐवज अपलोड करा / Upload Aadhaar Document:</label>
                            <input type="file" id="aadhaarUpload" name="aadhaarUpload" accept=".pdf,.jpg,.jpeg,.png" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="dateOfDeath" class="required">मृत्यूची तारीख / Date of Death:</label>
                            <input type="date" id="dateOfDeath" name="dateOfDeath" required>
                        </div>
                        <div class="form-group">
                            <label for="registrationDate" class="required">नोंदणी तारीख / Registration Date:</label>
                            <input type="date" id="registrationDate" name="registrationDate" required value="<?php echo date('Y-m-d'); ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="placeOfDeath" class="required">मृत्यू ठिकाण / Place of Death:</label>
                        <input type="text" id="placeOfDeath" name="placeOfDeath" required>
                    </div>
                    <div class="form-group">
                        <label for="address" class="required">पत्ता / Address:</label>
                        <textarea id="address" name="address" required></textarea>
                    </div>
                    <div class="btn-group">
                        <button type="submit" class="btn-primary">नोंदणी करा / Register</button>
                        <button type="reset" class="btn-reset">रीसेट / Reset</button>
                        <button type="button" class="btn-cancel" onclick="window.location.href='../Main/index.html'">रद्द करा / Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="../assets/js/death_form.js"></script>
</body>
</html>