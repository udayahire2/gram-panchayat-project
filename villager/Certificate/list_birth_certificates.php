<?php
require_once '../login/session_check.php';

// Check login before allowing access
checkLogin();

// Database connection settings
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "kusumba_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch records
$sql = "SELECT id, name, gender, mother_name, father_name FROM birth_certificate";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Birth Certificate Records</title>
    <style>
        /* Add your styles here */
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
        }
        .view-details {
            color: #3498db;
            text-decoration: none;
        }
        .view-details:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <h1>Birth Certificate Records</h1>
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Gender</th>
                <th>Mother's Name</th>
                <th>Father's Name</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['name']; ?></td>
                        <td><?php echo $row['gender']; ?></td>
                        <td><?php echo $row['mother_name']; ?></td>
                        <td><?php echo $row['father_name']; ?></td>
                        <td><a href="view_birth_certificate.php?id=<?php echo $row['id']; ?>" class="view-details">View Details</a></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5">No records found</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>

<?php
$conn->close();
?>