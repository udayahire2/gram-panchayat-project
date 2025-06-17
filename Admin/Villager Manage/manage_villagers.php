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

// Search configuration
$search_query = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

// Build WHERE clause dynamically
$where_conditions = [];
$where_params = [];

if (!empty($search_query)) {
    $where_conditions[] = "(name LIKE ? OR email LIKE ? OR mobile LIKE ?)";
    $where_params[] = "%$search_query%";
    $where_params[] = "%$search_query%";
    $where_params[] = "%$search_query%";
}

// Construct WHERE clause
$where_clause = !empty($where_conditions) ? "WHERE " . implode(" AND ", $where_conditions) : "";

try {
    // Prepare records query with dynamic WHERE clause
    $sql = "SELECT * FROM villager $where_clause ORDER BY id DESC";
    $stmt = $conn->prepare($sql);
    
    // Bind parameters if any
    if (!empty($where_params)) {
        $param_types = str_repeat('s', count($where_params));
        $stmt->bind_param($param_types, ...$where_params);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();

} catch (Exception $e) {
    error_log("Database Error: " . $e->getMessage());
    echo "<div class='alert alert-danger'>An error occurred while fetching records. Please try again later.</div>";
    exit();
}

// Delete villager if delete request is sent
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_id'])) {
    $delete_id = $conn->real_escape_string($_POST['delete_id']);
    
    // Prepare delete statement
    $delete_stmt = $conn->prepare("DELETE FROM villager WHERE id = ?");
    $delete_stmt->bind_param("i", $delete_id);
    
    try {
        if ($delete_stmt->execute()) {
            // Redirect to same page with success message
            header("Location: manage_villagers.php?delete_success=true");
            exit();
        } else {
            // Handle deletion error
            $delete_error = "Failed to delete villager record.";
        }
    } catch (Exception $e) {
        $delete_error = "Error: " . $e->getMessage();
    }
    $delete_stmt->close();
}

// Update villager if update request is sent
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_id'])) {
    // Sanitize and validate input
    $update_id = $conn->real_escape_string($_POST['update_id']);
    $name = $conn->real_escape_string($_POST['edit_name']);
    $email = $conn->real_escape_string($_POST['edit_email']);
    $mobile = $conn->real_escape_string($_POST['edit_mobile']);
    $address = $conn->real_escape_string($_POST['edit_address']);
    
    // Prepare update statement
    $update_stmt = $conn->prepare("UPDATE villager SET name = ?, email = ?, mobile = ?, address = ? WHERE id = ?");
    $update_stmt->bind_param("ssssi", $name, $email, $mobile, $address, $update_id);
    
    try {
        if ($update_stmt->execute()) {
            // Redirect to same page with success message
            header("Location: manage_villagers.php?update_success=true");
            exit();
        } else {
            // Handle update error
            $update_error = "Failed to update villager record.";
        }
    } catch (Exception $e) {
        $update_error = "Error: " . $e->getMessage();
    }
    $update_stmt->close();
}

