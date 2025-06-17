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
$id = isset($_GET['id']) ? intval($_GET['id']) : null;

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
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
        }
        
        .certificate {
            width: 100%;
            height: 100%;
            box-sizing: border-box;
            padding: 15mm;
            border: 4px solid #1a237e;
            position: relative;
            background: white;
            /* Removed background circle pattern */
        }
        
        /* Watermark removed */
        
        .certificate-header {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 10mm;
            position: relative;
            z-index: 1;
        }
        
        .emblem {
            width: 18mm;
            height: auto;
            margin-right: 5mm;
        }
        
        .header-text {
            text-align: center;
        }
        
        .header-text h2 {
            margin: 0;
            font-size: 18px;
            color: #1a237e;
            font-weight: 600;
        }
        
        .header-text h3 {
            margin: 5px 0 0 0;
            font-size: 16px;
            color: #1a237e;
            font-weight: 600;
        }
        
        .certificate-title {
            text-align: center;
            color: #1a237e;
            margin: 8mm 0;
            font-size: 24px;
            border-bottom: 2px solid #1a237e;
            padding-bottom: 5mm;
            position: relative;
            z-index: 1;
        }
        
        .legal-text {
            font-size: 10px;
            margin-bottom: 8mm;
            text-align: center;
            color: #444;
            position: relative;
            z-index: 1;
        }
        
        .details-grid {
            display: grid;
            gap: 2mm;
            margin: 8mm 0;
            position: relative;
            z-index: 1;
            border: 1px solid #e0e0e0;
            border-radius: 4px;
            overflow: hidden;
        }
        
        .detail-row {
            display: grid;
            grid-template-columns: 45mm 1fr;
            gap: 5mm;
            padding: 3mm 4mm;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .detail-row:nth-child(odd) {
            background-color: #f8f9ff;
        }
        
        .detail-row:last-child {
            border-bottom: none;
        }
        
        .label {
            font-weight: 600;
            color: #333;
        }
        
        .value {
            font-weight: 500;
        }
        
        .certificate-footer {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-top: 15mm;
            position: relative;
            z-index: 1;
        }
        
        .qr-code {
            border: 1px solid #e0e0e0;
            padding: 2mm;
            background: white;
        }
        
        .qr-code img {
            width: 25mm;
            height: 25mm;
        }
        
        .signature {
            text-align: right;
            display: flex;
            flex-direction: column;
            align-items: flex-end;
        }
        
        .signature img {
            max-width: 35mm;
            height: auto;
            margin-bottom: 3mm;
        }
        
        .signature p {
            margin: 1mm 0;
            font-size: 14px;
            font-weight: 600;
            color: #1a237e;
        }
        
        .signature .designation {
            font-size: 12px;
            color: #333;
            font-weight: 500;
        }
        
        .signature-line {
            width: 40mm;
            border-top: 1px solid #1a237e;
            margin-bottom: 2mm;
        }
        
        .issuance-date {
            font-size: 11px;
            color: #666;
            position: absolute;
            bottom: 8mm;
            left: 15mm;
            font-style: italic;
        }
        
        .button-container {
            display: flex;
            justify-content: center;
            gap: 10mm;
            margin: 10mm 0;
        }
        
        .print-btn, .back-btn {
            padding: 10px 20px;
            background: #1a237e;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 500;
            transition: background 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .print-btn:hover, .back-btn:hover {
            background: #0e1358;
        }
        
        .serial-number {
            position: absolute;
            top: 10mm;
            right: 10mm;
            font-size: 11px;
            color: #666;
            font-weight: 500;
        }
        
        .verification-text {
            font-size: 9px;
            text-align: center;
            color: #666;
            margin-top: 5mm;
            font-style: italic;
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
            <div class="certificate-section" id="certificateSection">
                <div class="certificate" id="certificate">
                    <!-- Watermark div removed -->
                    
                    <div class="serial-number">Certificate Number: <?php echo htmlspecialchars($record['registration_number']); ?></div>
                    
                    <div class="certificate-header">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/5/55/Emblem_of_India.svg" alt="Emblem" class="emblem">
                        <div class="header-text">
                            <h2>महाराष्ट्र शासन / GOVERNMENT OF MAHARASHTRA</h2>
                            <h3>सार्वजनिक आरोग्य विभाग / DEPARTMENT OF PUBLIC HEALTH</h3>
                        </div>
                    </div>

                    <h1 class="certificate-title">जन्म प्रमाणपत्र / Birth Certificate</h1>

                    <div class="certificate-content">
                        <p class="legal-text">
                            (ISSUED UNDER SECTION 12/17 OF THE REGISTRATION OF BIRTHS AND DEATHS ACT, 1969 AND RULE 8/13 OF THE
                            MAHARASHTRA REGISTRATION OF BIRTHS AND DEATHS RULES 2000)
                        </p>

                        <div class="details-grid">
                            <div class="detail-row">
                                <span class="label">Name:</span>
                                <span class="value"><?php echo htmlspecialchars($record['name']); ?></span>
                            </div>
                            <div class="detail-row">
                                <span class="label">Gender:</span>
                                <span class="value"><?php echo htmlspecialchars($record['gender']); ?></span>
                            </div>
                            <div class="detail-row">
                                <span class="label">Date of Birth:</span>
                                <span class="value"><?php echo date('d-m-Y', strtotime($record['dob'])); ?></span>
                            </div>
                            <div class="detail-row">
                                <span class="label">Place of Birth:</span>
                                <span class="value"><?php echo htmlspecialchars($record['address']); ?></span>
                            </div>
                            <div class="detail-row">
                                <span class="label">Mother's Name:</span>
                                <span class="value"><?php echo htmlspecialchars($record['mother_name']); ?></span>
                            </div>
                           
                            <div class="detail-row">
                                <span class="label">Father's Name:</span>
                                <span class="value"><?php echo htmlspecialchars($record['father_name']); ?></span>
                            </div>

                            <div class="detail-row">
                                <span class="label">Address:</span>
                                <span class="value"><?php echo htmlspecialchars($record['address']); ?></span>
                            </div>                            
                            <div class="detail-row">
                                <span class="label">Registration Date:</span>
                                <span class="value"><?php echo date('d-m-Y', strtotime($record['registration_date'])); ?></span>
                            </div>
                        </div>

                        <div class="certificate-footer">
                            <div class="qr-code">
                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=<?php 
                                    $qrData = "Registration No: " . $record['registration_number'] . "\n" .
                                             "Name: " . $record['name'] . "\n" .
                                             "Date of Birth: " . date('d-m-Y', strtotime($record['dob']));
                                    echo urlencode($qrData);
                                ?>" alt="QR Code">
                                <div class="verification-text">Scan to verify authenticity</div>
                            </div>
                            <div class="signature">
                                <img src="sig.png" alt="Registrar Signature">
                                <div class="signature-line"></div>
                                <p>Registrar</p>
                                <p class="designation">(BIRTH)</p>
                            </div>
                        </div>
                        
                        <div class="issuance-date">
                            Date of Issue: <?php echo date('d-m-Y'); ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="button-container">
                <button class="print-btn" onclick="window.print()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="6 9 6 2 18 2 18 9"></polyline>
                        <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                        <rect x="6" y="14" width="12" height="8"></rect>
                    </svg>
                    Print Certificate
                </button>
                <button class="back-btn" onclick="window.close()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M19 12H5M12 19l-7-7 7-7"></path>
                    </svg>
                    Close
                </button>
            </div>
        </div>
    </main>

    <script>
        function maskAadhaar(aadhaar) {
            return aadhaar ? 'XXXX-XXXX-' + aadhaar.slice(-4) : '';
        }
        
        document.addEventListener('DOMContentLoaded', function() {
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