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

// Create scheme_recipients table if not exists
$sql = "CREATE TABLE IF NOT EXISTS scheme_recipients (
    sr_no INT(11) AUTO_INCREMENT PRIMARY KEY,
    recipient_id VARCHAR(50) NOT NULL UNIQUE,
    scheme_id INT(11),
    recipient_name VARCHAR(200) NOT NULL,
    recipient_name_marathi VARCHAR(200) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (scheme_id) REFERENCES scheme_db(id) ON DELETE SET NULL
)";

if ($conn->query($sql) !== TRUE) {
    echo "Error creating recipients table: " . $conn->error;
}

$success_message = "";
$error_message = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $scheme_id = $conn->real_escape_string($_POST['scheme_id']);
    $recipient_name = $conn->real_escape_string($_POST['recipient_name']);
    $recipient_name_marathi = $conn->real_escape_string($_POST['recipient_name_marathi']);
    $recipient_id = $conn->real_escape_string($_POST['recipient_id']);

    $sql = "INSERT INTO scheme_recipients (recipient_id, scheme_id, recipient_name, recipient_name_marathi) 
            VALUES (?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("siss", $recipient_id, $scheme_id, $recipient_name, $recipient_name_marathi);

    if ($stmt->execute()) {
        $success_message = "Recipient added successfully!";
    } else {
        $error_message = "Error: " . $stmt->error;
    }
    $stmt->close();
}

// Fetch active schemes for dropdown
$schemes_query = "SELECT id, scheme_name FROM scheme_db WHERE status = 'active'";
$schemes_result = $conn->query($schemes_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Scheme Recipients - Gram Panchayat</title>
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
        .scheme-details {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
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
                    <h1 class="h3 mb-0">Add Scheme Recipients</h1>
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
                <h2 class="section-title">Add New Scheme Recipient</h2>

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
                    <form method="POST" id="recipientForm">
                        <div class="scheme-details" id="schemeInfo" style="display: none;">
                            <h5 class="mb-3">Selected Scheme Details</h5>
                            <div id="schemeDescription" class="mb-3"></div>
                            <div>
                                <strong>Required Documents:</strong>
                                <ul id="requiredDocuments" class="mt-2"></ul>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="scheme_id" class="form-label">Select Scheme</label>
                                <select class="form-select" id="scheme_id" name="scheme_id" required>
                                    <option value="">Choose a scheme...</option>
                                    <?php while ($scheme = $schemes_result->fetch_assoc()): ?>
                                        <option value="<?php echo $scheme['id']; ?>">
                                            <?php echo htmlspecialchars($scheme['scheme_name']); ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="recipient_id" class="form-label">Recipient ID</label>
                                <input type="text" class="form-control" id="recipient_id" name="recipient_id" required>
                            </div>

                            <div class="col-md-6">
                                <label for="recipient_name" class="form-label">Recipient Name (English)</label>
                                <input type="text" class="form-control" id="recipient_name" name="recipient_name" required>
                            </div>

                            <div class="col-md-6">
                                <label for="recipient_name_marathi" class="form-label">Recipient Name (Marathi)</label>
                                <input type="text" class="form-control" id="recipient_name_marathi" name="recipient_name_marathi" readonly>
                            </div>

                            <div class="col-12 text-center mt-4">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="bi bi-plus-circle me-2"></i>Add Recipient
                                </button>
                                <a href="view_scheme_recipients.php" class="btn btn-secondary btn-lg ms-2">
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
        // Auto-translation for Marathi name
        document.getElementById('recipient_name').addEventListener('input', async (e) => {
            const text = e.target.value.trim();
            if (text) {
                try {
                    const response = await fetch(
                        `https://translate.googleapis.com/translate_a/single?client=gtx&sl=en&tl=mr&dt=t&q=${encodeURIComponent(text)}`
                    );
                    const data = await response.json();
                    document.getElementById('recipient_name_marathi').value = data[0][0][0];
                } catch (error) {
                    console.error('Translation error:', error);
                    document.getElementById('recipient_name_marathi').value = 'Translation error occurred';
                }
            }
        });

        // Fetch and display scheme details
        document.getElementById('scheme_id').addEventListener('change', async (e) => {
            const schemeId = e.target.value;
            if (schemeId) {
                try {
                    const response = await fetch(`get_scheme_details.php?id=${schemeId}`);
                    const data = await response.json();
                    
                    document.getElementById('schemeDescription').innerHTML = `
                        <strong>Description:</strong><br>
                        ${data.description}
                    `;
                    
                    const documentsList = data.required_documents
                        .split('\n')
                        .filter(doc => doc.trim())
                        .map(doc => `<li>${doc.trim()}</li>`)
                        .join('');
                    
                    document.getElementById('requiredDocuments').innerHTML = documentsList;
                    document.getElementById('schemeInfo').style.display = 'block';
                } catch (error) {
                    console.error('Error fetching scheme details:', error);
                }
            } else {
                document.getElementById('schemeInfo').style.display = 'none';
            }
        });
    </script>
</body>
</html>