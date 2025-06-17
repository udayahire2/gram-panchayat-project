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

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $query = "SELECT * FROM scheme_db WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $scheme = $result->fetch_assoc();
    $stmt->close();

    if (!$scheme) {
        header('Location: view_schemes.php');
        exit();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_scheme'])) {
    $id = $_POST['id'];
    $scheme_name = $_POST['scheme_name'];
    $description = trim($_POST['description']); // Add trim() to remove extra spaces
    $description_marathi = trim($_POST['description_marathi']);
    $required_documents = trim($_POST['required_documents']);
    $required_documents_marathi = trim($_POST['required_documents_marathi']);
    $start_date = $_POST['start_date'];
    $status = $_POST['status'];

    $update_query = "UPDATE scheme_db SET 
        scheme_name = ?,
        description = ?,
        description_marathi = ?,
        required_documents = ?,
        required_documents_marathi = ?,
        start_date = ?,
        status = ?
        WHERE id = ?";

    if ($stmt = $conn->prepare($update_query)) {
        $stmt->bind_param(
            "ssssssss", // Changed from "ssssss   si" to "ssssssss"
            $scheme_name,
            $description,
            $description_marathi,
            $required_documents,
            $required_documents_marathi,
            $start_date,
            $status,
            $id
        );

        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Scheme updated successfully!";
            header('Location: view_schemes.php');
            exit();
        } else {
            $error_message = "Error updating scheme: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $error_message = "Error preparing statement: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Scheme</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <!-- Reuse the same navigation -->

    <div class="container">
        <h2 class="text-center mb-4">Edit Scheme</h2>

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

        <form method="POST">
            <input type="hidden" name="id" value="<?php echo $scheme['id']; ?>">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Scheme Name</label>
                    <input type="text" class="form-control" name="scheme_name"
                        value="<?php echo htmlspecialchars($scheme['scheme_name']); ?>" required>
                </div>

            </div>
            <div class="col-md-6">
                <label class="form-label">Start Date</label>
                <input type="date" class="form-control" name="start_date"
                    value="<?php echo $scheme['start_date']; ?>" required>
            </div>
            <div class="col-12">
                <label class="form-label">Description (English)</label>
                <textarea class="form-control" name="description" rows="3" required>
                        <?php echo htmlspecialchars($scheme['description']); ?>
                    </textarea>
            </div>
            <div class="col-12">
                <label class="form-label">Description (Marathi)</label>
                <textarea class="form-control" name="description_marathi" rows="3">
                        <?php echo htmlspecialchars($scheme['description_marathi']); ?>
                    </textarea>
            </div>
            <div class="col-12">
                <label class="form-label">Required Documents (English)</label>
                <textarea class="form-control" name="required_documents" rows="3">
                        <?php echo htmlspecialchars($scheme['required_documents']); ?>
                    </textarea>
            </div>
            <div class="col-12">
                <label class="form-label">Required Documents (Marathi)</label>
                <textarea class="form-control" name="required_documents_marathi" rows="3">
                        <?php echo htmlspecialchars($scheme['required_documents_marathi']); ?>
                    </textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label">Status</label>
                <select class="form-select" name="status" required>
                    <option value="active" <?php echo ($scheme['status'] == 'active') ? 'selected' : ''; ?>>Active</option>
                    <option value="inactive" <?php echo ($scheme['status'] == 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                </select>
            </div>
            <div class="col-12 text-center mt-4">
                <button type="submit" name="update_scheme" class="btn btn-primary btn-lg">Update Scheme</button>
                <a href="view_schemes.php" class="btn btn-secondary btn-lg ms-2">Cancel</a>
            </div>
    </div>
    </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>