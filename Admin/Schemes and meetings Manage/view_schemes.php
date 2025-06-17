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

// Handle Delete Operation
if(isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    $delete_query = "DELETE FROM scheme_db WHERE id = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param("i", $id);
    
    if($stmt->execute()) {
        $success_message = "Scheme deleted successfully";
    } else {
        $error_message = "Error deleting scheme";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Schemes - Gram Panchayat</title>
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
        .logout-btn {
            background-color: #ff5252;
            border: none;
        }
        .logout-btn:hover {
            background-color: #ff1744;
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
                    <h1 class="h3 mb-0">View Schemes</h1>
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
                <h2 class="section-title">Government Schemes List</h2>

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
                        <h5 class="mb-0">Manage Schemes</h5>
                        <div>
                            <div class="btn-group me-2" role="group" aria-label="Language Toggle">
                                <button type="button" class="btn btn-outline-primary active" onclick="toggleLanguage('en')">English</button>
                                <button type="button" class="btn btn-outline-primary" onclick="toggleLanguage('mr')">मराठी</button>
                            </div>
                            <a href="add_scheme.php" class="btn btn-primary">
                                <i class="bi bi-plus-circle"></i> Add New Scheme
                            </a>
                        </div>
                    </div>
                </div>

                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                    <?php
                    $query = "SELECT * FROM scheme_db ORDER BY created_at DESC";
                    $result = mysqli_query($conn, $query);

                    while($row = mysqli_fetch_assoc($result)) {
                        ?>
                        <div class="col">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h5 class="card-title mb-0"><?php echo htmlspecialchars($row['scheme_name']); ?></h5>
                                </div>
                                <div class="card-body">
                                    <p class="card-text text-muted small">
                                        <i class="bi bi-calendar"></i> Start Date: <?php echo date('d-m-Y', strtotime($row['start_date'])); ?>
                                    </p>
                                    <div class="lang-en">
                                        <h6 class="mb-2">Description:</h6>
                                        <p class="card-text" style="height: 4.5rem; overflow: hidden;">
                                            <?php echo htmlspecialchars($row['description']); ?>
                                        </p>
                                        <button class="btn btn-link btn-sm p-0" type="button" data-bs-toggle="collapse" 
                                                data-bs-target="#description_en_<?php echo $row['id']; ?>">
                                            Read More
                                        </button>
                                        <div class="collapse" id="description_en_<?php echo $row['id']; ?>">
                                            <div class="card-text mt-2">
                                                <?php echo nl2br(htmlspecialchars($row['description'])); ?>
                                            </div>
                                        </div>
                                        <h6 class="mt-3 mb-2">Required Documents:</h6>
                                        <div class="card-text">
                                            <?php echo nl2br(htmlspecialchars($row['required_documents'])); ?>
                                        </div>
                                    </div>
                                    <div class="lang-mr" style="display: none;">
                                        <h6 class="mb-2">वर्णन:</h6>
                                        <p class="card-text" style="height: 4.5rem; overflow: hidden;">
                                            <?php echo htmlspecialchars($row['description_marathi']); ?>
                                        </p>
                                        <button class="btn btn-link btn-sm p-0" type="button" data-bs-toggle="collapse" 
                                                data-bs-target="#description_mr_<?php echo $row['id']; ?>">
                                            अधिक वाचा
                                        </button>
                                        <div class="collapse" id="description_mr_<?php echo $row['id']; ?>">
                                            <div class="card-text mt-2">
                                                <?php echo nl2br(htmlspecialchars($row['description_marathi'])); ?>
                                            </div>
                                        </div>
                                        <h6 class="mt-3 mb-2">आवश्यक कागदपत्रे:</h6>
                                        <div class="card-text">
                                            <?php echo nl2br(htmlspecialchars($row['required_documents_marathi'])); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer bg-transparent">
                                    <div class="d-flex justify-content-between">
                                        <a href="edit_scheme.php?id=<?php echo $row['id']; ?>" 
                                           class="btn btn-warning btn-sm">
                                            <i class="bi bi-pencil"></i> Edit
                                        </a>
                                        <button onclick="deleteScheme(<?php echo $row['id']; ?>)" 
                                                class="btn btn-danger btn-sm">
                                            <i class="bi bi-trash"></i> Delete
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                    ?>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleLanguage(lang) {
            document.querySelectorAll('.btn-group .btn').forEach(btn => btn.classList.remove('active'));
            event.target.classList.add('active');
            
            if (lang === 'en') {
                document.querySelectorAll('.lang-en').forEach(el => el.style.display = 'block');
                document.querySelectorAll('.lang-mr').forEach(el => el.style.display = 'none');
            } else {
                document.querySelectorAll('.lang-en').forEach(el => el.style.display = 'none');
                document.querySelectorAll('.lang-mr').forEach(el => el.style.display = 'block');
            }
        }

        function deleteScheme(id) {
            if(confirm('Are you sure you want to delete this scheme?')) {
                window.location.href = `view_schemes.php?delete_id=${id}`;
            }
        }
    </script>
</body>
</html>