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
        if ($new_status === 'APPROVED') {
            // Generate a random 3-digit registration number
            $registration_number = str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);

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
            $insertStmt->bind_param(
                "ssssssssss",
                $birthRecord['name'],
                $birthRecord['dob'],
                $birthRecord['mother_name'],
                $birthRecord['father_name'],
                $birthRecord['registration_date'],
                $birthRecord['gender'],
                $birthRecord['mother_aadhaar'],
                $birthRecord['father_aadhaar'],
                $birthRecord['address'],
                $registration_number
            );

            $insertStmt->execute();
            $insertStmt->close();
            $statusMsg = "Status updated and record approved successfully!";
        } else {
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
    <style>
        /* Main Styles for Marriage Certificate Records */
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
            margin: 0 auto;
            padding: 20px;
        }

        /* Header Section */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 2px solid #e5e7eb;
        }

        .page-title {
            color: #1e3a8a;
            font-size: 28px;
            font-weight: 600;
        }

        /* Records Table */
        .records-table {
            width: 100%;
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
            border-collapse: collapse;
            overflow: hidden;
            margin-bottom: 30px;
        }

        .records-table th {
            background-color: #1e3a8a;
            color: white;
            padding: 15px;
            text-align: left;
            font-weight: 500;
        }

        .records-table tr:nth-child(even) {
            background-color: #f9fafb;
        }

        .records-table td {
            padding: 15px;
            border-bottom: 1px solid #e5e7eb;
        }

        .records-table tr:hover {
            background-color: #f3f4f6;
        }

        /* Status Badges */
        .status-badge {
            padding: 6px 12px;
            border-radius: 50px;
            font-size: 14px;
            font-weight: 500;
            display: inline-block;
            text-align: center;
        }

        .status-pending {
            background-color: #fff7ed;
            color: #c2410c;
            border: 1px solid #fdba74;
        }

        .status-approved {
            background-color: #ecfdf5;
            color: #047857;
            border: 1px solid #6ee7b7;
        }

        .status-rejected {
            background-color: #fef2f2;
            color: #b91c1c;
            border: 1px solid #fca5a5;
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

        .approve-btn {
            background-color: #d1fae5;
            color: #047857;
        }

        .approve-btn:hover {
            background-color: #a7f3d0;
        }

        .reject-btn {
            background-color: #fee2e2;
            color: #b91c1c;
        }

        .reject-btn:hover {
            background-color: #fecaca;
        }

        /* Document Preview */
        .document-preview {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 5px;
        }

        .preview-thumbnail {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 4px;
            border: 1px solid #ddd;
        }

        .preview-link {
            display: flex;
            align-items: center;
            gap: 5px;
            text-decoration: none;
            color: #0d6efd;
        }

        .preview-link:hover {
            text-decoration: underline;
        }

        .preview-link svg {
            margin-right: 5px;
            height: 16px;
            width: 16px;
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
            max-width: 600px;
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

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #374151;
        }

        .form-group textarea {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 15px;
            min-height: 120px;
        }

        .modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            border-top: 1px solid #e5e7eb;
            padding-top: 20px;
        }

        .modal-btn {
            padding: 10px 20px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-approve {
            background-color: #10b981;
            color: white;
        }

        .btn-reject {
            background-color: #ef4444;
            color: white;
        }

        .btn-cancel {
            background-color: #e5e7eb;
            color: #4b5563;
        }

        /* Alert Messages */
        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 25px;
            font-weight: 500;
        }

        .alert-success {
            background-color: #ecfdf5;
            color: #047857;
            border-left: 4px solid #10b981;
        }

        .alert-danger {
            background-color: #fef2f2;
            color: #b91c1c;
            border-left: 4px solid #ef4444;
        }

        /* Document view modal */
        .doc-modal-content {
            max-width: 800px;
            max-height: 90vh;
            overflow-y: auto;
        }

        .doc-container {
            margin-top: 20px;
            text-align: center;
        }

        .doc-container img {
            max-width: 100%;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
        }

        /* Responsive adjustments */
        @media (max-width: 1024px) {
            .header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }

            .records-table {
                display: block;
                overflow-x: auto;
            }
        }

        @media (max-width: 640px) {
            .action-btn {
                padding: 6px 12px;
                font-size: 12px;
                margin-bottom: 5px;
                display: block;
            }
        }

        /* Details modal styling */
        .details-container {
            padding: 10px;
            max-height: 70vh;
            overflow-y: auto;
        }

        .details-section {
            margin-bottom: 25px;
            border-bottom: 1px solid #e0e0e0;
            padding-bottom: 15px;
        }

        .details-section h4 {
            color: #2c3e50;
            margin-bottom: 15px;
            font-size: 18px;
            font-weight: 600;
        }

        .details-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
            padding: 20px;
        }

        .document-item {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 10px;
            background: #fff;
        }

        .document-item h4 {
            margin: 0 0 10px 0;
            font-size: 14px;
            color: #333;
        }

        .document-preview-large {
            position: relative;
            width: 100%;
            aspect-ratio: 3/4;
            overflow: hidden;
            border-radius: 4px;
            background: #f5f5f5;
        }

        .document-preview-large img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .document-actions {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 10px;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s;
        }

        .document-preview-large:hover .document-actions {
            opacity: 1;
        }

        .document-actions button {
            margin: 0 5px;
        }

        .detail-item {
            margin-bottom: 10px;
        }

        .document-item {
            margin-top: 15px;
        }

        .document-item a {
            margin-bottom: 10px;
        }

        .document-item a {
            display: inline-block;
            padding: 5px 10px;
            background-color: #e0f2fe;
            color: #0369a1;
            border-radius: 4px;
            text-decoration: none;
            font-size: 14px;
            transition: all 0.2s ease;
        }

        .document-item a:hover {
            background-color: #bae6fd;
        }

        /* Make the details modal larger */
        #detailsModal .modal-content {
            width: 90%;
            max-width: 900px;
        }
    </style>
