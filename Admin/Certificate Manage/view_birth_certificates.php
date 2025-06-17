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
// Process status update if submitted
if (isset($_POST['update_status'])) {
    $record_id = $_POST['record_id'];
    $new_status = $_POST['new_status'];
    $remarks = $_POST['remarks'] ?? '';
    
    // First, get the birth certificate data
    $fetchSql = "SELECT * FROM birth_certificate WHERE id = ?";
    $fetchStmt = $conn->prepare($fetchSql);
    $fetchStmt->bind_param("i", $record_id);
    $fetchStmt->execute();
    $birthRecord = $fetchStmt->get_result()->fetch_assoc();
    $fetchStmt->close();
    
    // Update the status with current timestamp
    $updateSql = "UPDATE birth_certificate SET 
                  status = ?, 
                  remarks = ?, 
                  updated_at = CURRENT_TIMESTAMP 
                  WHERE id = ?";
    
    $stmt = $conn->prepare($updateSql);
    $stmt->bind_param("ssi", $new_status, $remarks, $record_id);
    
    if ($stmt->execute()) {
        // If status is APPROVED, insert into approved_birth_certificates
       // If status is APPROVED, insert into approved_birth_certificates
if ($new_status === 'APPROVED') {
    $insertSql = "INSERT INTO approved_birth_certificates (
        name, 
        dob, 
        mother_name, 
        father_name, 
        registration_date,
        gender,
        mother_aadhaar,
        father_aadhaar,
        address,
        registration_number
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $insertStmt = $conn->prepare($insertSql);
    $insertStmt->bind_param("ssssssssss", 
        $birthRecord['name'],
        $birthRecord['dob'],
        $birthRecord['mother_name'],
        $birthRecord['father_name'],
        $birthRecord['registration_date'],
        $birthRecord['gender'],
        $birthRecord['mother_aadhaar'],
        $birthRecord['father_aadhaar'],
        $birthRecord['address'],
        $birthRecord['registration_number']
    );
    $insertStmt->execute();
    $insertStmt->close();
    $statusMsg = "Status updated and record approved successfully!";
}
        else {
            $statusMsg = "Status updated successfully!";
        }
    } else {
        $errorMsg = "Error updating status: " . $stmt->error;
    }
    $stmt->close();
}


