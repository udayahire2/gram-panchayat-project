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

$recipient_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $recipient_name = $conn->real_escape_string($_POST['recipient_name']);
    $scheme_id = intval($_POST['scheme_id']);
    $recipient_id_new = $conn->real_escape_string($_POST['recipient_id']);

    $update_sql = "UPDATE scheme_recipients SET 
                   recipient_name = ?,
                   scheme_id = ?,
                   recipient_id = ?
                   WHERE sr_no = ?";

    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("sisi", $recipient_name, $scheme_id, $recipient_id_new, $recipient_id);

    if ($update_stmt->execute()) {
        header("Location: view_scheme_recipients.php?success=1");
        exit();
    } else {
        $error_message = "Error updating recipient: " . $update_stmt->error;
    }
    $update_stmt->close();
}

// Fetch recipient data
$sql = "SELECT * FROM scheme_recipients WHERE sr_no = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $recipient_id);
$stmt->execute();
$result = $stmt->get_result();
$recipient = $result->fetch_assoc();

if (!$recipient) {
    header("Location: view_scheme_recipients.php");
    exit();
}

// Fetch all schemes for dropdown
$schemes_query = "SELECT id, scheme_name FROM scheme_db WHERE status = 'active'";
$schemes_result = $conn->query($schemes_query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Recipient</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="container mt-5">
        <h2 class="text-center mb-4">Edit Recipient</h2>

        <form method="POST" class="col-md-6 mx-auto">
            <div class="mb-3">
                <label for="recipient_id" class="form-label">Recipient ID</label>
                <input type="text" class="form-control" id="recipient_id" name="recipient_id"
                    value="<?php echo htmlspecialchars($recipient['recipient_id']); ?>" required>
            </div>

            <div class="mb-3">
                <label for="recipient_name" class="form-label">Recipient Name</label>
                <input type="text" class="form-control" id="recipient_name" name="recipient_name"
                    value="<?php echo htmlspecialchars($recipient['recipient_name']); ?>" required>
            </div>

            <div class="mb-3">
                <label for="scheme_id" class="form-label">Scheme</label>
                <select class="form-select" id="scheme_id" name="scheme_id" required>
                    <?php while ($scheme = $schemes_result->fetch_assoc()): ?>
                        <option value="<?php echo $scheme['id']; ?>" <?php echo $recipient['scheme_id'] == $scheme['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($scheme['scheme_name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="text-center">
                <button type="submit" class="btn btn-primary">Update Recipient</button>
                <a href="view_scheme_recipients.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>