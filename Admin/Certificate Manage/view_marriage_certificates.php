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

    // Check if remarks column exists
    $checkColumn = $conn->query("SHOW COLUMNS FROM marriage_certificates LIKE 'remarks'");

    if ($checkColumn->num_rows > 0) {
        // If remarks column exists, update both status and remarks
        $updateSql = "UPDATE marriage_certificates SET status = ?, remarks = ?, updated_at = NOW() WHERE id = ?";
        $stmt = $conn->prepare($updateSql);
        $stmt->bind_param("ssi", $new_status, $remarks, $record_id);
    } else {
        // If remarks column doesn't exist, only update status
        $updateSql = "UPDATE marriage_certificates SET status = ?, updated_at = NOW() WHERE id = ?";
        $stmt = $conn->prepare($updateSql);
        $stmt->bind_param("si", $new_status, $record_id);
    }

    if ($stmt->execute()) {
        $statusMsg = "Status updated successfully!";
    } else {
        $errorMsg = "Error updating status: " . $stmt->error;
    }

    $stmt->close();
}

// Fetch all records
$sql = "SELECT * FROM marriage_certificates ORDER BY created_at DESC";  // Changed from marriage_certificates
$result = $conn->query($sql);


?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Marriage Certificate Records</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
</head>
<style>
    /* General Reset and Base Styles */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Poppins', sans-serif;
    line-height: 1.6;
    background-color: #f5f7fa;
    color: #333;
}

/* Container Styles */
.container {
    max-width: 900px;
    margin: 20px auto;
    padding: 0 20px;
}

/* Form Section Styles */
.form-section {
    background: white;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    margin-bottom: 30px;
}

h1 {
    text-align: center;
    color: #1a237e;
    margin-bottom: 30px;
    font-size: 1.8rem;
    font-weight: 600;
}

/* Form Group Styles */
.form-group {
    margin-bottom: 20px;
}

label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
    color: #333;
    font-size: 0.95rem;
}

input, select, textarea {
    width: 100%;
    padding: 12px;
    border: 1px solid #e0e0e0;
    border-radius: 6px;
    font-size: 1rem;
    transition: border-color 0.3s, box-shadow 0.3s;
    font-family: 'Poppins', sans-serif;
}

input:focus, 
select:focus, 
textarea:focus {
    outline: none;
    border-color: #1a237e;
    box-shadow: 0 0 0 3px rgba(26, 35, 126, 0.1);
}

input::placeholder, 
textarea::placeholder {
    color: #aaa;
}

textarea {
    height: 100px;
    resize: vertical;
}

/* File Input Styles */
input[type="file"] {
    padding: 10px;
    border: 1px dashed #ccc;
    background-color: #f9f9f9;
}

/* Button Styles */
button {
    background-color: #1a237e;
    color: white;
    padding: 14px 24px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-size: 1rem;
    font-weight: 500;
    width: 100%;
    margin-top: 30px;
    transition: background-color 0.3s, transform 0.2s;
}

button:hover {
    background-color: #0d1657;
    transform: translateY(-1px);
}

button:active {
    transform: translateY(0);
}

/* Undo Button Styles */
#undo {
    background-color: #3f51b5;
    color: white;
    padding: 10px 16px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 0.9rem;
    font-weight: 500;
    width: auto;
    margin-top: 20px;
    margin-left: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    position: relative;
    transition: background-color 0.2s;
}

#undo::before {
    content: '↩';
    font-size: 20px;
    position: absolute;
    left: 10px;
    top: 50%;
    transform: translateY(-50%);
}

#undo span {
    margin-left: 20px;
}

#undo:hover {
    background-color: #303f9f;
}

/* Responsive Design */
@media (max-width: 768px) {
    .container {
        padding: 0 15px;
    }
    
    .form-section {
        padding: 20px;
    }
    
    h1 {
        font-size: 1.5rem;
    }
    
    input, select, textarea {
        font-size: 0.95rem;
        padding: 10px;
    }
    
    button {
        padding: 12px 20px;
        font-size: 0.95rem;
    }
}

/* Validation and Error States */
input:invalid {
    border-color: #d32f2f;
    box-shadow: 0 0 0 3px rgba(211, 47, 47, 0.1);
}

.error {
    color: #d32f2f;
    font-size: 0.8rem;
    margin-top: 5px;
}

/* Accessibility Enhancements */
input:focus-visible,
select:focus-visible,
textarea:focus-visible {
    outline: 2px solid #1a237e;
    outline-offset: 2px;
}

