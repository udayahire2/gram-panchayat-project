<?php
// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$database = "kusumba_db";

// Initialize variables
$total_records = 0;
$total_pages = 0;
$result = null;
$error_message = '';

// Create connection with error reporting
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    // Create connection
    $conn = new mysqli($servername, $username, $password, $database);

    // Check connection
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    // Pagination and search configuration
    $records_per_page = 10;
    $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $search_query = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
    $filter_type = isset($_GET['water_tax_type']) ? $conn->real_escape_string($_GET['water_tax_type']) : '';

    // Build WHERE clause dynamically
    $where_conditions = [];
    $where_params = [];

    if (!empty($search_query)) {
        $where_conditions[] = "(property_number LIKE ? OR owner_name LIKE ?)";
        $where_params[] = "%$search_query%";
        $where_params[] = "%$search_query%";
    }

    if (!empty($filter_type)) {
        $where_conditions[] = "water_tax_type = ?";
        $where_params[] = $filter_type;
    }

    // Construct WHERE clause
    $where_clause = !empty($where_conditions) ? "WHERE " . implode(" AND ", $where_conditions) : "";

    // Prepare count query with dynamic WHERE clause
    $count_sql = "SELECT COUNT(*) as total FROM property_tax $where_clause";
    $count_stmt = $conn->prepare($count_sql);
    
    // Bind parameters if any
    if (!empty($where_params)) {
        $param_types = str_repeat('s', count($where_params));
        $count_stmt->bind_param($param_types, ...$where_params);
    }
    
    $count_stmt->execute();
    $count_result = $count_stmt->get_result();
    $total_records = $count_result->fetch_assoc()['total'];
    $count_stmt->close();

    // Pagination calculations
    $total_pages = ceil($total_records / $records_per_page);
    $offset = max(0, ($page - 1) * $records_per_page);

    // Prepare records query with dynamic WHERE clause and pagination
    $sql = "SELECT * FROM property_tax $where_clause ORDER BY property_number DESC LIMIT ? OFFSET ?";
    $stmt = $conn->prepare($sql);
    
    // Prepare parameter types and values
    $param_types = str_repeat('s', count($where_params)) . 'ii';
    $bind_params = array_merge($where_params, [$records_per_page, $offset]);
    
    // Bind parameters
    $stmt->bind_param($param_types, ...$bind_params);
    
    $stmt->execute();
    $result = $stmt->get_result();

} catch (Exception $e) {
    // Log the full error details
    error_log("Database Error in view_water.php: " . $e->getMessage());
    error_log("SQL Query: " . ($sql ?? 'N/A'));
    error_log("Search Query: " . $search_query);
    error_log("Filter Type: " . $filter_type);
    
    // Set error message
    $error_message = "An error occurred while fetching records. " . 
                     "Please check your database connection and try again. " . 
                     "If the problem persists, contact the system administrator.";
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
    <title>Water Tax Records</title>
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
        .badge-general { background-color: #28a745; }
        .badge-special { background-color: #dc3545; }
    </style>
</head>
<body>
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Water Tax Records</h2>
            <a href="../Clerk/clerk_dashboard.php" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to Dashboard
            </a>
        </div>

        <!-- Error Message Handling -->
        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>

        <!-- Success Messages -->
        <?php 
        displaySuccessMessage('update');
        displaySuccessMessage('delete');
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
                    <select name="water_tax_type" class="form-select">
                        <option value="">All Water Tax Types</option>
                        <option value="General" <?php echo $filter_type == 'General' ? 'selected' : ''; ?>>General</option>
                        <option value="Special" <?php echo $filter_type == 'Special' ? 'selected' : ''; ?>>Special</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Search</button>
                </div>
            </div>
        </form>

        <!-- Water Tax Records Table -->
        <div class="table-responsive">
            <?php if ($result !== null): ?>
                <table class="table table-striped table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Property Number</th>
                            <th>Owner Name</th>
                            <th>Water Tax Type</th>
                            <th>Previous Water Tax</th>
                            <th>Current Water Tax</th>
                            <th>Total Water Tax</th>
                            <th>Actions</th>
                            
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while($row = $result->fetch_assoc()): ?>
                                <?php 
                                $badgeClass = $row['water_tax_type'] == 'General' ? 'badge-general' : 'badge-special';
                                ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['id']); ?></td>
                                    <td><?php echo htmlspecialchars($row['property_number']); ?></td>
                                    <td><?php echo htmlspecialchars($row['owner_name']); ?></td>
                                    <td>
                                        <span class="badge <?php echo $badgeClass; ?>">
                                            <?php echo htmlspecialchars($row['water_tax_type']); ?>
                                        </span>
                                    </td>
                                    <td>₹<?php echo number_format($row['previous_water_tax'], 2); ?></td>
                                    <td>₹<?php echo number_format($row['water_tax'], 2); ?></td>
                                    <td>₹<?php echo number_format($row['total_water_tax'], 2); ?></td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <button type='button' class='btn btn-info edit-record' 
                                                data-bs-toggle='modal' 
                                                data-bs-target='#updateWaterTaxModal'
                                                data-id='<?php echo htmlspecialchars($row['id']); ?>'
                                                data-property-number='<?php echo htmlspecialchars($row['property_number']); ?>'
                                                data-owner-name='<?php echo htmlspecialchars($row['owner_name']); ?>'
                                                data-water-tax-type='<?php echo htmlspecialchars($row['water_tax_type']); ?>'
                                                data-previous-water-tax='<?php echo number_format($row['previous_water_tax'], 2, '.', ''); ?>'
                                                data-water-tax='<?php echo number_format($row['water_tax'], 2, '.', ''); ?>'
                                                data-total-water-tax='<?php echo number_format($row['total_water_tax'], 2, '.', ''); ?>'>
                                                <i class='bi bi-pencil'></i> Edit
                                            </button>
                                            <a href="water_taxBill.php?id=<?php echo $row['id']; ?>" class="btn btn-success">
                                                <i class="bi bi-file-earmark-text"></i> Bill
                                            </a>
                                            <button type='button' class='btn btn-primary view-details' 
                                                data-bs-toggle='modal' 
                                                data-bs-target='#propertyDetailsModal'
                                                data-property-number='<?php echo htmlspecialchars($row['property_number']); ?>'
                                                data-owner-name='<?php echo htmlspecialchars($row['owner_name']); ?>'
                                                data-house-type='<?php echo htmlspecialchars($row['house_type'] ?? 'N/A'); ?>'
                                                data-transfer-name='<?php echo htmlspecialchars($row['transfer_name'] ?? 'N/A'); ?>'
                                                data-width='<?php echo htmlspecialchars($row['width'] ?? 'N/A'); ?>'
                                                data-area-sqft='<?php echo htmlspecialchars($row['area_sqft'] ?? 'N/A'); ?>'>
                                                <i class="bi bi-info-circle"></i> Details
                                            </button>
                                        </div>
                                    </td>
                                       
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9" class="text-center">No water tax records found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>

                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                    <nav aria-label="Page navigation">
                        <ul class="pagination justify-content-center">
                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $i; 
                                        echo !empty($search_query) ? '&search=' . urlencode($search_query) : ''; 
                                        echo !empty($filter_type) ? '&water_tax_type=' . urlencode($filter_type) : ''; 
                                    ?>">
                                        <?php echo $i; ?>
                                    </a>
                                </li>
                            <?php endfor; ?>
                        </ul>
                    </nav>
                <?php endif; ?>
            <?php else: ?>
                <div class="alert alert-warning">
                    Unable to retrieve records at this time. Please try again later.
                </div>
            <?php endif; ?>
        </div>

        <!-- Property Details Modal - One shared modal for all records -->
        <div class='modal fade' id='propertyDetailsModal' tabindex='-1'>
            <div class='modal-dialog modal-lg'>
                <div class='modal-content'>
                    <div class='modal-header'>
                        <h5 class='modal-title'>Water Property Details</h5>
                        <button type='button' class='btn-close' data-bs-dismiss='modal'></button>
                    </div>
                    <div class='modal-body'>
                        <div class='row'>
                            <div class='col-md-6'>
                                <h6>Water Connection Information</h6>
                                <p><strong>Property Number:</strong> <span id="modal-property-number"></span></p>
                                <p><strong>Water Connection Owner:</strong> <span id="modal-owner-name"></span></p>
                                <p><strong>Connection Type:</strong> <span id="modal-house-type"></span></p>
                            </div>
                            <div class='col-md-6'>
                                <h6>Water Usage Details</h6>
                                <p><strong>Service Provider:</strong> <span id="modal-transfer-name"></span></p>
                                <p><strong>Connection Size:</strong> <span id="modal-width"></span> inches</p>
                                <p><strong>Total Area Served:</strong> <span id="modal-area-sqft"></span> sq.ft</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Update Water Tax Modal - One shared modal for all records -->
        <div class='modal fade' id='updateWaterTaxModal' tabindex='-1'>
            <div class='modal-dialog modal-lg'>
                <div class='modal-content'>
                    <div class='modal-header'>
                        <h5 class='modal-title'>Update Water Tax</h5>
                        <button type='button' class='btn-close' data-bs-dismiss='modal'></button>
                    </div>
                    <div class='modal-body'>
                        <form action='update_water_tax.php' method='POST'>
                            <input type='hidden' id='updateRecordId' name='id'>
                            <input type='hidden' id='updatePropertyNumber' name='property_number'>
                            <div class='row'>
                                <div class='col-md-6 mb-3'>
                                    <label class='form-label'>Owner Name</label>
                                    <input type='text' class='form-control' id='updateOwnerName' readonly>
                                </div>
                                <div class='col-md-6 mb-3'>
                                    <label class='form-label'>Water Tax Type</label>
                                    <select class='form-select' id='updateWaterTaxType' name='water_tax_type'>
                                        <option value='General'>General</option>
                                        <option value='Special'>Special</option>
                                    </select>
                                </div>
                            </div>
                            <div class='row'>
                                <div class='col-md-6 mb-3'>
                                    <label class='form-label'>Previous Water Tax</label>
                                    <input type='number' class='form-control' id='updatePreviousWaterTax' name='previous_water_tax' step='0.01' required>
                                </div>
                                <div class='col-md-6 mb-3'>
                                    <label class='form-label'>Current Water Tax</label>
                                    <input type='number' class='form-control' id='updateWaterTax' name='water_tax' step='0.01' required>
                                </div>
                            </div>
                            <div class='text-end'>
                                <button type='submit' class='btn btn-primary'>Update Water Tax</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Edit record handlers
        document.querySelectorAll('.edit-record').forEach(button => {
            button.addEventListener('click', function() {
                // Get data from data attributes
                const id = this.getAttribute('data-id');
                const propertyNumber = this.getAttribute('data-property-number');
                const ownerName = this.getAttribute('data-owner-name');
                const waterTaxType = this.getAttribute('data-water-tax-type');
                const previousWaterTax = this.getAttribute('data-previous-water-tax');
                const waterTax = this.getAttribute('data-water-tax');
                
                // Populate modal fields
                document.getElementById('updateRecordId').value = id;
                document.getElementById('updatePropertyNumber').value = propertyNumber;
                document.getElementById('updateOwnerName').value = ownerName;
                document.getElementById('updateWaterTaxType').value = waterTaxType;
                document.getElementById('updatePreviousWaterTax').value = previousWaterTax;
                document.getElementById('updateWaterTax').value = waterTax;
            });
        });
        
        // View details handlers
        document.querySelectorAll('.view-details').forEach(button => {
            button.addEventListener('click', function() {
                // Get data from data attributes
                const propertyNumber = this.getAttribute('data-property-number');
                const ownerName = this.getAttribute('data-owner-name');
                const houseType = this.getAttribute('data-house-type');
                const transferName = this.getAttribute('data-transfer-name');
                const width = this.getAttribute('data-width');
                const areaSqft = this.getAttribute('data-area-sqft');
                
                // Update modal title
                document.querySelector('#propertyDetailsModal .modal-title').textContent = 
                    'Water Property Details - ' + propertyNumber;
                
                // Update modal content
                document.getElementById('modal-property-number').textContent = propertyNumber;
                document.getElementById('modal-owner-name').textContent = ownerName;
                document.getElementById('modal-house-type').textContent = houseType;
                document.getElementById('modal-transfer-name').textContent = transferName;
                document.getElementById('modal-width').textContent = width;
                document.getElementById('modal-area-sqft').textContent = areaSqft;
            });
        });
    });
    </script>
</body>
</html>