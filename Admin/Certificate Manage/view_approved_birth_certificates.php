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
    <style>
        /* Main Styles for Birth Certificate Records */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Poppins', sans-serif;
}

body {
    background-color: #f0f4f8;
    color: #333;
    line-height: 1.6;
}

.container {
    max-width: 1200px;
    margin: 30px auto;
    padding: 20px;
}

/* Header Section */
.navbar {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}

.navbar-brand {
    font-weight: 600;
}

.page-title {
    color: #1e3a8a;
    font-size: 28px;
    font-weight: 600;
    margin-bottom: 25px;
    padding-bottom: 15px;
    border-bottom: 2px solid #e5e7eb;
}

/* Records Table */
.table {
    width: 100%;
    background-color: #fff;
    border-radius: 12px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
    border-collapse: separate;
    border-spacing: 0;
    overflow: hidden;
    margin-bottom: 30px;
}

.table thead th {
    background-color: #1e3a8a;
    color: white;
    padding: 15px;
    text-align: left;
    font-weight: 500;
    border: none;
}

.table tbody tr:nth-child(even) {
    background-color: #f9fafb;
}

.table tbody td {
    padding: 15px;
    border-bottom: 1px solid #e5e7eb;
    border-top: none;
}

.table tbody tr:hover {
    background-color: #f3f4f6;
}

/* Action Buttons */
.action-btn {
    padding: 8px 16px;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 500;
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
    margin-right: 8px;
}

.view-btn {
    background-color: #dbeafe;
    color: #1e40af;
}

.view-btn:hover {
    background-color: #bfdbfe;
}

.print-btn {
    background-color: #e0e7ff;
    color: #4338ca;
}

.print-btn:hover {
    background-color: #c7d2fe;
}

.download-btn {
    background-color: #d1fae5;
    color: #047857;
}

.download-btn:hover {
    background-color: #a7f3d0;
}

/* Modal Styles */
.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 1000;
    justify-content: center;
    align-items: center;
}

.modal-content {
    background-color: white;
    padding: 30px;
    border-radius: 12px;
    width: 90%;
    max-width: 800px;
    box-shadow: 0 5px 30px rgba(0, 0, 0, 0.2);
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid #e5e7eb;
    padding-bottom: 15px;
    margin-bottom: 20px;
}

.modal-title {
    color: #1e3a8a;
    font-size: 24px;
    font-weight: 600;
}

.close-btn {
    background: none;
    border: none;
    font-size: 24px;
    cursor: pointer;
    color: #6b7280;
}

.modal-body {
    margin-bottom: 20px;
}

.certificate-details {
    margin-bottom: 20px;
}

.certificate-details .row {
    margin-bottom: 10px;
}

.detail-label {
    font-weight: 600;
    color: #374151;
}

/* Certificate Preview */
.certificate-preview {
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 20px;
    background-color: #f9fafb;
    margin-bottom: 20px;
}

.certificate-preview img {
    max-width: 100%;
    border-radius: 4px;
}

.certificate-document {
    margin-top: 20px;
    padding: 30px;
    background-color: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    position: relative;
}

.certificate-document::before {
    content: '';
    position: absolute;
    top: 10px;
    left: 10px;
    right: 10px;
    bottom: 10px;
    border: 2px solid #1e3a8a;
    border-radius: 4px;
    pointer-events: none;
}

.certificate-header {
    text-align: center;
    margin-bottom: 30px;
}

.certificate-header h2 {
    color: #1e3a8a;
    font-size: 28px;
    margin-bottom: 10px;
}

.certificate-body {
    margin-bottom: 30px;
}

.certificate-footer {
    display: flex;
    justify-content: space-between;
    margin-top: 40px;
}

.signature-box {
    text-align: center;
    flex: 0 0 45%;
}

.signature-line {
    border-top: 1px solid #000;
    margin-bottom: 5px;
    width: 80%;
    margin-left: auto;
    margin-right: auto;
}

/* Search and Filter Section */
.filter-section {
    background-color: #fff;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
    margin-bottom: 30px;
}

.filter-form .row {
    align-items: flex-end;
}

.form-control {
    border-radius: 8px;
    padding: 10px 15px;
    border: 1px solid #d1d5db;
}

.search-btn {
    background-color: #1e3a8a;
    color: white;
    border: none;
    border-radius: 8px;
    padding: 10px 20px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
}

