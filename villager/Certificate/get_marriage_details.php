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

// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<p class='error-message'>Error: No record ID provided.</p>";
    exit;
}

$record_id = intval($_GET['id']);

// Prepare and execute query
$sql = "SELECT * FROM marriage_certificates active WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $record_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<p class='error-message'>Error: Record not found.</p>";
    exit;
}

$row = $result->fetch_assoc();

// Format the output
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

// Output the details
?>

<div class="details-container">
    <div class="detail-section">
        <h3>Marriage Details</h3>
        <div class="detail-row">
            <div class="detail-label">Husband Name:</div>
            <div class="detail-value"><?php echo htmlspecialchars($row['husband_name']); ?></div>
        </div>
        <div class="detail-row">
            <div class="detail-label">Wife Name:</div>
            <div class="detail-value"><?php echo htmlspecialchars($row['wife_name']); ?></div>
        </div>
        <div class="detail-row">
            <div class="detail-label">Husband Age:</div>
            <div class="detail-value"><?php echo htmlspecialchars($row['husband_age']); ?></div>
        </div>
        <div class="detail-row">
            <div class="detail-label">Wife Age:</div>
            <div class="detail-value"><?php echo htmlspecialchars($row['wife_age']); ?></div>
        </div>
        <div class="detail-row">
            <div class="detail-label">Husband Aadhaar:</div>
            <div class="detail-value"><?php echo htmlspecialchars($row['husband_aadhar_no']); ?></div>
        </div>
        <div class="detail-row">
            <div class="detail-label">Wife Aadhaar:</div>
            <div class="detail-value"><?php echo htmlspecialchars($row['wife_aadhar_no']); ?></div>
        </div>
        <div class="detail-row">
            <div class="detail-label">Marriage Date:</div>
            <div class="detail-value"><?php echo date('d-m-Y', strtotime($row['marriage_date'])); ?></div>
        </div>
        <div class="detail-row">
            <div class="detail-label">Marriage Place:</div>
            <div class="detail-value"><?php echo htmlspecialchars($row['marriage_place']); ?></div>
        </div>
    </div>

    <div class="detail-section">
        <h3>Registration Details</h3>
        <div class="detail-row">
            <div class="detail-label">Registration Number:</div>
            <div class="detail-value"><?php echo htmlspecialchars($row['registration_number'] ?? 'Not Specified'); ?></div>
        </div>
        <div class="detail-row">
            <div class="detail-label">Registration Date:</div>
            <div class="detail-value"><?php echo date('d-m-Y', strtotime($row['registration_date'])); ?></div>
        </div>
        <div class="detail-row">
            <div class="detail-label">Status:</div>
            <div class="detail-value">
                <span class="status-badge <?php echo $statusClass; ?>"><?php echo $status; ?></span>
            </div>
        </div>
        <?php if (!empty($row['remarks'])): ?>
        <div class="detail-row">
            <div class="detail-label">Remarks:</div>
            <div class="detail-value"><?php echo htmlspecialchars($row['remarks']); ?></div>
        </div>
        <?php endif; ?>
    </div>

    <div class="detail-section">
        <h3>Application Details</h3>
        <div class="detail-row">
            <div class="detail-label">Applied On:</div>
            <div class="detail-value"><?php echo date('d-m-Y H:i', strtotime($row['created_at'])); ?></div>
        </div>
        <?php if (!empty($row['updated_at'])): ?>
        <div class="detail-row">
            <div class="detail-label">Last Updated:</div>
            <div class="detail-value"><?php echo date('d-m-Y H:i', strtotime($row['updated_at'])); ?></div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php
$stmt->close();
$conn->close();
?>