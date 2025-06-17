<?php

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

    // Update status in death_certificate table
    $updateSql = "UPDATE death_certificate SET status = ?, remarks = ?, updated_at = NOW() WHERE id = ?";
    $stmt = $conn->prepare($updateSql);
    $stmt->bind_param("ssi", $new_status, $remarks, $record_id);

    if ($stmt->execute()) {
        $statusMsg = "Status updated successfully!";

        // If status is APPROVED, insert into approved_death_certificate
        if ($new_status === 'APPROVED') {
            // Fetch the record from death_certificate
            $selectSql = "SELECT * FROM death_certificate WHERE id = ?";
            $selectStmt = $conn->prepare($selectSql);
            $selectStmt->bind_param("i", $record_id);
            $selectStmt->execute();
            $result = $selectStmt->get_result();

            if ($row = $result->fetch_assoc()) {
                // Handle NULL registration_number
                $registration_number = $row['registration_number'] ?? 'NOT_SPECIFIED'; // Default value if NULL

                // Insert into approved_death_certificate
                $insertSql = "INSERT INTO approved_death_certificate (
                    name_of_deceased, gender, age, aadhaar_number, date_of_death, 
                    place_of_death, address, registration_number, registration_date, remarks
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

                $insertStmt = $conn->prepare($insertSql);
                $insertStmt->bind_param(
                    "ssisssssss",
                    $row['name_of_deceased'],
                    $row['gender'],
                    $row['age'],
                    $row['aadhaar_number'],
                    $row['date_of_death'],
                    $row['place_of_death'],
                    $row['address'],
                    $registration_number, // Use the handled value
                    $row['registration_date'],
                    $remarks
                );

                if ($insertStmt->execute()) {
                    $statusMsg .= " Record added to approved death certificates!";
                } else {
                    $errorMsg = "Error inserting into approved table: " . $insertStmt->error;
                }
                $insertStmt->close();
            } else {
                $errorMsg = "Error: Record not found for insertion.";
            }
            $selectStmt->close();
        }
    } else {
        $errorMsg = "Error updating status: " . $stmt->error;
    }

    $stmt->close();
}

// Fetch all records
$sql = "SELECT * FROM death_certificate ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Death Certificate Records</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/view_death.css">
</head>

<body>
    <div class="container">
        <div class="header">
            <h1 class="page-title">Death Certificate Records</h1>
            <div class="d-flex gap-2">
                <a href="../../Admin/Clerk/clerk_dashboard.php" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Dashboard
                </a>
            </div>
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
                    <th>Date of Death</th>
                    <th>Gender</th>
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
                            <td><?php echo htmlspecialchars($row['name_of_deceased']); ?></td>
                            <td><?php echo date('d-m-Y', strtotime($row['date_of_death'])); ?></td>
                            <td><?php echo htmlspecialchars($row['gender']); ?></td>
                            <td><?php echo date('d-m-Y', strtotime($row['registration_date'])); ?></td>
                            <td><span class="status-badge <?php echo $statusClass; ?>"><?php echo $status; ?></span></td>

                            <td>
                                <?php if (!empty($row['aadhaar_document'])): ?>
                                    <div class="document-preview">
                                        <a href="#" class="preview-link"
                                            onclick="viewDocument('../uploads/death_certificate/<?php echo $row['aadhaar_document']; ?>', 'Next of Death Aadhaar')">
                                            Aadhaar Proof
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
                <div class="doc-container" id="docContainer">
                    <!-- Document will be shown here -->
                </div>
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
            <div class="modal-body" id="detailsContainer">
                <!-- Details will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="modal-btn btn-cancel" onclick="closeModal('detailsModal')">Close</button>
            </div>
        </div>
    </div>

    <script src="../assets/js/death.js"></script>
</body>

</html>