<?php
session_start();

// Database configuration and connection
$host = "localhost";
$dbname = "kusumba_db";
$username = "root";
$password = "";

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    error_log("Connection failed: " . $e->getMessage());
    die("Connection failed. Please try again later.");
}

// Initialize error and success messages
$error_message = "";
$success_message = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_scheme'])) {
    // Sanitize inputs
    $scheme_name = trim($_POST['scheme_name']);
    $description = trim($_POST['description']);
    $description_marathi = trim($_POST['description_marathi']);
    $required_documents = trim($_POST['required_documents']);
    $required_documents_marathi = trim($_POST['required_documents_marathi']);
    $start_date = trim($_POST['start_date']);
    $status = $_POST['status'];

    // Validation checks
    if (empty($scheme_name) || empty($description) || empty($start_date)) {
        $error_message = "Scheme name, description, and start date are required.";
    } else {
        try {
            $stmt = $conn->prepare("INSERT INTO scheme_db 
                (scheme_name, description, description_marathi, required_documents, required_documents_marathi, start_date, status) 
                VALUES (:scheme_name, :description, :description_marathi, :required_documents, :required_documents_marathi, :start_date, :status)");

            $stmt->execute([
                ':scheme_name' => $scheme_name,
                ':description' => $description,
                ':description_marathi' => $description_marathi,
                ':required_documents' => $required_documents,
                ':required_documents_marathi' => $required_documents_marathi,
                ':start_date' => $start_date,
                ':status' => $status
            ]);

            $success_message = "Scheme added successfully! Redirecting...";
            header("refresh:2;url=view_schemes.php");
        } catch (Exception $e) {
            error_log($e->getMessage());
            $error_message = "An error occurred while processing your request. Please try again later.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Scheme - Gram Panchayat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        /* Copy the same styles from add_villagers.php */
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
    </style>
</head>
<body>
    <!-- Header -->
    <div class="dashboard-header">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <img src="../Operator/logo.png" alt="Logo" style="height:48px; margin-right:15px; border-radius:50%;">
                    <h1 class="h3 mb-0">Add New Scheme</h1>
                </div>
                <div>
                    <a href="../Operator/operator_dashboard.php" class="btn btn-outline-light me-2">
                        <i class="bi bi-house-door"></i> Dashboard
                    </a>
                  
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <main class="col-12 main-content">
                <h2 class="section-title">Add New Government Scheme</h2>

                <?php if (!empty($success_message)): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <?php echo $success_message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (!empty($error_message)): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <?php echo $error_message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="card">
                    <form method="POST" id="schemeForm">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="scheme_name" class="form-label">Scheme Name</label>
                                <input type="text" class="form-control" id="scheme_name" name="scheme_name" required>
                            </div>

                            <div class="col-md-6">
                                <label for="start_date" class="form-label">Start Date</label>
                                <input type="date" class="form-control" id="start_date" name="start_date" required>
                            </div>

                            <div class="col-12">
                                <label for="description" class="form-label">Description (English)</label>
                                <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                            </div>

                            <div class="col-12">
                                <label for="description_marathi" class="form-label">Description (Marathi)</label>
                                <textarea class="form-control" id="description_marathi" name="description_marathi" rows="3" readonly></textarea>
                            </div>

                            <div class="col-12">
                                <label for="required_documents" class="form-label">Required Documents (English)</label>
                                <textarea class="form-control" id="required_documents" name="required_documents" rows="3" 
                                    placeholder="Enter each document on a new line"></textarea>
                            </div>

                            <div class="col-12">
                                <label for="required_documents_marathi" class="form-label">Required Documents (Marathi)</label>
                                <textarea class="form-control" id="required_documents_marathi" name="required_documents_marathi" 
                                    rows="3" readonly></textarea>
                            </div>

                            <div class="col-md-6">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>

                            <div class="col-12 text-center mt-4">
                                <button type="submit" name="add_scheme" class="btn btn-primary btn-lg">
                                    <i class="bi bi-plus-circle me-2"></i>Add Scheme
                                </button>
                                <a href="view_schemes.php" class="btn btn-secondary btn-lg ms-2">
                                    <i class="bi bi-x-circle me-2"></i>Cancel
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-translation functionality
        const englishInputs = {
            'description': document.getElementById('description_marathi'),
            'required_documents': document.getElementById('required_documents_marathi')
        };

        async function translateText(text, targetOutput) {
            try {
                const response = await fetch(
                    `https://translate.googleapis.com/translate_a/single?client=gtx&sl=en&tl=mr&dt=t&q=${encodeURIComponent(text)}`
                );
                const data = await response.json();
                const translatedText = data[0][0][0];
                targetOutput.value = translatedText;
            } catch (error) {
                console.error('Translation error:', error);
                targetOutput.value = 'Translation error occurred';
            }
        }

        // Add event listeners for English inputs
        document.getElementById('description').addEventListener('input', (e) => {
            const text = e.target.value.trim();
            if (text) translateText(text, englishInputs['description']);
        });

        document.getElementById('required_documents').addEventListener('input', (e) => {
            const text = e.target.value.trim();
            if (text) translateText(text, englishInputs['required_documents']);
        });
    </script>
</body>
</html>