// Fetch all records
$sql = "SELECT * FROM birth_certificate ORDER BY created_at DESC";
$result = $conn->query($sql);


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Birth Certificate Records</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    
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
                        <a class="nav-link active" href="view_birth_certificates.php">View Birth Records</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="view_death_certificates.php">View Death Records</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="view_marriage_certificates.php">View Marriage Records</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="view_approved_birth_certificates.php">View Approved Birth Records</a>
                    </li>
                </ul>
                <div class="d-flex">
                    <a href="/CPP WEB/villager/Main/index.html" class="btn btn-outline-light">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="header">
            <h1 class="page-title">Birth Certificate Records</h1>
        </div>

        <?php if (isset($statusMsg)): ?>
            <div class="alert alert-success">
                <?php echo $statusMsg; ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($errorMsg)): ?>
            <div class="alert alert-danger">
                <?php echo $errorMsg; ?>
            </div>
        <?php endif; ?>

        <table class="records-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Date of Birth</th>
                    <th>Parents</th>
                    <th>Registration Date</th>
                    <th>Status</th>
                    <th>Documents</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        $status = $row['status'] ?? 'PENDING';
                        $statusClass = '';
                        switch($status) {
                            case 'APPROVED':
                                $statusClass = 'status-approved';
                                break;
                            case 'REJECTED':
                                $statusClass = 'status-rejected';
                                break;
                            default:
                                $statusClass = 'status-pending';
                                break;
                        }
                ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo date('d-m-Y', strtotime($row['dob'])); ?></td>
                    <td>
                        <div>Mother: <?php echo htmlspecialchars($row['mother_name']); ?></div>
                        <div>Father: <?php echo htmlspecialchars($row['father_name']); ?></div>
                    </td>
                    <td><?php echo date('d-m-Y', strtotime($row['registration_date'])); ?></td>
                    <td><span class="status-badge <?php echo $statusClass; ?>"><?php echo $status; ?></span></td>
                    <td>
                        <?php if(!empty($row['proof_of_birth_file'])): ?>
                        <div class="document-preview">
                            <a href="#" class="preview-link" onclick="viewDocument('/CPP WEB/villager/uploads/birth_certificate<?php echo $row['proof_of_birth_file']; ?>', 'Birth Proof')">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                Birth Proof
                            </a>
                        </div>
                        <?php endif; ?>
                        <?php if (!empty($row['mother_aadhaar_proof'])): ?>
                                    <div class="document-preview">
                                        <a href="#" class="preview-link"
                                            onclick="viewDocument('/CPP WEB/villager/uploads/birth_certificate<?php echo $row['mother_aadhaar_proof']; ?>', 'Mother Aadhaar')">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            Mother Aadhaar
                                        </a>
                                    </div>
                                <?php endif; ?>

                                <?php if (!empty($row['father_aadhaar_proof'])): ?>
                                    <div class="document-preview">
                                        <a href="#" class="preview-link"
                                            onclick="viewDocument('/CPP WEB/villager/uploads/birth_certificate<?php echo $row['father_aadhaar_proof']; ?>', 'Father Aadhaar')">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            Father Aadhaar
                                        </a>
                                    </div>
                                <?php endif; ?>

                                <?php if (!empty($row['marriage_certificate'])): ?>
                                    <div class="document-preview">
                                        <a href="#" class="preview-link"
                                            onclick="viewDocument('/CPP WEB/villager/uploads/birth_certificate<?php echo $row['marriage_certificate']; ?>', 'Marriage Certificate')">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            Marriage Certificate
                                        </a>
                                    </div>
                                <?php endif; ?>

                    </td>
                    <td>
                        <button class="action-btn view-btn" onclick="viewDetails(<?php echo $row['id']; ?>)">View</button>
                        <?php if($status !== 'APPROVED' && $status !== 'REJECTED'): ?>
                        <button class="action-btn approve-btn" onclick="showStatusModal(<?php echo $row['id']; ?>, 'APPROVED')">Approve</button>
                        <button class="action-btn reject-btn" onclick="showStatusModal(<?php echo $row['id']; ?>, 'REJECTED')">Reject</button>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php 
                    }
                } else {
                    echo '<tr><td colspan="8" style="text-align: center;">No records found</td></tr>';
                }
                $conn->close();
                ?>
            </tbody>
        </table>
    </div>

    <!-- Status Update Modal -->
    <div id="statusModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title" id="modalTitle">Update Status</h2>
                <button class="close-btn" onclick="closeModal('statusModal')">×</button>
            </div>
            <form id="statusForm" method="POST" action="">
                <div class="modal-body">
                    <input type="hidden" id="record_id" name="record_id">
                    <input type="hidden" id="new_status" name="new_status">
                    <div class="form-group">
                        <label for="remarks">Remarks / Notes:</label>
                        <textarea id="remarks" name="remarks" placeholder="Enter any additional notes or remarks..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="modal-btn btn-cancel" onclick="closeModal('statusModal')">Cancel</button>
                    <button type="submit" name="update_status" class="modal-btn" id="confirmBtn">Confirm</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Document View Modal -->
    <div id="documentModal" class="modal">
        <div class="modal-content doc-modal-content">
            <div class="modal-header">
                <h2 class="modal-title" id="docModalTitle">Document Preview</h2>
                <button class="close-btn" onclick="closeModal('documentModal')">×</button>
            </div>
            <div class="modal-body">
                <div class="doc-container" id="docContainer"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="modal-btn btn-cancel" onclick="closeModal('documentModal')">Close</button>
            </div>
        </div>
    </div>

    <!-- Details View Modal -->
    <div id="detailsModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Record Details</h2>
                <button class="close-btn" onclick="closeModal('detailsModal')">×</button>
            </div>
            <div class="modal-body" id="detailsContainer"></div>
            <div class="modal-footer">
                <button type="button" class="modal-btn btn-cancel" onclick="closeModal('detailsModal')">Close</button>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="birth.js"></script>
</body>
</html>