// Function to display success messages
function displaySuccessMessage($type) {
    $messages = [
        'update' => 'Villager record updated successfully!',
        'delete' => 'Villager record deleted successfully!'
    ];
    
    if (isset($_GET[$type . '_success'])) {
        echo "<div class='alert alert-success alert-dismissible fade show' role='alert'>
                {$messages[$type]}
                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
              </div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Villagers - Gram Panchayat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f0f2f5;
            font-family: 'Segoe UI', sans-serif;
        }
        .dashboard-header {
            background: #1a237e;
            color: white;
            padding: 1rem;
        }
        .main-content {
            padding: 2rem 1rem;
        }
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
        .table {
            border-collapse: separate;
            border-spacing: 0;
        }
        .table th {
            background-color: #f8f9fa;
            color: #1a237e;
            font-weight: 600;
        }
        .table td, .table th {
            vertical-align: middle;
        }
        .form-label {
            font-weight: 600;
            color: #333;
        }
        .logout-btn {
            background-color: #ff5252;
            border: none;
        }
        .logout-btn:hover {
            background-color: #ff1744;
        }
        .btn-primary {
            background-color: #1a237e;
            border-color: #1a237e;
        }
        .btn-primary:hover {
            background-color: #3949ab;
            border-color: #3949ab;
        }
        .search-box {
            position: relative;
        }
        .search-box .form-control {
            padding-left: 2.5rem;
            border-radius: 20px;
        }
        .search-box .bi-search {
            position: absolute;
            top: 10px;
            left: 12px;
            color: #6c757d;
        }
        .action-buttons .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
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
                    <h1 class="h3 mb-0">Manage Villagers</h1>
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
            <!-- Main Content -->
            <main class="col-12 main-content">
                <h2 class="section-title">Villager Records</h2>
                
                <?php 
                // Display success messages
                if (isset($_GET['update_success'])) {
                    displaySuccessMessage('update');
                }
                if (isset($_GET['delete_success'])) {
                    displaySuccessMessage('delete');
                }
                ?>

                <div class="card">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <a href="add_villagers.php" class="btn btn-primary">
                            <i class="bi bi-person-plus-fill me-2"></i>Add New Villager
                        </a>
                        <div class="search-box">
                            <i class="bi bi-search"></i>
                            <form method="GET" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                                <input type="text" class="form-control" placeholder="Search villagers..." 
                                       name="search" value="<?php echo htmlspecialchars($search_query); ?>">
                            </form>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Mobile</th>
                                    <th>Address</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<tr>
                                                <td>{$row['id']}</td>
                                                <td>{$row['name']}</td>
                                                <td>{$row['email']}</td>
                                                <td>{$row['mobile']}</td>
                                                <td>{$row['address']}</td>
                                                <td class='action-buttons'>
                                                    <button type='button' class='btn btn-sm btn-primary edit-btn' 
                                                            data-bs-toggle='modal' data-bs-target='#editModal' 
                                                            data-id='{$row['id']}' 
                                                            data-name='{$row['name']}' 
                                                            data-email='{$row['email']}' 
                                                            data-mobile='{$row['mobile']}' 
                                                            data-address='{$row['address']}'>
                                                        <i class='bi bi-pencil-square'></i>
                                                    </button>
                                                    <button type='button' class='btn btn-sm btn-danger delete-btn' 
                                                            data-bs-toggle='modal' data-bs-target='#deleteModal' 
                                                            data-id='{$row['id']}' 
                                                            data-name='{$row['name']}'>
                                                        <i class='bi bi-trash'></i>
                                                    </button>
                                                </td>
                                              </tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='6' class='text-center'>No villagers found</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Villager</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <div class="modal-body">
                        <input type="hidden" name="update_id" id="edit_id">
                        <div class="mb-3">
                            <label for="edit_name" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="edit_name" name="edit_name" required pattern="[A-Za-z\s]+" title="Only alphabets and spaces allowed">
                        </div>
                        <div class="mb-3">
                            <label for="edit_email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="edit_email" name="edit_email" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_mobile" class="form-label">Mobile Number</label>
                            <input type="tel" class="form-control" id="edit_mobile" name="edit_mobile" required pattern="[6-9]\d{9}" title="10 digit mobile number starting with 6-9">
                        </div>
                        <div class="mb-3">
                            <label for="edit_address" class="form-label">Address</label>
                            <textarea class="form-control" id="edit_address" name="edit_address" required rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete <span id="delete_name" class="fw-bold"></span>?</p>
                    <p class="text-danger">This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                        <input type="hidden" name="delete_id" id="delete_id">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Edit modal data population
            const editButtons = document.querySelectorAll('.edit-btn');
            editButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    const name = this.getAttribute('data-name');
                    const email = this.getAttribute('data-email');
                    const mobile = this.getAttribute('data-mobile');
                    const address = this.getAttribute('data-address');
                    
                    document.getElementById('edit_id').value = id;
                    document.getElementById('edit_name').value = name;
                    document.getElementById('edit_email').value = email;
                    document.getElementById('edit_mobile').value = mobile;
                    document.getElementById('edit_address').value = address;
                });
            });
            
            // Delete modal data population
            const deleteButtons = document.querySelectorAll('.delete-btn');
            deleteButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    const name = this.getAttribute('data-name');
                    
                    document.getElementById('delete_id').value = id;
                    document.getElementById('delete_name').textContent = name;
                });
            });
        });
    </script>
</body>
</html>