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

// Create meetings table if not exists
$sql = "CREATE TABLE IF NOT EXISTS meetings_db (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    title_marathi VARCHAR(200),
    description TEXT NOT NULL,
    description_marathi TEXT,
    meeting_date DATETIME NOT NULL,
    venue VARCHAR(200) NOT NULL,
    status ENUM('upcoming', 'completed', 'cancelled') DEFAULT 'upcoming',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql) !== TRUE) {
    echo "Error creating meetings table: " . $conn->error . "<br>";
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['add_meeting'])) {
        $title = $_POST['title'];
        $title_marathi = $_POST['title_marathi'] ?? $title;
        $description = $_POST['description'];
        $description_marathi = $_POST['description_marathi'] ?? $description;
        $meeting_date = $_POST['meeting_date'];
        $venue = $_POST['venue'];
        $status = $_POST['status'];

        $sql = "INSERT INTO meetings_db (title, title_marathi, description, description_marathi, meeting_date, venue, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssss", $title, $title_marathi, $description, $description_marathi, $meeting_date, $venue, $status);
        
        if ($stmt->execute()) {
            $success_message = "Meeting added successfully";
        } else {
            $error_message = "Error adding meeting: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Meeting - Gram Panchayat</title>
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
    </style>
</head>
<body>
    <!-- Header -->
    <div class="dashboard-header">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <img src="../Operator/logo.png" alt="Logo" style="height:48px; margin-right:15px; border-radius:50%;">
                    <h1 class="h3 mb-0">Add New Meeting</h1>
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
                <h2 class="section-title">Add New Meeting Details</h2>

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

                <div class="card">
                    <form method="POST">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="title" class="form-label">Meeting Title (English)</label>
                                <input type="text" class="form-control" id="title" name="title" required>
                            </div>
                            <div class="col-md-6">
                                <label for="title_marathi" class="form-label">Meeting Title (Marathi)</label>
                                <input type="text" class="form-control" id="title_marathi" name="title_marathi" readonly>
                            </div>
                            <div class="col-md-6">
                                <label for="meeting_status" class="form-label">Status</label>
                                <select class="form-select" id="meeting_status" name="status" required>
                                    <option value="upcoming">Upcoming</option>
                                    <option value="completed">Completed</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="meeting_date" class="form-label">Meeting Date & Time</label>
                                <input type="datetime-local" class="form-control" id="meeting_date" name="meeting_date" required>
                            </div>
                            <div class="col-12">
                                <label for="venue" class="form-label">Venue</label>
                                <input type="text" class="form-control" id="venue" name="venue" required>
                            </div>
                            <div class="col-12">
                                <label for="description" class="form-label">Description (English)</label>
                                <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                            </div>
                            <div class="col-12">
                                <label for="description_marathi" class="form-label">Description (Marathi)</label>
                                <textarea class="form-control" id="description_marathi" name="description_marathi" rows="3" readonly></textarea>
                            </div>
                            <div class="col-12 text-center mt-4">
                                <button type="submit" name="add_meeting" class="btn btn-primary btn-lg">
                                    <i class="bi bi-plus-circle me-2"></i>Add Meeting
                                </button>
                                <button type="reset" class="btn btn-secondary btn-lg ms-2">
                                    <i class="bi bi-x-circle me-2"></i>Reset
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-translation for Marathi fields
        document.getElementById('title').addEventListener('input', async (e) => {
            const text = e.target.value.trim();
            if (text) {
                try {
                    const response = await fetch(
                        `https://translate.googleapis.com/translate_a/single?client=gtx&sl=en&tl=mr&dt=t&q=${encodeURIComponent(text)}`
                    );
                    const data = await response.json();
                    document.getElementById('title_marathi').value = data[0][0][0];
                } catch (error) {
                    document.getElementById('title_marathi').value = 'Translation error occurred';
                }
            } else {
                document.getElementById('title_marathi').value = '';
            }
        });

        document.getElementById('description').addEventListener('input', async (e) => {
            const text = e.target.value.trim();
            if (text) {
                try {
                    const response = await fetch(
                        `https://translate.googleapis.com/translate_a/single?client=gtx&sl=en&tl=mr&dt=t&q=${encodeURIComponent(text)}`
                    );
                    const data = await response.json();
                    document.getElementById('description_marathi').value = data[0][0][0];
                } catch (error) {
                    document.getElementById('description_marathi').value = 'Translation error occurred';
                }
            } else {
                document.getElementById('description_marathi').value = '';
            }
        });
    </script>
</body>
</html>