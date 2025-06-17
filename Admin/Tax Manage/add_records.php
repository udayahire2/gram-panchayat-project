<?php
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

// SQL to create table (uncommented)
$sql = "CREATE TABLE IF NOT EXISTS property_tax (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    property_number VARCHAR(255) NOT NULL PRIMARY KEY,
    owner_name VARCHAR(255),
    transfer_name VARCHAR(255),
    house_type VARCHAR(255),
    height DOUBLE,
    width DOUBLE,
    area_sqft DOUBLE,
    area_sqm DOUBLE,
    tax_rate DOUBLE,
    previous_home_tax DOUBLE,
    current_home_tax DOUBLE,
    total_home_tax DOUBLE,
    water_tax_type VARCHAR(255),
    previous_water_tax DOUBLE,
    water_tax DOUBLE,
    total_water_tax DOUBLE,
    previous_sanitation_tax DOUBLE,
    sanitation_tax DOUBLE,
    total_sanitation_tax DOUBLE
)";

// if ($conn->query($sql) === TRUE) {
//     echo "Table created successfully or already exists<br>";
// } else {
//     echo "Error creating table: " . $conn->error . "<br>";
// }

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form data
    $property_number = $_POST['propertyNumber'];
    $owner_name = $_POST['ownerName'];
    $transfer_name = $_POST['transferName'];
    $house_type = $_POST['houseDesc'];
    $height = floatval($_POST['height']);
    $width = floatval($_POST['width']);
    $area_sqft = floatval($_POST['areaSqft']);
    $area_sqm = floatval($_POST['areaSqM']);
    $tax_rate = floatval($_POST['rateTax']);
    $previous_home_tax = floatval($_POST['previousHomeTax']);
    $current_home_tax = floatval($_POST['currentHomeTax']);
    $total_home_tax = floatval($_POST['totalHomeTax']);
    $water_tax_type = $_POST['waterTaxType'];
    $previous_water_tax = floatval($_POST['previousWaterTax']);
    $water_tax = floatval($_POST['waterTax']);
    $total_water_tax = floatval($_POST['totalWaterTax']);
    $previous_sanitation_tax = floatval($_POST['previousSanitationTax']);
    $sanitation_tax = floatval($_POST['sanitationTax']);
    $total_sanitation_tax = floatval($_POST['totalSanitationTax']);

    // Validate critical inputs
    $errors = [];

    // Check for empty or invalid critical fields
    if (empty($property_number)) {
        $errors[] = "Property Number is required";
    }

    if (empty($owner_name)) {
        $errors[] = "Owner Name is required";
    }

    if ($height <= 0 || $width <= 0) {
        $errors[] = "Height and Width must be positive numbers";
    }

    // Check for duplicate property number
    $check_stmt = $conn->prepare("SELECT property_number FROM property_tax WHERE property_number = ?");
    $check_stmt->bind_param("s", $property_number);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        $errors[] = "Property Number already exists";
    }
    $check_stmt->close();

    // If there are validation errors, display them
    if (!empty($errors)) {
        echo "<div class='alert alert-danger'>";
        echo "<strong>Please correct the following errors:</strong><br>";
        foreach ($errors as $error) {
            echo "- " . htmlspecialchars($error) . "<br>";
        }
        echo "</div>";
    } else {
        // Prepare SQL statement
        $stmt = $conn->prepare("INSERT INTO property_tax (
            property_number, owner_name, transfer_name, house_type, 
            height, width, area_sqft, area_sqm, tax_rate, 
            previous_home_tax, current_home_tax, total_home_tax, 
            water_tax_type, previous_water_tax, water_tax, total_water_tax,
            previous_sanitation_tax, sanitation_tax, total_sanitation_tax
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        // Bind parameters
        $stmt->bind_param(
            "ssssddddddddsdddddd",
            $property_number,
            $owner_name,
            $transfer_name,
            $house_type,
            $height,
            $width,
            $area_sqft,
            $area_sqm,
            $tax_rate,
            $previous_home_tax,
            $current_home_tax,
            $total_home_tax,
            $water_tax_type,
            $previous_water_tax,
            $water_tax,
            $total_water_tax,
            $previous_sanitation_tax,
            $sanitation_tax,
            $total_sanitation_tax
        );

        // Execute the statement with detailed error handling
        try {
            if ($stmt->execute()) {
                // Clear POST data to prevent resubmission
                $_POST = [];
                echo "<div class='alert alert-success'>
                    <strong>Success!</strong> Record saved successfully.
                    <a href='" . $_SERVER['PHP_SELF'] . "' class='btn btn-sm btn-outline-success ml-2'>Add Another Record</a>
                </div>";
            } else {
                throw new Exception("Database insertion failed: " . $stmt->error);
            }
        } catch (Exception $e) {
            error_log($e->getMessage()); // Log error for server-side tracking
            echo "<div class='alert alert-danger'>
                <strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "
                <p>Please try again or contact support if the problem persists.</p>
            </div>";
        }

        // Close statement
        $stmt->close();
    }
}

