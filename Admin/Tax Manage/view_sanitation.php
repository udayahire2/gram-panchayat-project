<?php
// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$database = "kusumba_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Pagination and search configuration
$records_per_page = 10;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$search_query = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$filter_type = isset($_GET['house_type']) ? $conn->real_escape_string($_GET['house_type']) : '';

// Build WHERE clause dynamically
$where_conditions = [];
$where_params = [];

if (!empty($search_query)) {
    $where_conditions[] = "(property_number LIKE ? OR owner_name LIKE ?)";
    $where_params[] = "%$search_query%";
    $where_params[] = "%$search_query%";
}

if (!empty($filter_type)) {
    $where_conditions[] = "house_type = ?";
    $where_params[] = $filter_type;
}

// Construct WHERE clause
$where_clause = !empty($where_conditions) ? "WHERE " . implode(" AND ", $where_conditions) : "";

try {
    // Prepare count query with dynamic WHERE clause
    $count_stmt = $conn->prepare("SELECT COUNT(*) as total FROM property_tax $where_clause");
    
    // Bind parameters if any
    if (!empty($where_params)) {
        $param_types = str_repeat('s', count($where_params));
        $count_stmt->bind_param($param_types, ...$where_params);
    }
    
    if (!$count_stmt->execute()) {
        throw new Exception("Count query execution failed: " . $count_stmt->error);
    }
    $count_result = $count_stmt->get_result();
    $total_records = $count_result->fetch_assoc()['total'];
    $count_stmt->close();

    // Pagination calculations
    $total_pages = ceil($total_records / $records_per_page);
    $offset = ($page - 1) * $records_per_page;

    // Prepare records query with dynamic WHERE clause and pagination
    $sql = "SELECT * FROM property_tax $where_clause ORDER BY property_number DESC LIMIT ? OFFSET ?";
    $stmt = $conn->prepare($sql);
    
    // Prepare parameter types and values
    $param_types = str_repeat('s', count($where_params)) . 'ii';
    $bind_params = array_merge($where_params, [$records_per_page, $offset]);
    
    // Bind parameters
    $stmt->bind_param($param_types, ...$bind_params);
    
    if (!$stmt->execute()) {
        throw new Exception("Records query execution failed: " . $stmt->error);
    }
    $result = $stmt->get_result();

} catch (Exception $e) {
    // Log the error with more details
    error_log("Database Error in view_sanitation.php: " . $e->getMessage());
    error_log("SQL Error Details: " . $conn->error);
    error_log("Search Query: " . $search_query);
    error_log("Filter Type: " . $filter_type);
    
    // Display user-friendly error message with a hint
    echo "<div class='alert alert-danger'>
            An error occurred while fetching records. 
            <details>
                <summary>Click for more information</summary>
                <p>Please check your search parameters or contact support.</p>
                <small>Error: " . htmlspecialchars($e->getMessage()) . "</small>
            </details>
          </div>";
    exit();
}

