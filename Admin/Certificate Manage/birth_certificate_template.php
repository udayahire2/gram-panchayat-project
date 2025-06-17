<?php
// Add this at the top of the file
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
$sql = "SELECT * FROM approved_birth_certificates WHERE id = ?";
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
    <title>जन्म प्रमाणपत्र / Birth Certificate</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            background: #f5f5f5;
        }
        
        /* A4 Size Settings */
        @page {
            size: A4;
            margin: 0;
        }
        
        /* Certificate Styles */
        .certificate-section {
            background: white;
            width: 210mm;
            height: 297mm;
            margin: 0 auto;
            box-sizing: border-box;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .certificate {
            width: 100%;
            height: 100%;
            box-sizing: border-box;
            padding: 20mm;
            border: 2px solid #1a237e;
            position: relative;
            background: white;
        }
        
        .certificate-header {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15mm;
        }
        
        .emblem {
            width: 20mm;
            height: auto;
            margin-right: 5mm;
        }
        
        .header-text {
            text-align: center;
        }
        
        .header-text h2 {
            margin: 0;
            font-size: 18px;
        }
        
        .header-text h3 {
            margin: 5px 0 0 0;
            font-size: 16px;
        }
        
        .certificate-title {
            text-align: center;
            color: #1a237e;
            margin: 10mm 0;
            font-size: 20px;
            border-bottom: 2px solid #1a237e;
            padding-bottom: 5mm;
        }
        
        .legal-text {
            font-size: 10px;
            margin-bottom: 10mm;
            text-align: center;
            color: #666;
        }
        
        .details-grid {
            display: grid;
            gap: 2mm;
            margin: 10mm 0;
        }
        
        .detail-row {
            display: grid;
            grid-template-columns: 40mm 1fr;
            gap: 5mm;
            padding: 2mm;
            border-bottom: 1px solid #eee;
        }
        
        .label {
            font-weight: bold;
            color: #333;
        }
        
        .certificate-footer {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-top: 20mm;
        }
        
        .qr-code img {
            width: 25mm;
            height: 25mm;
        }
        
        .signature {
            text-align: right;
        }
        
        .signature img {
            max-width: 40mm;
            height: auto;
        }
        
        .signature p {
            margin: 2mm 0;
            font-size: 14px;
        }
        
        .button-container {
            display: flex;
            justify-content: center;
            gap: 10mm;
            margin: 10mm 0;
        }
        
        .print-btn, .back-btn {
            padding: 8px 16px;
            background: #1a237e;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        /* Print Styles */
        @media print {
            body * {
                visibility: hidden;
            }
            .certificate-section, .certificate-section * {
                visibility: visible;
            }
            .certificate-section {
                position: absolute;
                left: 0;
                top: 0;
                margin: 0;
                padding: 0;
                box-shadow: none;
            }
            .print-btn, .back-btn, .button-container {
                display: none;
            }
        }
        
        /* Screen Display */
        @media screen {
            .certificate-section {
                margin: 20px auto;
            }
        }
    </style>
</head>
<body>
    <main>
        <div class="container">
            <!-- Certificate Section -->
            <div class="certificate-section" id="certificateSection">
                <div class="certificate" id="certificate">
                    <div class="certificate-header">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/5/55/Emblem_of_India.svg" alt="Emblem" class="emblem">
                        <div class="header-text">
                            <h2>महाराष्ट्र शासन / GOVERNMENT OF MAHARASHTRA</h2>
                            <h3>सार्वजनिक आरोग्य विभाग / DEPARTMENT OF PUBLIC HEALTH</h3>
                        </div>
                    </div>

                    <h1 class="certificate-title">जन्म प्रमाणपत्र / BIRTH CERTIFICATE</h1>

                    <div class="certificate-content">
                        <p class="legal-text">
                            (ISSUED UNDER SECTION 12/17 OF THE REGISTRATION OF BIRTHS AND DEATHS ACT, 1969 AND RULE 8/13 OF THE
                            MAHARASHTRA REGISTRATION OF BIRTHS AND DEATHS RULES 2000)
                        </p>

                        <div class="details-grid">
                            <div class="detail-row">
                                <span class="label">Name:</span>
                                <span class="value" id="certName"><?php echo htmlspecialchars($record['name']); ?></span>
                            </div>
                            <div class="detail-row">
                                <span class="label">Gender:</span>
                                <span class="value" id="certGender"><?php echo htmlspecialchars($record['gender']); ?></span>
                            </div>
                            <div class="detail-row">
                                <span class="label">Date of Birth:</span>
                                <span class="value" id="certDob"><?php echo date('d-m-Y', strtotime($record['dob'])); ?></span>
                            </div>
                            <div class="detail-row">
                                <span class="label">Place of Birth:</span>
                                <span class="value" id="certPlaceOfBirth"><?php echo htmlspecialchars($record['address']); ?></span>
                            </div>
                            <div class="detail-row">
                                <span class="label">Mother's Name:</span>
                                <span class="value" id="certMotherName"><?php echo htmlspecialchars($record['mother_name']); ?></span>
                            </div>
                            <div class="detail-row">
                                <span class="label">Mother's Aadhaar:</span>
                                <span class="value" id="certMotherAadhaar"><?php echo htmlspecialchars($record['mother_aadhaar']); ?></span>
                            </div>
                            <div class="detail-row">
                                <span class="label">Father's Name:</span>
                                <span class="value" id="certFatherName"><?php echo htmlspecialchars($record['father_name']); ?></span>
                            </div>
                            <div class="detail-row">
                                <span class="label">Father's Aadhaar:</span>
                                <span class="value" id="certFatherAadhaar"><?php echo htmlspecialchars($record['father_aadhaar']); ?></span>
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
                                   'name' => $record['name'],
                                   'dob' => $record['dob'],
                                   'registration_number' => $record['registration_number'],
                                   "approval" => 'certificate approved by: Gram Panchayat Clerk'
                               ])); ?>" alt="QR Code">
                                
                            </div>
                            <div class="signature">
                                <img src="images/signature.jpg" alt="Registrar Signature" style="max-width: 40mm; height: auto;">
                                <p>Registrar (BIRTH & DEATH)</p>
                                <p></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Buttons outside the certificate section for better control -->
            <div class="button-container">
                <button class="print-btn" onclick="printCertificate()">Print Certificate</button>
                <button class="back-btn" onclick="goBack()">Close</button>
            </div>
        </div>
    </main>
    
    <script>
        function maskAadhaar(aadhaar) {
            return aadhaar ? 'XXXX-XXXX-' + aadhaar.slice(-4) : '';
        }
        
        function printCertificate() {
            window.print();
        }
        
        function goBack() {
            window.close();
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            // Mask Aadhaar numbers for privacy
            const motherAadhaar = document.getElementById('certMotherAadhaar');
            const fatherAadhaar = document.getElementById('certFatherAadhaar');
            
            if (motherAadhaar && motherAadhaar.textContent) {
                motherAadhaar.textContent = maskAadhaar(motherAadhaar.textContent);
            }
            
            if (fatherAadhaar && fatherAadhaar.textContent) {
                fatherAadhaar.textContent = maskAadhaar(fatherAadhaar.textContent);
            }
        });
    </script>
</body>
</html>