.search-btn:hover {
    background-color: #1e40af;
}

.reset-btn {
    background-color: #e5e7eb;
    color: #4b5563;
    border: none;
    border-radius: 8px;
    padding: 10px 20px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
}

.reset-btn:hover {
    background-color: #d1d5db;
}

/* Pagination */
.pagination {
    display: flex;
    justify-content: center;
    margin-top: 30px;
}

.pagination .page-item .page-link {
    color: #1e3a8a;
    padding: 10px 15px;
    margin: 0 5px;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
}

.pagination .page-item.active .page-link {
    background-color: #1e3a8a;
    color: white;
    border-color: #1e3a8a;
}

.pagination .page-item .page-link:hover {
    background-color: #dbeafe;
}

/* Alerts */
.alert {
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 25px;
    font-weight: 500;
}

.alert-info {
    background-color: #dbeafe;
    color: #1e40af;
    border-left: 4px solid #3b82f6;
}

.alert-success {
    background-color: #ecfdf5;
    color: #047857;
    border-left: 4px solid #10b981;
}

/* Responsive adjustments */
@media (max-width: 1024px) {
    .container {
        padding: 15px;
    }
    .table {
        display: block;
        overflow-x: auto;
    }
    .certificate-footer {
        flex-direction: column;
        gap: 20px;
    }
    .signature-box {
        flex: 0 0 100%;
    }
}

@media (max-width: 640px) {
    .page-title {
        font-size: 24px;
    }
    .action-btn {
        padding: 6px 12px;
        font-size: 12px;
        margin-bottom: 5px;
        display: block;
    }
    .modal-content {
        padding: 20px;
    }
    .certificate-document {
        padding: 15px;
    }
    .filter-form .row {
        margin-bottom: 10px;
    }
}
/* Action Buttons */
.action-btn {
    padding: 8px 16px;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 500;
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
    margin-right: 8px;
}
.generate-btn {
    background-color: #047857;
    color: white;
}
.generate-btn:hover {
    background-color: #065f46;
}
.generate-btn:disabled {
    background-color: #9ca3af;
    cursor: not-allowed;
    opacity: 0.7;
}
.btn-sm {
    padding: 6px 12px;
    font-size: 12px;
}
.btn-primary {
    background-color: #1e40af;
    color: white;
}
.btn-primary:hover {
    background-color: #1e3a8a;
}
/* Records Table */
.table-responsive {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    margin-bottom: 30px;
}
.table {
    width: 100%;
    min-width: 1000px; /* Ensures table doesn't get too squeezed */
    background-color: #fff;
    border-radius: 12px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
    border-collapse: separate;
    border-spacing: 0;
}
/* Mobile optimizations */
@media (max-width: 768px) {
    .table-responsive {
        margin: 0 -15px; /* Negative margin to allow full-width scrolling */
        padding: 0 15px;
        width: calc(100% + 30px);
    }
    .table td, .table th {
        white-space: nowrap; /* Prevents text wrapping */
        padding: 12px 8px; /* Slightly reduced padding */
    }
}
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Certificate Management</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="view_birth_certificates.php">View Birth Records</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="view_death_certificates.php">View Death Records</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="view_marriage_certificates.php">View Marriage Records</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="view_approved_birth_certificates.php">View Approved Birth Records</a>
                    </li>
                </ul>
                <div class="d-flex">
                    <a href="../Login/logout.php" class="btn btn-outline-light">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container">
    <h1 class="page-title">Approved Birth Certificate Records</h1>

    <div class="table-responsive">
        <table class="table table-striped">
        <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Gender</th>
                    <th>Date of Birth</th>
                    <th>Mother's Name</th>
                    <th>Mother's Aadhaar</th>
                    <th>Father's Name</th>
                    <th>Father's Aadhaar</th>
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
                            <td>{$row['mother_aadhaar']}</td>
                            <td>{$row['father_name']}</td>
                            <td>{$row['father_aadhaar']}</td>
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
                echo '<tr><td colspan="12" style="text-align: center;">No approved records found</td></tr>';
            }
            $conn->close();
            ?>
            </tbody>
        </table>
    </div>
</div>
</body>
    <script>
    function generateCertificate(id) {
        // Open birth_certificate_template.php in a new window/tab with the record ID
        window.open('birth_certificate_template.php?id=' + id, '_blank');
    }
    </script>
</body>
</html>