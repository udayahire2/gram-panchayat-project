<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "kusumba_db";

// डेटाबेस कनेक्शन
$conn = new mysqli($servername, $username, $password, $dbname);

// कनेक्शन तपासणी
if ($conn->connect_error) {
    die("कनेक्शन अयशस्वी: " . $conn->connect_error);
}

// फॉर्म सबमिट झाल्यावरच कोड चालेल
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $taxname = $_POST['property']; // कराचे नाव
    $taxAmount = floatval($_POST['taxAmount']); // कराची रक्कम

    // 🔹 टेबलमध्ये नवीन 3 फील्ड्स जोडणे (फक्त एकदाच)
    $sql = "ALTER TABLE property_tax 
            ADD COLUMN IF NOT EXISTS {$taxname}_previous DOUBLE NOT NULL DEFAULT 0,
            ADD COLUMN IF NOT EXISTS current_{$taxname}_tax DOUBLE NOT NULL DEFAULT 0,
            ADD COLUMN IF NOT EXISTS total_{$taxname}_tax DOUBLE NOT NULL DEFAULT 0";
    
    if ($conn->query($sql) === TRUE) {
        echo "<p>*Table updated successfully!*</p>";
    } else {
        echo "<p>Error updating table: " . $conn->error . "</p>";
    }

    // आधीच्या नोंदी तपासा
    $sql = "SELECT total_{$taxname}_tax FROM property_tax WHERE name = ?";
    $stmt = $conn->prepare($sql);
   // $stmt->bind_param("s", $taxname);
    //$stmt->execute();
    $result = $stmt->get_result();

    $previous_tax = 0;
    if ($row = $result->fetch_assoc()) {
        $previous_tax = $row["total_{$taxname}_tax"];
    }
    
    // नवीन कराची गणना
    $total_tax = $previous_tax + $taxAmount;

    // नवीन डेटा टेबलमध्ये टाका किंवा अपडेट करा
    $stmt = $conn->prepare("INSERT INTO property_tax (
        property_number, owner_name, transfer_name, house_type, 
        height, width, area_sqft, area_sqm, tax_rate, 
        previous_home_tax, current_home_tax, total_home_tax, 
        water_tax_type, previous_water_tax, water_tax, total_water_tax,
        previous_sanitation_tax, sanitation_tax, total_sanitation_tax,{$taxname}previous,current{$taxname}tax,total{$taxname}_tax
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?,?)");

    $stmt = $conn->prepare($sql);
    $city = "Unknown"; // Default value  
    $stmt->bind_param("ssddd", $taxname, $city, $previous_tax, $taxAmount, $total_tax);

    try {
        if ($stmt->execute()) {
            echo "<div class='alert alert-success'>
                <strong>Success!</strong> कराची नोंद यशस्वीपणे झाली.
                <a href='" . $_SERVER['PHP_SELF'] . "' class='btn btn-sm btn-outline-success ml-2'>पुन्हा नोंद करा</a>
            </div>";
        } else {
            throw new Exception("डेटाबेसमध्ये नोंद करताना समस्या: " . $stmt->error);
        }
    } catch (Exception $e) {
        error_log($e->getMessage()); // एरर लॉग करा
        echo "<div class='alert alert-danger'>
            <strong>त्रुटी:</strong> " . htmlspecialchars($e->getMessage()) . "
            <p>कृपया पुन्हा प्रयत्न करा किंवा सपोर्टशी संपर्क साधा.</p>
        </div>";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="mr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>नवीन कर जोडा</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            text-align: center;
            padding: 20px;
        }
        .container {
            width: 50%;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0,0,0,0.1);
            margin: auto;
        }
        input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            background-color: #007bff; /* 🔵 Changed to Blue */
            color: white;
            padding: 10px 15px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            font-size: 16px;
            margin: 5px;
        }
        button:hover {
            background-color: #0056b3; /* 🔵 Dark Blue on Hover */
        }
        .translate-btn {
            background-color: #007bff; /* 🔵 Translation Button also Blue */
        }
        .translate-btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <button class="translate-btn" onclick="translateText()">मराठी / English</button>

        <h2 id="heading">नवीन कर जोडा</h2>
        <form action="addnewTax.php" method="post">
            <label id="taxNameLabel" for="property">कराचे नाव :</label>
            <input type="text" id="property" name="property" required>

            <label id="taxAmountLabel" for="taxAmount">कराची रक्कम:</label>
            <input type="number" id="taxAmount" name="taxAmount" required>

            <button type="submit" id="addTaxBtn">कर जोडा</button>
        </form>
    </div>

    <script>
        let isMarathi = true; // Default language

        function translateText() {
            if (isMarathi) {
                document.getElementById("heading").innerText = "Add New Tax";
                document.getElementById("taxNameLabel").innerText = "Tax Name:";
                document.getElementById("taxAmountLabel").innerText = "Tax Amount:";
                document.getElementById("addTaxBtn").innerText = "Add Tax";
            } else {
                document.getElementById("heading").innerText = "नवीन कर जोडा";
                document.getElementById("taxNameLabel").innerText = "कराचे नाव :";
                document.getElementById("taxAmountLabel").innerText = "कराची रक्कम:";
                document.getElementById("addTaxBtn").innerText = "कर जोडा";
            }
            isMarathi = !isMarathi; // Toggle language
        }
    </script>
</body>
</html>