// Close connection at the end of the script
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Property Tax Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        /* Global Styles */
        body {
            font-family: poppins;
            background-color: #ccddea;
            line-height: 1.6;
            color: #333;
        }

        /* Container Styles */
        .container {
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            padding: 30px;
            max-width: 900px;
        }

        /* Form Group Styles */
        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 0.5rem;
        }

        .form-control {
            border-radius: 6px;
            transition: all 0.3s ease;
            border-color: #ced4da;
        }

        .form-control:focus {
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
            border-color: #28a745;
        }

        /* Button Styles */
        .btn-primary {
            background-color: #28a745;
            border-color: #28a745;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #218838;
            border-color: #1e7e34;
        }

        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
        }

        .btn-info {
            background-color: #17a2b8;
            border-color: #17a2b8;
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .container {
                padding: 15px;
            }
        }

        /* Print Styles */
        @media print {
            body {
                background-color: white;
            }

            .btn {
                display: none;
            }

            .container {
                box-shadow: none;
                max-width: 100%;
                padding: 0;
            }
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Add Records</h2>
            <a href="../Clerk/clerk_dashboard.php" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to Dashboard
            </a>
        </div>

        <form id="propertyTaxForm" method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <div class="row">
                <div class="col-md-4 form-group">
                    <label for="propertyNumber" class="form-label">House Number</label>
                    <input type="text" class="form-control" id="propertyNumber" name="propertyNumber" required>
                </div>
                <div class="col-md-4 form-group">
                    <label for="ownerName" class="form-label">House Owner Name</label>
                    <input type="text" class="form-control" id="ownerName" name="ownerName" required>
                </div>
                <div class="col-md-4 form-group">
                    <label for="transferName" class="form-label">Name of Transfer</label>
                    <input type="text" class="form-control" id="transferName" name="transferName" required>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 form-group">
                    <label for="houseDesc" class="form-label">House Description</label>
                    <select id="houseDesc" class="form-select" name="houseDesc"
                        onchange="setTaxRate(); setSanitationTax()">
                        <option value="Concrete House">Concrete House</option>
                        <option value="Raw House">Raw House</option>
                        <option value="RCC">RCC</option>
                        <option value="Open Place">Open Place</option>
                        <option value="Letter House">Letter House</option>
                    </select>
                </div>
                <div class="col-md-3 form-group">
                    <label for="height" class="form-label">Height</label>
                    <input type="number" class="form-control" id="height" name="height" required
                        onchange="calculateArea()">
                </div>
                <div class="col-md-3 form-group">
                    <label for="width" class="form-label">Width</label>
                    <input type="number" class="form-control" id="width" name="width" required
                        onchange="calculateArea()">
                </div>
            </div>

            <div class="row">
                <div class="col-md-3 form-group">
                    <label for="areaSqft" class="form-label">Area (sq.ft)</label>
                    <input type="number" class="form-control" id="areaSqft" name="areaSqft" readonly>
                </div>
                <div class="col-md-3 form-group">
                    <label for="areaSqM" class="form-label">Area (sq.m)</label>
                    <input type="number" class="form-control" id="areaSqM" name="areaSqM" readonly>
                </div>
                <div class="col-md-6 form-group">
                    <label for="rateTax" class="form-label">Rate of Tax</label>
                    <input type="number" class="form-control" id="rateTax" name="rateTax" step="0.01" readonly>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 form-group">
                    <label for="previousHomeTax" class="form-label">Previous Home Tax</label>
                    <input type="number" class="form-control" id="previousHomeTax" name="previousHomeTax" value="0"
                        required>
                </div>
                <div class="col-md-4 form-group">
                    <label for="currentHomeTax" class="form-label">Current Home Tax</label>
                    <input type="number" class="form-control" id="currentHomeTax" name="currentHomeTax" readonly>
                </div>
                <div class="col-md-4 form-group">
                    <label for="totalHomeTax" class="form-label">Total Home Tax</label>
                    <input type="number" class="form-control" id="totalHomeTax" name="totalHomeTax" readonly>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 form-group">
                    <label for="waterTaxType" class="form-label">Water Tax Type</label>
                    <select id="waterTaxType" class="form-select" name="waterTaxType" onchange="setWaterTax()">
                        <option value="General">General</option>
                        <option value="Special">Special</option>
                    </select>
                </div>
                <div class="col-md-6 form-group">
                    <label for="previousWaterTax" class="form-label">Previous Water Tax</label>
                    <input type="number" class="form-control" id="previousWaterTax" name="previousWaterTax" value="0"
                        required>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 form-group">
                    <label for="waterTax" class="form-label">Water Tax</label>
                    <input type="number" class="form-control" id="waterTax" name="waterTax" readonly>
                </div>
                <div class="col-md-6 form-group">
                    <label for="totalWaterTax" class="form-label">Total Water Tax</label>
                    <input type="number" class="form-control" id="totalWaterTax" name="totalWaterTax" readonly>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 form-group">
                    <label for="previousSanitationTax" class="form-label">Previous Sanitation Tax</label>
                    <input type="number" class="form-control" id="previousSanitationTax" name="previousSanitationTax"
                        value="0" required>
                </div>
                <div class="col-md-4 form-group">
                    <label for="sanitationTax" class="form-label">Sanitation Tax</label>
                    <input type="number" class="form-control" id="sanitationTax" name="sanitationTax" readonly>
                </div>
                <div class="col-md-4 form-group">
                    <label for="totalSanitationTax" class="form-label">Total Sanitation Tax</label>
                    <input type="number" class="form-control" id="totalSanitationTax" name="totalSanitationTax"
                        readonly>
                </div>
            </div>

            <div class="text-center mt-4">
                <button type="submit" class="btn btn-primary">Save Record</button>
                <button type="reset" class="btn btn-secondary">Reset</button>
                <button type="button" class="btn btn-info" onclick="window.print()">Print</button>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function calculateArea() {
            const height = parseFloat(document.getElementById('height').value) || 0;
            const width = parseFloat(document.getElementById('width').value) || 0;

            // Calculate area in sq.ft
            const areaSqft = height * width;
            document.getElementById('areaSqft').value = areaSqft.toFixed(2);

            // Calculate area in sq.m (1 sq.ft = 0.092903 sq.m)
            const areaSqm = areaSqft * 0.092903;
            document.getElementById('areaSqM').value = areaSqm.toFixed(2);

            // Trigger tax calculation
            calculateTax();
        }

        function setTaxRate() {
            const houseType = document.getElementById("houseDesc").value;
            let rate = 0;

            switch (houseType) {
                case "Concrete House":
                    rate = 1.95;
                    break;
                case "Raw House":
                    rate = 1.00;
                    break;
                case "RCC":
                    rate = 2.80;
                    break;
                case "Open Place":
                    rate = 0.50;
                    break;
                case "Letter House":
                    rate = 1.75;
                    break;
            }

            document.getElementById("rateTax").value = rate;
            calculateTax();
        }

        function setSanitationTax() {

            let sanitationTax = 50;



            document.getElementById("sanitationTax").value = sanitationTax.toFixed(2);
            calculateTax();
        }

        function setWaterTax() {
            const waterTaxType = document.getElementById("waterTaxType").value;
            let waterTax = waterTaxType === "General" ? 150 : 1500;
            document.getElementById("waterTax").value = waterTax;
            calculateTax();
        }

        function calculateTax() {
            const areaSqft = parseFloat(document.getElementById('areaSqft').value) || 0;
            const rateTax = parseFloat(document.getElementById('rateTax').value) || 0;
            const previousHomeTax = parseFloat(document.getElementById('previousHomeTax').value) || 0;
            const previousWaterTax = parseFloat(document.getElementById('previousWaterTax').value) || 0;
            const previousSanitationTax = parseFloat(document.getElementById('previousSanitationTax').value) || 0;
            const waterTaxType = document.getElementById('waterTaxType').value;
            //caculate home tax
            const currentHomeTax = areaSqft * rateTax;
            //calculate water tax
            const waterTax = waterTaxType === "General" ? 150 : 600;

            const sanitationTax = parseFloat(document.getElementById('sanitationTax').value) || 0;
            const totalHomeTax = previousHomeTax + currentHomeTax;
            const totalWaterTax = previousWaterTax + waterTax;
            const totalSanitationTax = previousSanitationTax + sanitationTax;

            document.getElementById('currentHomeTax').value = currentHomeTax.toFixed(2);
            document.getElementById('totalHomeTax').value = totalHomeTax.toFixed(2);
            document.getElementById('waterTax').value = waterTax.toFixed(2);
            document.getElementById('totalWaterTax').value = totalWaterTax.toFixed(2);
            document.getElementById('sanitationTax').value = sanitationTax.toFixed(2);
            document.getElementById('totalSanitationTax').value = totalSanitationTax.toFixed(2);
        }

        // Initialize calculations
        window.onload = function() {
            setTaxRate();
            setWaterTax();
            setSanitationTax();
        };

        function calculateTotalTaxes() {
            const previousHomeTax = parseFloat(document.getElementById('previousHomeTax').value) || 0;
            const currentHomeTax = parseFloat(document.getElementById('currentHomeTax').value) || 0;
            const previousWaterTax = parseFloat(document.getElementById('previousWaterTax').value) || 0;
            const waterTax = parseFloat(document.getElementById('waterTax').value) || 0;
            const previousSanitationTax = parseFloat(document.getElementById('previousSanitationTax').value) || 0;
            const sanitationTax = parseFloat(document.getElementById('sanitationTax').value) || 0;

            // Calculate totals
            const totalHomeTax = previousHomeTax + currentHomeTax;
            const totalWaterTax = previousWaterTax + waterTax;
            const totalSanitationTax = previousSanitationTax + sanitationTax;

            // Update the total tax fields
            document.getElementById('totalHomeTax').value = totalHomeTax.toFixed(2);
            document.getElementById('totalWaterTax').value = totalWaterTax.toFixed(2);
            document.getElementById('totalSanitationTax').value = totalSanitationTax.toFixed(2);
        }

        // Attach event listeners to the relevant fields
        document.getElementById('previousHomeTax').addEventListener('input', calculateTotalTaxes);
        document.getElementById('currentHomeTax').addEventListener('input', calculateTotalTaxes);
        document.getElementById('previousWaterTax').addEventListener('input', calculateTotalTaxes);
        document.getElementById('waterTax').addEventListener('input', calculateTotalTaxes);
        document.getElementById('previousSanitationTax').addEventListener('input', calculateTotalTaxes);
        document.getElementById('sanitationTax').addEventListener('input', calculateTotalTaxes);
    </script>
</body>

</html>