<?php
// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$database = "kusumba_db";

// Initialize variables
$result = null;
$error_message = '';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    // Create connection
    $conn = new mysqli($servername, $username, $password, $database);

    // Check connection
    if ($conn->connect_error) {
        throw new Exception('Connection Failed: ' . $conn->connect_error);
    }

    // Get search parameters
    $search_query = isset($_GET['search']) ? trim($_GET['search']) : '';
    $filter_type = isset($_GET['house_type']) ? trim($_GET['house_type']) : '';

    // Build where conditions
    $where_conditions = [];
    $where_params = [];
    $param_types = '';

    if (!empty($search_query)) {
        $where_conditions[] = "(property_number LIKE ? OR owner_name LIKE ?)";
        $where_params[] = "%$search_query%";
        $where_params[] = "%$search_query%";
        $param_types .= 'ss'; // Two string parameters
    }

    if (!empty($filter_type)) {
        $where_conditions[] = "house_type = ?";
        $where_params[] = $filter_type;
        $param_types .= 's'; // One string parameter
    }

    $where_clause = !empty($where_conditions) ? "WHERE " . implode(" AND ", $where_conditions) : "";

    // Count total records
    $count_sql = "SELECT COUNT(*) as total FROM property_tax $where_clause";
    $count_stmt = $conn->prepare($count_sql);

    if (!empty($where_params)) {
        $count_stmt->bind_param($param_types, ...$where_params);
    }

    $count_stmt->execute();
    $count_result = $count_stmt->get_result();
    $total_records = $count_result->fetch_assoc()['total'];
    $count_stmt->close();

    // Fetch property tax records
    $sql = "SELECT * FROM property_tax $where_clause ORDER BY property_number DESC";
    $stmt = $conn->prepare($sql);

    if (!empty($where_params)) {
        $stmt->bind_param($param_types, ...$where_params);
    }

    $stmt->execute();
    $result = $stmt->get_result();
} catch (Exception $e) {
    // Log the full error details
    error_log("Database Error in view_home.php: " . $e->getMessage());
    error_log("SQL Query: " . ($sql ?? 'N/A'));
    error_log("Search Query: " . $search_query);
    error_log("Filter Type: " . $filter_type);

    // Set error message
    $error_message = "An error occurred while fetching records. " .
        "Please check your database connection and try again. " .
        "If the problem persists, contact the system administrator.";
}