</head>

<body>
  


    <div class="container">
        <div class="header">
            <h1 class="page-title">Birth Certificate Records</h1>
            <a href="../../Admin/Clerk/clerk_dashboard.php" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to Dashboard
            </a>
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
                            <td><?php echo htmlspecialchars($row['name']); ?></td>
                            <td><?php echo date('d-m-Y', strtotime($row['dob'])); ?></td>
                            <td>
                                <div>Mother: <?php echo htmlspecialchars($row['mother_name']); ?></div>
                                <div>Father: <?php echo htmlspecialchars($row['father_name']); ?></div>
                            </td>
                            <td><?php echo date('d-m-Y', strtotime($row['registration_date'])); ?></td>
                            <td><span class="status-badge <?php echo $statusClass; ?>"><?php echo $status; ?></span></td>
                            <td>
                                <?php if (!empty($row['proof_of_birth_file'])): ?>
                                    <div class="document-preview">
                                        <a href="#" class="preview-link"
                                            onclick="viewDocument('../uploads/birth_certificate/<?php echo $row['proof_of_birth_file']; ?>', 'Birth Proof')">

                                            Birth Proof
                                        </a>
                                    </div>
                                <?php endif; ?>
                                <?php if (!empty($row['mother_aadhaar_proof'])): ?>
                                    <div class="document-preview">
                                        <a href="#" class="preview-link"
                                            onclick="viewDocument('../uploads/birth_certificate/<?php echo $row['mother_aadhaar_proof']; ?>', 'Mother Aadhaar')">

                                            Mother Aadhaar
                                        </a>
                                    </div>
                                <?php endif; ?>

                                <?php if (!empty($row['father_aadhaar_proof'])): ?>
                                    <div class="document-preview">
                                        <a href="#" class="preview-link"
                                            onclick="viewDocument('../uploads/birth_certificate/<?php echo $row['father_aadhaar_proof']; ?>', 'Father Aadhaar')">

                                            Father Aadhaar
                                        </a>
                                    </div>
                                <?php endif; ?>

                                <?php if (!empty($row['marriage_certificate'])): ?>
                                    <div class="document-preview">
                                        <a href="#" class="preview-link"
                                            onclick="viewDocument('../uploads/birth_certificate/<?php echo $row['marriage_certificate']; ?>', 'Marriage Certificate')">

                                            Marriage Certificate
                                        </a>
                                    </div>
                                <?php endif; ?>
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/birth.js">

    </script>
</body>

</html>