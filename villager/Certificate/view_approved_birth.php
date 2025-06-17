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

// Add this function before fetching records
function generateRegistrationNumber($id) {
    $year = date('Y');
    $random = str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
    return "BRTH-$year-$random-$id";
}

// Modify the SQL query to generate numbers for unregistered records
$sql = "UPDATE approved_birth_certificates 
        SET registration_number = CASE 
            WHEN registration_number IS NULL THEN CONCAT('BRTH-', YEAR(NOW()), '-', LPAD(FLOOR(RAND() * 100000), 5, '0'), '-', id)
            ELSE registration_number
        END";
$conn->query($sql);

// Then fetch all approved records
$sql = "SELECT * FROM approved_birth_certificates ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approved Birth Certificate Records</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/approved_birth.css">
    <style>
        .dashboard-btn {
            background-color: #1e3a8a;
            color: white;
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 20px;
        }

        .dashboard-btn:hover {
            background-color: #1e40af;
            color: white;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="page-title">Approved Birth Certificate Records</h1>
            <a href="../../Admin/Clerk/clerk_dashboard.php" class="dashboard-btn">
                <i class="bi bi-arrow-left"></i> Back to Dashboard
            </a>
        </div>

        <div class="table-responsive">
            <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Gender</th>
                    <th>Date of Birth</th>
                    <th>Mother's Name</th>
                    <th>Father's Name</th>
                    <th>Address</th>
                    <th>Registration Number</th>
                    <th>Registration Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php 
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>{$row['id']}</td>
                            <td>{$row['name']}</td>
                            <td>{$row['gender']}</td>
                            <td>" . date('d-m-Y', strtotime($row['dob'])) . "</td>
                            <td>{$row['mother_name']}</td>
                            <td>{$row['father_name']}</td>
                            <td>{$row['address']}</td>
                            <td id='reg_num_{$row['id']}'>" . 
                                ($row['registration_number'] ? $row['registration_number'] : 
                                "<button class='btn btn-sm btn-primary' onclick='generateRegistrationNumber({$row['id']})'>Generate Registration Number</button>") . 
                            "</td>
                            <td>" . date('d-m-Y', strtotime($row['registration_date'])) . "</td>
                            <td>
                                <button class='action-btn generate-btn' onclick='generateCertificate({$row['id']})'>
                                    Generate Certificate
                                </button>
                            </td>
                          </tr>";
                }
            } else {
                echo '<tr><td colspan="10" style="text-align: center;">No approved records found</td></tr>';
            }
            $conn->close();
            ?>
            </tbody>
        </table>
    </div>
</div>
    <script>
    function generateCertificate(id) {
        // Open birth_certificate_template.php in a new window/tab with the record ID
        window.open('birth_certificate_template.php?id=' + id, '_blank');
    }
    </script>
</body>
</html>