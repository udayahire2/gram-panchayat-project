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

if (!isset($_GET['data'])) {
    die("No certificate data provided");
}

try {
    $decodedData = json_decode(base64_decode($_GET['data']), true);
    if (!$decodedData) {
        throw new Exception("Invalid certificate data");
    }

    // Verify certificate in database
    $sql = "SELECT * FROM death_certificate WHERE 
            certificate_number = ? AND 
            name_of_deceased = ? AND 
            status = 'APPROVED'";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", 
        $decodedData['certificate_number'], 
        $decodedData['name']
    );
    $stmt->execute();
    $result = $stmt->get_result();
    
    $isValid = $result->num_rows > 0;
    
} catch (Exception $e) {
    die("Error: Invalid certificate data");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Death Certificate</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #1a237e;
            margin: 0;
            font-size: 24px;
        }
        .verification-status {
            text-align: center;
            padding: 15px;
            background: #ecfdf5;
            color: #047857;
            border-radius: 6px;
            margin-bottom: 30px;
        }
        .certificate-details {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .certificate-details th,
        .certificate-details td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }
        .certificate-details th {
            background-color: #f8fafc;
            color: #1e293b;
            font-weight: 500;
            width: 40%;
        }
        .certificate-details tr:last-child th,
        .certificate-details tr:last-child td {
            border-bottom: none;
        }
        @media (max-width: 640px) {
            .container {
                padding: 15px;
            }
            .certificate-details th,
            .certificate-details td {
                padding: 8px 10px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Death Certificate Verification</h1>
        </div>
        
        <div class="verification-status">
            ✓ This is a valid death certificate issued by the Government of Maharashtra
        </div>

        <table class="certificate-details">
            <tr>
                <th>Name of Deceased</th>
                <td><?php echo htmlspecialchars($decodedData['name']); ?></td>
            </tr>
            <tr>
                <th>Gender</th>
                <td><?php echo htmlspecialchars($decodedData['gender']); ?></td>
            </tr>
            <tr>
                <th>Age</th>
                <td><?php echo htmlspecialchars($decodedData['age']); ?></td>
            </tr>
            <tr>
                <th>Aadhaar Number</th>
                <td><?php echo htmlspecialchars($decodedData['aadhaar']); ?></td>
            </tr>
            <tr>
                <th>Date of Death</th>
                <td><?php echo date('d-m-Y', strtotime($decodedData['date_of_death'])); ?></td>
            </tr>
            <tr>
                <th>Place of Death</th>
                <td><?php echo htmlspecialchars($decodedData['place_of_death']); ?></td>
            </tr>
            <tr>
                <th>Address</th>
                <td><?php echo htmlspecialchars($decodedData['address']); ?></td>
            </tr>
            <tr>
                <th>Certificate Number</th>
                <td><?php echo htmlspecialchars($decodedData['certificate_number']); ?></td>
            </tr>
            <tr>
                <th>Registration Date</th>
                <td><?php echo date('d-m-Y', strtotime($decodedData['registration_date'])); ?></td>
            </tr>
        </table>
    </div>
</body>
</html>