/* Print Styles */
@media print {
    body {
        background: white;
    }
    
    .container {
        max-width: none;
        margin: 0;
        padding: 0;
    }
    
    #undo, button {
        display: none;
    }
}
</style>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Certificate Management</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
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
                        <a class="nav-link active" href="view_marriage_certificates.php">View Marriage Records</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link " href="view_approved_birth_certificates.php">View Approved Birth Records</a>
                    </li>
                </ul>
                <div class="d-flex">
                    <a href="../Login/logout.php" class="btn btn-outline-light">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="header">
            <h1 class="page-title">Marriage Certificate Records</h1>
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
                    <th>Husband's Name</th>
                    <th>Wife's Name</th>
                    <th>Marriage Date</th>
                    <th>Registration Date</th>
                    <th>Status</th>
                    <th>Documents</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $status = $row['status'] ?? 'PENDING';
                        $statusClass = '';
                        switch ($status) {
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
                            <td><?php echo htmlspecialchars($row['husband_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['wife_name']); ?></td>
                            <td><?php echo date('d-m-Y', strtotime($row['marriage_date'])); ?></td>
                            <td><?php echo date('d-m-Y', strtotime($row['registration_date'])); ?></td>
                            <td><span class="status-badge <?php echo $statusClass; ?>"><?php echo $status; ?></span></td>
                            <td>
                                <?php if (!empty($row['marriage_certificate_file'])): ?>
                                    <div class="document-preview">
                                        <img src="/CPP WEB/villager/uploads/marriage_certificates/<?php echo htmlspecialchars($row['marriage_certificate_file']); ?>"
                                            alt="Marriage Certificate" class="preview-thumbnail"
                                            onerror="this.onerror=null; this.src='../assets/document-icon.png';">
                                        <a href="#" class="preview-link"
                                            onclick="viewDocument('../Villager Doc/Certificate Manage/marriage/uploads/<?php echo htmlspecialchars($row['marriage_certificate_file']); ?>', 'Marriage Certificate')">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor" width="16" height="16">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            Certificate
                                        </a>
                                    </div>
                                <?php endif; ?>

                                <?php if (!empty($row['husband_photo'])): ?>
                                    <div class="document-preview">
                                        <a href="#" class="preview-link"
                                            onclick="viewDocument('../../uploads/marriage/<?php echo htmlspecialchars($row['husband_photo']); ?>', 'Husband Photo')">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor" width="16" height="16">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            Husband Photo
                                        </a>
                                    </div>
                                <?php endif; ?>

                                <?php if (!empty($row['wife_photo'])): ?>
                                    <div class="document-preview">
                                        <a href="#" class="preview-link"
                                            onclick="viewDocument('../../uploads/marriage/<?php echo htmlspecialchars($row['wife_photo']); ?>', 'Wife Photo')">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor" width="16" height="16">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            Wife Photo
                                        </a>
                                    </div>
                                <?php endif; ?>

                                <div class="document-more">
                                    <a href="#" onclick="viewAllDocuments(<?php echo $row['id']; ?>)">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor" width="16" height="16">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z" />
                                        </svg>
                                        More Documents
                                    </a>
                                </div>
                            </td>
                            <td>
                                <button class="action-btn view-btn"
                                    onclick="viewDetails(<?php echo $row['id']; ?>)">View</button>
                                <?php if ($status !== 'APPROVED' && $status !== 'REJECTED'): ?>
                                    <button class="action-btn approve-btn"
                                        onclick="showStatusModal(<?php echo $row['id']; ?>, 'APPROVED')">Approve</button>
                                    <button class="action-btn reject-btn"
                                        onclick="showStatusModal(<?php echo $row['id']; ?>, 'REJECTED')">Reject</button>
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
                        <textarea id="remarks" name="remarks"
                            placeholder="Enter any additional notes or remarks..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="modal-btn btn-cancel"
                        onclick="closeModal('statusModal')">Cancel</button>
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

    <!-- All Documents Modal -->
    <div id="allDocumentsModal" class="modal">
        <div class="modal-content doc-modal-content">
            <div class="modal-header">
                <h2 class="modal-title">All Documents</h2>
                <button class="close-btn" onclick="closeModal('allDocumentsModal')">×</button>
            </div>
            <div class="modal-body">
                <div class="documents-grid" id="allDocumentsContainer"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="modal-btn btn-cancel"
                    onclick="closeModal('allDocumentsModal')">Close</button>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="marriage.js"></script>
</body>

</html>