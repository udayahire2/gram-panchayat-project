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

// Handle meeting deletion
if (isset($_POST['delete_meeting'])) {
    $meeting_id = $_POST['meeting_id'];
    $delete_sql = "DELETE FROM meetings_db WHERE id = ?";
    $stmt = $conn->prepare($delete_sql);
    $stmt->bind_param("i", $meeting_id);

    if ($stmt->execute()) {
        $success_message = "Meeting deleted successfully";
    } else {
        $error_message = "Error deleting meeting: " . $stmt->error;
    }
    $stmt->close();
}

// Handle meeting update
if (isset($_POST['update_meeting'])) {
    $meeting_id = $_POST['meeting_id'];
    $title = $_POST['title'];
    $title_marathi = $_POST['title_marathi'];
    $description = $_POST['description'];
    $description_marathi = $_POST['description_marathi'];
    $meeting_date = $_POST['meeting_date'];
    $venue = $_POST['venue'];
    $status = $_POST['status'];

    $update_sql = "UPDATE meetings_db SET title=?, title_marathi=?, description=?, 
                   description_marathi=?, meeting_date=?, venue=?, status=? WHERE id=?";

    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param(
        "sssssssi",
        $title,
        $title_marathi,
        $description,
        $description_marathi,
        $meeting_date,
        $venue,
        $status,
        $meeting_id
    );

    if ($stmt->execute()) {
        $success_message = "Meeting updated successfully";
    } else {
        $error_message = "Error updating meeting: " . $stmt->error;
    }
    $stmt->close();
}

// Fetch all meetings
$sql = "SELECT * FROM meetings_db ORDER BY meeting_date DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Meetings - Gram Panchayat</title>
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
        }
        .card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.12);
        }
        .btn-primary {
            background-color: #1a237e;
            border-color: #1a237e;
        }
        .btn-primary:hover {
            background-color: #3949ab;
            border-color: #3949ab;
        }
        .badge-upcoming { background-color: #4CAF50; }
        .badge-completed { background-color: #2196F3; }
        .badge-cancelled { background-color: #f44336; }
        .badge {
            padding: 8px 12px;
            border-radius: 6px;
            color: white;
        }
        .modal-content {
            border-radius: 12px;
            border: none;
        }
        .modal-header {
            background-color: #1a237e;
            color: white;
            border-radius: 12px 12px 0 0;
        }
        .modal-header .btn-close {
            color: white;
            filter: brightness(0) invert(1);
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
                    <h1 class="h3 mb-0">View Meetings</h1>
                </div>
                <div>
                    <a href="../Operator/operator_dashboard.php" class="btn btn-outline-light me-2">
                        <i class="bi bi-house-door"></i> Dashboard
                    </a>
                    <a href="../Operator/logout.php" class="btn btn-danger">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <main class="col-12 main-content">
                <h2 class="section-title">Manage Meetings</h2>

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
                        <h5 class="mb-0">All Meetings</h5>
                        <a href="add_meeting.php" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> Add New Meeting
                        </a>
                    </div>
                </div>

                <div class="row">
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <div class="col-md-6 mb-4">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <h5 class="card-title"><?php echo htmlspecialchars($row['title']); ?></h5>
                                            <span class="badge badge-<?php echo $row['status']; ?>">
                                                <?php echo ucfirst($row['status']); ?>
                                            </span>
                                        </div>
                                        <h6 class="text-muted"><?php echo htmlspecialchars($row['title_marathi']); ?></h6>
                                        <p class="card-text"><?php echo htmlspecialchars($row['description']); ?></p>
                                        <p class="card-text"><small
                                                class="text-muted"><?php echo htmlspecialchars($row['description_marathi']); ?></small>
                                        </p>
                                        <div class="mt-3">
                                            <p><strong>Date & Time:</strong>
                                                <?php echo date('Y-m-d h:i A', strtotime($row['meeting_date'])); ?></p>
                                            <p><strong>Venue:</strong> <?php echo htmlspecialchars($row['venue']); ?></p>
                                        </div>
                                        <div class="mt-3 d-flex justify-content-end gap-2">
                                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#editModal<?php echo $row['id']; ?>">
                                                <i class="bi bi-pencil"></i> Edit
                                            </button>
                                            <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#deleteModal<?php echo $row['id']; ?>">
                                                <i class="bi bi-trash"></i> Delete
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Edit Modal -->
                            <div class="modal fade" id="editModal<?php echo $row['id']; ?>" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <form method="POST">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Edit Meeting</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <input type="hidden" name="meeting_id" value="<?php echo $row['id']; ?>">
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <label class="form-label">Title (English)</label>
                                                        <input type="text" class="form-control" name="title"
                                                            value="<?php echo htmlspecialchars($row['title']); ?>" required>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label">Title (Marathi)</label>
                                                        <input type="text" class="form-control" name="title_marathi"
                                                            value="<?php echo htmlspecialchars($row['title_marathi']); ?>">
                                                    </div>
                                                    <div class="col-12">
                                                        <label class="form-label">Description (English)</label>
                                                        <textarea class="form-control" name="description" rows="3" required><?php
                                                            echo htmlspecialchars($row['description']);
                                                        ?></textarea>
                                                    </div>
                                                    <div class="col-12">
                                                        <label class="form-label">Description (Marathi)</label>
                                                        <textarea class="form-control" name="description_marathi" rows="3"><?php
                                                            echo htmlspecialchars($row['description_marathi']);
                                                        ?></textarea>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label">Meeting Date & Time</label>
                                                        <input type="datetime-local" class="form-control" name="meeting_date"
                                                            value="<?php echo date('Y-m-d\TH:i', strtotime($row['meeting_date'])); ?>"
                                                            required>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label">Venue</label>
                                                        <input type="text" class="form-control" name="venue"
                                                            value="<?php echo htmlspecialchars($row['venue']); ?>" required>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label">Status</label>
                                                        <select class="form-select" name="status" required>
                                                            <option value="upcoming" <?php echo $row['status'] == 'upcoming' ? 'selected' : ''; ?>>Upcoming</option>
                                                            <option value="completed" <?php echo $row['status'] == 'completed' ? 'selected' : ''; ?>>Completed</option>
                                                            <option value="cancelled" <?php echo $row['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" name="update_meeting" class="btn btn-primary">
                                                    <i class="bi bi-save"></i> Update Meeting
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <!-- End Edit Modal -->

                            <!-- Delete Modal -->
                            <div class="modal fade" id="deleteModal<?php echo $row['id']; ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Delete Meeting</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p>Are you sure you want to delete this meeting?</p>
                                            <p><strong><?php echo htmlspecialchars($row['title']); ?></strong></p>
                                        </div>
                                        <div class="modal-footer">
                                            <form method="POST">
                                                <input type="hidden" name="meeting_id" value="<?php echo $row['id']; ?>">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" name="delete_meeting" class="btn btn-danger">Delete
                                                    Meeting</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="col-12">
                            <div class="alert alert-info">No meetings found.</div>
                        </div>
                    <?php endif; ?>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>