// Function to display success messages
function displaySuccessMessage($type)
{
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
    <title>Home Tax Records</title>
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

        .badge-concrete {
            background-color: #28a745;
            color: white;
        }

        .badge-rcc {
            background-color: #ffc107;
            color: black;
        }

        .badge-raw {
            background-color: #dc3545;
            color: white;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Home Tax Records</h2>
        </div>

        <!-- Success Messages -->
        <?php
        displaySuccessMessage('update');
        displaySuccessMessage('delete');

        // Display any error messages
        if (!empty($error_message)) {
            echo "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
                    $error_message
                    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                  </div>";
        }
        ?>

        <!-- Search and Filter Form -->
        <form method="GET" class="mb-4">
            <div class="row g-3">
                <div class="col-md-6">
                    <input type="text" name="search" class="form-control"
                        placeholder="Search by Home Number or Owner Name"
                        value="<?php echo htmlspecialchars($search_query); ?>">
                </div>
                <div class="col-md-4">
                    <select name="house_type" class="form-select">
                        <option value="">All House Types</option>
                        <option value="Concrete House" <?php echo $filter_type == 'Concrete House' ? 'selected' : ''; ?>>Concrete House</option>
                        <option value="RCC" <?php echo $filter_type == 'RCC' ? 'selected' : ''; ?>>RCC</option>
                        <option value="Raw House" <?php echo $filter_type == 'Raw House' ? 'selected' : ''; ?>>Raw House</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Search</button>
                </div>
            </div>
        </form>

        <!-- Property Tax Records Table -->
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Home Number</th>
                        <th>Owner Name</th>
                        <th>House Type</th>
                        <th>Area (sq.ft)</th>
                        <th>Total Home Tax</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result && $result->num_rows > 0):
                        while ($row = $result->fetch_assoc()):
                            $badgeClass = match ($row['house_type']) {
                                'Concrete House' => 'badge-concrete',
                                'RCC' => 'badge-rcc',
                                'Raw House' => 'badge-raw',
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
                                <td><?php echo number_format($row['area_sqft'], 2); ?></td>
                                <td>₹<?php echo number_format($row['total_home_tax'], 2); ?></td>

                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <button type='button' class='btn btn-info edit-record'
                                            data-bs-toggle='modal'
                                            data-bs-target='#editPropertyTaxModal'
                                            data-id='<?php echo htmlspecialchars($row['id']); ?>'
                                            data-property-number='<?php echo htmlspecialchars($row['property_number']); ?>'
                                            data-owner-name='<?php echo htmlspecialchars($row['owner_name']); ?>'
                                            data-house-type='<?php echo htmlspecialchars($row['house_type']); ?>'
                                            data-previous-home-tax='<?php echo number_format($row['previous_home_tax'], 2, '.', ''); ?>'
                                            data-total-home-tax='<?php echo number_format($row['total_home_tax'], 2, '.', ''); ?>'>
                                            <i class='bi bi-pencil'></i> Edit
                                        </button>
                                        <a href="home_taxBill.php?id=<?php echo $row['id']; ?>" class="btn btn-success">
                                            <i class="bi bi-file-earmark-text"></i> Bill
                                        </a>
                                        <button type='button' class='btn btn-primary view-details'
                                            data-bs-toggle='modal'
                                            data-bs-target='#propertyDetailsModal<?php echo $row['id']; ?>'>
                                            <i class="bi bi-info-circle"></i> Details
                                        </button>
                                    </div>
                                </td>
                                <td>
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
                                                        <strong>Area:</strong> <?php echo number_format($row['area_sqft'], 2); ?> sq.ft
                                                    </div>
                                                    <div class="mb-3">
                                                        <strong>Total Home Tax:</strong> ₹<?php echo number_format($row['total_home_tax'], 2); ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php
                        endwhile;
                    else:
                        ?>
                        <tr>
                            <td colspan="8" class="text-center">No property tax records found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Edit Property Tax Record Modal -->
    <div class="modal fade" id="editPropertyTaxModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Property Tax Record</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editPropertyTaxForm" method="POST" action="update_home.php">
                    <div class="modal-body">
                        <input type="hidden" name="record_id" id="editRecordId">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Property Number</label>
                                <input type="text" class="form-control" name="property_number" id="editPropertyNumber" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Owner Name</label>
                                <input type="text" class="form-control" name="owner_name" id="editOwnerName" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">House Type</label>
                                <select class="form-select" name="house_type" id="editHouseType" required>
                                    <option value="Concrete-House">Concrete House</option>
                                    <option value="RCC">RCC</option>
                                    <option value="Raw-House">Raw House</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Previous Home Tax</label>
                                <input type="number" step="0.01" class="form-control" name="previous_home_tax" id="editPreviousHomeTax" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Total Home Tax</label>
                                <input type="number" step="0.01" class="form-control" name="total_home_tax" id="editTotalHomeTax" required>
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
            // Edit button click handler
            document.querySelectorAll('.edit-record').forEach(button => {
                button.addEventListener('click', function() {
                    // Populate form fields with data attributes
                    document.getElementById('editRecordId').value = this.getAttribute('data-id');
                    document.getElementById('editPropertyNumber').value = this.getAttribute('data-property-number');
                    document.getElementById('editOwnerName').value = this.getAttribute('data-owner-name');
                    document.getElementById('editHouseType').value = this.getAttribute('data-house-type');
                    document.getElementById('editPreviousHomeTax').value = this.getAttribute('data-previous-home-tax');
                    document.getElementById('editTotalHomeTax').value = this.getAttribute('data-total-home-tax');
                });
            });

            // Log view details clicks for debugging
            document.querySelectorAll('.view-details').forEach(button => {
                button.addEventListener('click', function() {
                    console.log("View Details clicked for modal: " + this.getAttribute('data-bs-target'));
                });
            });
        });
    </script>
</body>

</html>