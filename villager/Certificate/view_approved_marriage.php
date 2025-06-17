
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

// Fetch approved marriage records
$sql = "SELECT * FROM approved_marriage_db ORDER BY id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approved Marriage Certificate Records</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/approved_marriage.css">
    <style>
        .btn-generate {
            background-color: #047857;
            color: white;
            text-decoration: none;
            padding: 6px 12px;
            border-radius: 4px;
            display: inline-block;
            font-size: 14px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        .btn-generate:hover {
            background-color: #065f46;
            color: white;
        }
        
        .btn-view {
            background-color: #17a2b8;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            text-decoration: none;
            display: inline-block;
            font-size: 14px;
            margin-right: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        .btn-view:hover {
            background-color: #138496;
            color: white;
        }
        
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
            <h1 class="page-title">Approved Marriage Certificate Records</h1>
            <a href="../../Admin/Clerk/clerk_dashboard.php" class="dashboard-btn">
                <i class="bi bi-arrow-left"></i> Back to Dashboard
            </a>
        </div>

        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Husband's Name</th>
                        <th>Wife's Name</th>
                        <th>Marriage Date</th>
                        <th>Place of Marriage</th>
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
                                <td>{$row['husband_name']}</td>
                                <td>{$row['wife_name']}</td>
                                <td>" . date('d-m-Y', strtotime($row['marriage_date'])) . "</td>
                                <td>{$row['marriage_place']}</td>
                                <td id='reg_num_{$row['id']}'>" . 
                                    ($row['registration_number'] ? $row['registration_number'] : 
                                    "<button class='btn btn-sm btn-primary' onclick='generateRegistrationNumber({$row['id']})'>Generate Registration Number</button>") . 
                                "</td>
                                <td>" . date('d-m-Y', strtotime($row['registration_date'])) . "</td>
                                <td>
                                    <a href='generate_marriage.php?id={$row['id']}' class='btn btn-sm btn-generate' target='_blank'>
                                        Generate Certificate
                                    </a>
                                </td>
                              </tr>";
                    }
                } else {
                    echo '<tr><td colspan="8" style="text-align: center;">No approved records found</td></tr>';
                }
                $conn->close();
                ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
<script>
function generateCertificate(id) {
    // Open marriage_certificate_template.php in a new window/tab with the record ID
    window.open('marriage_certificate_template.php?id=' + id, '_blank');
}

function generateRegistrationNumber(id) {
    // AJAX call to generate registration number
    fetch('generate_marriage_registration_number.php?id=' + id)
        .then(response => response.text())
        .then(registrationNumber => {
            document.getElementById('reg_num_' + id).innerHTML = registrationNumber;
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to generate registration number');
        });
}

function viewDetails(id) {
    // Open details view in a modal or new page
    window.open('view_marriage_details.php?id=' + id, '_blank');
}
</script>
</body>
</html>