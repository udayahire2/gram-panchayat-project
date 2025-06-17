<?php
session_start();
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

// Handle delete operation
if (isset($_POST['delete_recipient'])) {
    $recipient_id = intval($_POST['recipient_id']);
    $delete_sql = "DELETE FROM scheme_recipients WHERE sr_no = ?";
    $delete_stmt = $conn->prepare($delete_sql);
    $delete_stmt->bind_param("i", $recipient_id);
    
    if ($delete_stmt->execute()) {
        $success_message = "Recipient deleted successfully";
    } else {
        $error_message = "Error deleting recipient: " . $delete_stmt->error;
    }
    $delete_stmt->close();
}

// Initialize variables for filtering
$scheme_filter = isset($_GET['scheme_id']) ? $_GET['scheme_id'] : '';
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$search_query = isset($_GET['search']) ? $_GET['search'] : '';

// Build the SQL query with filters
// Remove status filter from SQL query
$sql = "SELECT sr.*, s.scheme_name 
        FROM scheme_recipients sr 
        JOIN scheme_db s ON sr.scheme_id = s.id 
        WHERE 1=1";

if (!empty($scheme_filter)) {
    $sql .= " AND sr.scheme_id = " . intval($scheme_filter);
}

if (!empty($search_query)) {
    $sql .= " AND (sr.recipient_name LIKE '%" . $conn->real_escape_string($search_query) . "%')";
}

$sql .= " ORDER BY sr.created_at DESC";

$result = $conn->query($sql);

// Fetch all schemes for filter dropdown
$schemes_query = "SELECT id, scheme_name FROM scheme_db WHERE status = 'active'";
$schemes_result = $conn->query($schemes_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Scheme Recipients - Gram Panchayat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body { background-color: #f0f2f5; font-family: 'Segoe UI', sans-serif; }
        .dashboard-header { background: #1a237e; color: white; padding: 1rem; }
        .main-content { padding: 2rem 1rem; }
        .section-title { 
            color: #1a237e; 
            border-left: 4px solid #1a237e; 
            padding-left: 10px; 
            margin: 20px 0; 
        }
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            transition: transform 0.2s, box-shadow 0.2s;
            margin-bottom: 24px;
            padding: 20px;
        }
        .form-label { font-weight: 600; color: #333; }
        .btn-primary {
            background-color: #1a237e;
            border-color: #1a237e;
        }
        .btn-primary:hover {
            background-color: #3949ab;
            border-color: #3949ab;
        }
        .logout-btn {
            background-color: #ff5252;
            border: none;
        }
        .logout-btn:hover {
            background-color: #ff1744;
        }
        .table th { 
            background-color: #1a237e;
            color: white;
        }
        .table-hover tbody tr:hover {
            background-color: #f5f5f5;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="dashboard-header">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <img src="../Operator/logo.png" alt="Logo" style="height:48px; margin-right:15px; border-radius:50%;">
                    <h1 class="h3 mb-0">View Scheme Recipients</h1>
                </div>
                <div>
                    <a href="../Operator/operator_dashboard.php" class="btn btn-outline-light me-2">
                        <i class="bi bi-house-door"></i> Dashboard
                    </a>
                    <a href="../Operator/logout.php" class="btn btn-danger logout-btn">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <main class="col-12 main-content">
                <h2 class="section-title">Manage Scheme Recipients</h2>

                <?php if (isset($success_message)): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <?php echo $success_message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (isset($error_message)): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <?php echo $error_message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center py-3">
                        <h5 class="mb-0">Filter Recipients</h5>
                        <a href="add_scheme_recipients.php" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> Add New Recipient
                        </a>
                    </div>
                    <div class="card-body">
                        <form method="GET" class="row g-3">
                            <div class="col-md-6">
                                <label for="scheme_id" class="form-label">Select Scheme</label>
                                <select name="scheme_id" id="scheme_id" class="form-select">
                                    <option value="">All Schemes</option>
                                    <?php while($scheme = $schemes_result->fetch_assoc()): ?>
                                        <option value="<?php echo $scheme['id']; ?>" 
                                                <?php echo $scheme_filter == $scheme['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($scheme['scheme_name']); ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="search" class="form-label">Search Recipients</label>
                                <input type="text" name="search" id="search" class="form-control" 
                                       placeholder="Enter recipient name..." 
                                       value="<?php echo htmlspecialchars($search_query); ?>">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">&nbsp;</label>
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bi bi-search"></i> Search
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Scheme Name</th>
                                    <th>Recipient Name</th>
                                    <th>Recipient ID</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($result->num_rows > 0): ?>
                                    <?php while($row = $result->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($row['scheme_name']); ?></td>
                                            <td><?php echo htmlspecialchars($row['recipient_name']); ?></td>
                                            <td><?php echo htmlspecialchars($row['recipient_id']); ?></td>
                                            <td class="text-center">
                                                <form method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this recipient?');">
                                                    <input type="hidden" name="recipient_id" value="<?php echo $row['sr_no']; ?>">
                                                    <button type="submit" name="delete_recipient" class="btn btn-danger btn-sm">
                                                        <i class="bi bi-trash"></i> Delete
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center">No recipients found</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>