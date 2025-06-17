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
    die("Connection failed: " . $conn->connect_error);
}

// Get the record ID from URL
$id = isset($_GET['id']) ? $_GET['id'] : null;

if (!$id) {
    die("No record ID provided");
}

// Fetch the record
$sql = "SELECT * FROM approved_death_certificates WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$record = $result->fetch_assoc();

if (!$record) {
    die("Record not found");
}

$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>मृत्यू प्रमाणपत्र / Death Certificate</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* Same CSS styles as birth_certificate_template.php */
        body {
            font-family: 'Poppins', sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            background: #f5f5f5;
        }
        
        @page {
            size: A4;
            margin: 0;
        }
        
        .certificate-section {
            background: white;
            width: 210mm;
            height: 297mm;
            margin: 0 auto;
            box-sizing: border-box;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        /* Include all the same CSS styles from birth_certificate_template.php */
        /* ... */
    </style>
</head>
<body>
    <main>
        <div class="container">
            <div class="certificate-section" id="certificateSection">
                <div class="certificate" id="certificate">
                    <div class="certificate-header">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/5/55/Emblem_of_India.svg" alt="Emblem" class="emblem">
                        <div class="header-text">
                            <h2>महाराष्ट्र शासन / GOVERNMENT OF MAHARASHTRA</h2>
                            <h3>सार्वजनिक आरोग्य विभाग / DEPARTMENT OF PUBLIC HEALTH</h3>
                        </div>
                    </div>

                    <h1 class="certificate-title">मृत्यू प्रमाणपत्र / DEATH CERTIFICATE</h1>

                    <div class="certificate-content">
                        <p class="legal-text">
                            (ISSUED UNDER SECTION 12/17 OF THE REGISTRATION OF BIRTHS AND DEATHS ACT, 1969 AND RULE 8/13 OF THE
                            MAHARASHTRA REGISTRATION OF BIRTHS AND DEATHS RULES 2000)
                        </p>

                        <div class="details-grid">
                            <div class="detail-row">
                                <span class="label">Name of Deceased:</span>
                                <span class="value" id="certName"><?php echo htmlspecialchars($record['name_of_deceased']); ?></span>
                            </div>
                            <div class="detail-row">
                                <span class="label">Gender:</span>
                                <span class="value" id="certGender"><?php echo htmlspecialchars($record['gender']); ?></span>
                            </div>
                            <div class="detail-row">
                                <span class="label">Date of Death:</span>
                                <span class="value" id="certDateOfDeath"><?php echo date('d-m-Y', strtotime($record['date_of_death'])); ?></span>
                            </div>
                            <div class="detail-row">
                                <span class="label">Place of Death:</span>
                                <span class="value" id="certPlaceOfDeath"><?php echo htmlspecialchars($record['place_of_death']); ?></span>
                            </div>
                            <div class="detail-row">
                                <span class="label">Address:</span>
                                <span class="value" id="certAddress"><?php echo htmlspecialchars($record['address']); ?></span>
                            </div>
                            <div class="detail-row">
                                <span class="label">Registration Number:</span>
                                <span class="value" id="certRegistrationNumber"><?php echo htmlspecialchars($record['registration_number']); ?></span>
                            </div>
                            <div class="detail-row">
                                <span class="label">Registration Date:</span>
                                <span class="value" id="certRegistrationDate"><?php echo date('d-m-Y', strtotime($record['registration_date'])); ?></span>
                            </div>
                        </div>

                        <div class="certificate-footer">
                            <div class="qr-code">
                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=<?php echo urlencode(json_encode([
                                    'name' => $record['name_of_deceased'],
                                    'date_of_death' => $record['date_of_death'],
                                    'registration_number' => $record['registration_number']
                                ])); ?>" alt="QR Code">
                            </div>
                            <div class="signature">
                                <img src="images/signature.jpg" alt="Registrar Signature">
                                <p>Registrar (BIRTH & DEATH)</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="button-container">
                <button class="print-btn" onclick="printCertificate()">Print Certificate</button>
                <button class="back-btn" onclick="goBack()">Close</button>
            </div>
        </div>
    </main>
    
    <script>
        function printCertificate() {
            window.print();
        }
        
        function goBack() {
            window.close();
        }
    </script>
</body>
</html>