// Function to display success messages
function displaySuccessMessage($type) {
    $messages = [
        'update' => 'Record updated successfully!',
        'delete' => 'Record deleted successfully!'
    ];
    
    if (isset($_GET[$type . '_success'])) {
        echo "<div class='container mt-3'>
                <div class='alert alert-success alert-dismissible fade show' role='alert'>
                    {$messages[$type]}
                    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                </div>
              </div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sanitation Tax Records</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #ccddea;
            font-family: poppins;
        }
        .container {
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            padding: 30px;
            margin-top: 20px;
        }
        .table-responsive {
            margin-top: 20px;
        }
        .badge-concrete { background-color: #28a745; }
        .badge-raw { background-color: #dc3545; }
        .badge-rcc { background-color: #ffc107; }
    </style>
</head>
<body>
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Sanitation Tax Records</h2>
            <a href="../Clerk/clerk_dashboard.php" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to Dashboard
            </a>
        </div>

        <!-- Success Messages -->
        <?php 
        displaySuccessMessage('update');
        ?>

        <!-- Search and Filter Form -->
        <form method="GET" class="mb-4">
            <div class="row g-3">
                <div class="col-md-6">
                    <input type="text" name="search" class="form-control" 
                           placeholder="Search by Property Number or Owner Name" 
                           value="<?php echo htmlspecialchars($search_query); ?>">
                </div>
                <div class="col-md-4">
                    <select name="house_type" class="form-select">
                        <option value="">All House Types</option>
                        <option value="Concrete House" <?php echo $filter_type == 'Concrete House' ? 'selected' : ''; ?>>Concrete House</option>
                        <option value="Raw House" <?php echo $filter_type == 'Raw House' ? 'selected' : ''; ?>>Raw House</option>
                        <option value="RCC" <?php echo $filter_type == 'RCC' ? 'selected' : ''; ?>>RCC</option>
                        <option value="Open Place" <?php echo $filter_type == 'Open Place' ? 'selected' : ''; ?>>Open Place</option>
                        <option value="Letter House" <?php echo $filter_type == 'Letter House' ? 'selected' : ''; ?>>Letter House</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Search</button>
                </div>
            </div>
        </form>

        <!-- Sanitation Tax Records Table -->
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Property Number</th>
                        <th>Owner Name</th>
                        <th>House Type</th>
                        <th>Previous Sanitation Tax</th>
                        <th>Current Sanitation Tax</th>
                        <th>Total Sanitation Tax</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <?php 
                            $badgeClass = match($row['house_type']) {
                                'Concrete House' => 'badge-concrete',
                                'Raw House' => 'badge-raw',
                                'RCC' => 'badge-rcc',
                                default => 'bg-secondary'
                            };
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['id']); ?></td>
                                <td><?php echo htmlspecialchars($row['property_number']); ?></td>
                                <td><?php echo htmlspecialchars($row['owner_name']); ?></td>
                                <td>
                                    <span class="badge <?php echo $badgeClass; ?>">
                                        <?php echo htmlspecialchars($row['house_type']); ?>
                                    </span>
                                </td>
                                <td>₹<?php echo number_format($row['previous_sanitation_tax'], 2); ?></td>
                                <td>₹<?php echo number_format($row['sanitation_tax'], 2); ?></td>
                                <td>₹<?php echo number_format($row['total_sanitation_tax'], 2); ?></td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <button type='button' class='btn btn-info edit-record' 
                                                data-bs-toggle='modal' 
                                                data-bs-target='#updateSanitationTaxModal'
                                                data-id='<?php echo htmlspecialchars($row['id']); ?>'
                                                data-property-number='<?php echo htmlspecialchars($row['property_number']); ?>'
                                                data-owner-name='<?php echo htmlspecialchars($row['owner_name']); ?>'
                                                data-house-type='<?php echo htmlspecialchars($row['house_type']); ?>'
                                                data-previous-sanitation-tax='<?php echo number_format($row['previous_sanitation_tax'], 2, '.', ''); ?>'
                                                data-sanitation-tax='<?php echo number_format($row['sanitation_tax'], 2, '.', ''); ?>'
                                                data-total-sanitation-tax='<?php echo number_format($row['total_sanitation_tax'], 2, '.', ''); ?>'>
                                            <i class='bi bi-pencil'></i> Edit
                                        </button>
                                        <a href="sanitation_taxBill.php?id=<?php echo $row['id']; ?>" class="btn btn-success">
                                            <i class="bi bi-file-earmark-text"></i> Bill
                                        </a>
                                        <button type='button' class='btn btn-primary view-details' 
                                                data-bs-toggle='modal' 
                                                data-bs-target='#propertyDetailsModal<?php echo $row['id']; ?>'
                                                data-property-number='<?php echo htmlspecialchars($row['property_number']); ?>'
                                                data-owner-name='<?php echo htmlspecialchars($row['owner_name']); ?>'
                                                data-house-type='<?php echo htmlspecialchars($row['house_type']); ?>'>
                                            <i class="bi bi-info-circle"></i> Details
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <!-- Property Details Modal -->
                            <div class="modal fade" id="propertyDetailsModal<?php echo $row['id']; ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Property Details</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <strong>Property Number:</strong> <?php echo htmlspecialchars($row['property_number']); ?>
                                            </div>
                                            <div class="mb-3">
                                                <strong>Owner Name:</strong> <?php echo htmlspecialchars($row['owner_name']); ?>
                                            </div>
                                            <div class="mb-3">
                                                <strong>House Type:</strong> <?php echo htmlspecialchars($row['house_type']); ?>
                                            </div>
                                            <div class="mb-3">
                                                <strong>Previous Sanitation Tax:</strong> ₹<?php echo number_format($row['previous_sanitation_tax'], 2); ?>
                                            </div>
                                            <div class="mb-3">
                                                <strong>Current Sanitation Tax:</strong> ₹<?php echo number_format($row['sanitation_tax'], 2); ?>
                                            </div>
                                            <div class="mb-3">
                                                <strong>Total Sanitation Tax:</strong> ₹<?php echo number_format($row['total_sanitation_tax'], 2); ?>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center">No sanitation tax records found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    <!-- Update Sanitation Tax Record Modal -->
    <div class="modal fade" id="updateSanitationTaxModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Update Sanitation Tax Record</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="updateSanitationTaxForm" method="POST" action="update_sanitation.php">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Property Number</label>
                                <input type="text" class="form-control" name="property_number" id="updatePropertyNumber" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Owner Name</label>
                                <input type="text" class="form-control" name="owner_name" id="updateOwnerName" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">House Type</label>
                                <select class="form-select" name="house_type" id="updateHouseType" required>
                                    <option value="Concrete House">Concrete House</option>
                                    <option value="Raw House">Raw House</option>
                                    <option value="RCC">RCC</option>
                                    <option value="Open Place">Open Place</option>
                                    <option value="Letter House">Letter House</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Previous Sanitation Tax</label>
                                <input type="number" step="0.01" class="form-control" name="previous_sanitation_tax" id="updatePreviousSanitationTax" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Current Sanitation Tax</label>
                                <input type="number" step="0.01" class="form-control" name="sanitation_tax" id="updateSanitationTax" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Total Sanitation Tax</label>
                                <input type="number" step="0.01" class="form-control" name="total_sanitation_tax" id="updateTotalSanitationTax" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Record</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const updateModal = new bootstrap.Modal(document.getElementById('updateSanitationTaxModal'));

        // Edit button click handler
        document.querySelectorAll('.edit-record').forEach(button => {
            button.addEventListener('click', function() {
                // Populate modal with data attributes
                document.getElementById('updatePropertyNumber').value = this.getAttribute('data-property-number');
                document.getElementById('updateOwnerName').value = this.getAttribute('data-owner-name');
                document.getElementById('updateHouseType').value = this.getAttribute('data-house-type');
                document.getElementById('updatePreviousSanitationTax').value = parseFloat(this.getAttribute('data-previous-sanitation-tax')).toFixed(2);
                document.getElementById('updateSanitationTax').value = parseFloat(this.getAttribute('data-sanitation-tax')).toFixed(2);
                document.getElementById('updateTotalSanitationTax').value = parseFloat(this.getAttribute('data-total-sanitation-tax')).toFixed(2);
                
                // Show the modal
                updateModal.show();
            });
        });
    });
    </script>
</body>
</html>