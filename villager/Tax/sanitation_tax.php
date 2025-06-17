<?php

require_once '../login/session_check.php';

// Check login before allowing access
checkLogin();
// Only process the API request if it's a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Database connection configuration
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "kusumba_db";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die(json_encode([
            "success" => false,
            "message" => "Connection failed: " . $conn->connect_error
        ]));
    }

    // Set character set to UTF-8
    $conn->set_charset("utf8mb4");

    // Get search parameters from POST request
    $property_number = isset($_POST['property_number']) ? $_POST['property_number'] : '';
    $ownerName = isset($_POST['ownerName']) ? $_POST['ownerName'] : '';

    // Initialize response array
    $response = [
        "success" => false,
        "message" => "",
        "records" => []
    ];

    // Validate input - at least one search parameter must be provided
    if (empty($property_number) && empty($ownerName)) {
        $response["message"] = "Please provide either Property ID or Owner Name";
        echo json_encode($response);
        exit;
    }

    // Prepare SQL query based on provided parameters
    $sql = "SELECT * FROM property_tax WHERE 1=1";
    $params = [];
    $types = "";

    if (!empty($property_number)) {
        $sql .= " AND property_number = ?";
        $params[] = $property_number;
        $types .= "s";
    }

    if (!empty($ownerName)) {
        $sql .= " AND owner_name LIKE ?";
        $params[] = "%$ownerName%";
        $types .= "s";
    }

    // Order by most recent records first
    $sql .= " ORDER BY property_number DESC";

    // Prepare and execute the statement
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $response["records"][] = $row;
            }
            $response["success"] = true;
            $response["message"] = "Records found: " . $result->num_rows;
        } else {
            $response["message"] = "No records found for the given search criteria";
        }
        
        $stmt->close();
    } else {
        $response["message"] = "Query preparation failed: " . $conn->error;
    }

    $conn->close();

    // Return JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
    exit; // Important: stop execution after sending JSON response
}
?>

<!DOCTYPE html>
<html lang="mr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>स्वच्छता कर माहिती पहा</title>
    <style>
   :root {
    --primary-color: #4CAF50; /* Green */
    --primary-dark: #388E3C; /* Darker Green */
    --secondary-color: #E8F5E9; /* Light Green */
    --border-color: #C8E6C9; /* Light Green Border */
    --text-color: #333;
    --error-color: #f44336;
    --font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: var(--font-family);
    background-color: #f5f5f5;
    color: var(--text-color);
    line-height: 1.6;
}

.wrapper {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding: 10px 0;
    border-bottom: 1px solid var(--border-color);
}

.container {
    background-color: white;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    padding: 30px;
    margin-bottom: 30px;
}

h2 {
    color: var(--primary-color);
    margin-bottom: 25px;
    text-align: center;
}

h3 {
    margin-bottom: 15px;
    color: var(--primary-color);
}

.form-group {
    margin-bottom: 20px;
}

label {
    display: block;
    margin-bottom: 8px;
    font-weight: bold;
}

input[type="text"] {
    width: 100%;
    padding: 12px;
    border: 1px solid var(--border-color);
    border-radius: 4px;
    font-size: 16px;
    transition: border-color 0.3s;
}

input[type="text"]:focus {
    border-color: var(--primary-color);
    outline: none;
    box-shadow: 0 0 5px rgba(76, 175, 80, 0.3);
}

.divider {
    text-align: center;
    margin: 20px 0;
    position: relative;
}

.divider:before,
.divider:after {
    content: "";
    display: inline-block;
    width: 40%;
    height: 1px;
    background-color: var(--border-color);
    position: absolute;
    top: 50%;
}

.divider:before {
    left: 0;
}

.divider:after {
    right: 0;
}

.btn-primary {
    background-color: var(--primary-color);
    color: white;
    border: none;
    padding: 12px 24px;
    border-radius: 4px;
    cursor: pointer;
    font-size: 16px;
    font-weight: bold;
    width: 100%;
    transition: background-color 0.3s;
}

.btn-primary:hover {
    background-color: var(--primary-dark);
}

.btn-primary:disabled {
    background-color: #cccccc;
    cursor: not-allowed;
}

.btn-back {
    background: none;
    border: none;
    color: var(--primary-color);
    cursor: pointer;
    font-size: 16px;
    display: flex;
    align-items: center;
    padding: 5px 10px;
}

.btn-back:hover {
    text-decoration: underline;
}

.lang-buttons {
    display: flex;
    gap: 10px;
}

.btn-language {
    background: none;
    border: 1px solid var(--primary-color);
    color: var(--primary-color);
    padding: 5px 15px;
    border-radius: 4px;
    cursor: pointer;
}

.btn-language.active {
    background-color: var(--primary-color);
    color: white;
}

.results-container {
    background-color: white;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    padding: 30px;
}

.results-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

.results-table th,
.results-table td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid var(--border-color);
}

.results-table th {
    background-color: var(--secondary-color);
    font-weight: bold;
}

.results-table tr:hover {
    background-color: #f9f9f9;
}

.alert {
    padding: 15px;
    margin-bottom: 20px;
    border: 1px solid transparent;
    border-radius: 4px;
    color: #721c24;
    background-color: #f8d7da;
    border-color: #f5c6cb;
}

.loading {
    display: none;
    text-align: center;
    margin: 20px 0;
}

.spinner {
    border: 4px solid rgba(0, 0, 0, 0.1);
    border-left-color: var(--primary-color);
    border-radius: 50%;
    width: 30px;
    height: 30px;
    animation: spin 1s linear infinite;
    margin: 0 auto 10px;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

/* Media queries for responsive design */
@media screen and (max-width: 768px) {
    .container, .results-container {
        padding: 20px;
    }
    
    .results-table th,
    .results-table td {
        padding: 8px;
        font-size: 14px;
    }
    
    .header {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
    }
    
    .lang-buttons {
        align-self: flex-end;
    }
}

@media screen and (max-width: 480px) {
    .results-table {
        display: block;
        overflow-x: auto;
    }
    
    input[type="text"] {
        padding: 10px;
    }
    
    .btn-primary {
        padding: 10px 20px;
    }
}
</style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <button onclick="goToHomepage()" class="btn-back">
                ← <span id="backText">मुख्यपृष्ठावर परत जा</span>
            </button>
            <div class="lang-buttons">
                <button onclick="setLanguage('mr')" class="btn-language active">मराठी</button>
                <button onclick="setLanguage('en')" class="btn-language">English</button>
            </div>
        </div>
        
        <div class="container">
            <h2 id="searchTitle">स्वच्छता कर शोधा</h2>
            
            <div id="errorMessage" class="alert alert-error" style="display: none;"></div>
            
            <div class="form-group">
                <label for="property_number">मालमत्ता क्रमांक:</label>
                <input type="text" id="property_number" placeholder="मालमत्ता क्रमांक प्रविष्ट करा">
            </div>
            
            <div class="divider">किंवा</div>
            
            <div class="form-group">
                <label for="ownerName">मालकाचे नाव:</label>
                <input type="text" id="ownerName" placeholder="मालकाचे नाव प्रविष्ट करा">
            </div>
            
            <div id="loading" class="loading">
                <div class="spinner"></div>
                <div>शोधत आहे...</div>
            </div>
            
            <button onclick="searchRecords()" id="searchButton" class="btn-primary">शोधा</button>
        </div>
        
        <div id="resultsContainer" class="results-container" style="display: none;">
            <h3>शोध परिणाम</h3>
            <div id="resultsContent"></div>
        </div>
    </div>

    <script>
        function searchRecords() {
            const property_number = document.getElementById('property_number').value.trim();
            const ownerName = document.getElementById('ownerName').value.trim();
            const errorMessage = document.getElementById('errorMessage');
            const loading = document.getElementById('loading');
            const searchButton = document.getElementById('searchButton');
            
            // Reset error message
            errorMessage.style.display = 'none';
            
            if (!property_number && !ownerName) {
                errorMessage.textContent = 'कृपया मालमत्ता क्रमांक किंवा मालकाचे नाव प्रविष्ट करा';
                errorMessage.style.display = 'block';
                return;
            }
            
            // Show loading and disable button
            loading.style.display = 'block';
            searchButton.disabled = true;
            
            const formData = new FormData();
            formData.append('property_number', property_number);
            formData.append('ownerName', ownerName);
            
            fetch(window.location.href, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                loading.style.display = 'none';
                searchButton.disabled = false;
                
                if (!data.success) {
                    errorMessage.textContent = data.message;
                    errorMessage.style.display = 'block';
                    return;
                }
                
                displayResults(data);
            })
            .catch(error => {
                console.error('Error:', error);
                loading.style.display = 'none';
                searchButton.disabled = false;
                errorMessage.textContent = 'एक त्रुटी आली. कृपया पुन्हा प्रयत्न करा.';
                errorMessage.style.display = 'block';
            });
        }

        function displayResults(data) {
            const resultsContainer = document.getElementById('resultsContainer');
            const resultsContent = document.getElementById('resultsContent');
            resultsContent.innerHTML = '';
            
            if (!data.success) {
                resultsContent.innerHTML = `<div class="alert">${data.message}</div>`;
                resultsContainer.style.display = 'block';
                return;
            }
            
            const table = document.createElement('table');
            table.className = 'results-table';
            
            // Create table header
            const thead = document.createElement('thead');
            thead.innerHTML = `
                <tr>
                    <th>मालमत्ता क्र.</th>
                    <th>मालकाचे नाव</th>
                    <th>मागील कर</th>
                    <th>चालू कर</th>
                    <th>एकूण कर</th>
                </tr>
            `;
            table.appendChild(thead);
            
            // Create table body
            const tbody = document.createElement('tbody');
            data.records.forEach(record => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${record.property_number}</td>
                    <td>${record.owner_name}</td>
                    <td>₹${parseFloat(record.previous_sanitation_tax).toFixed(2)}</td>
                    <td>₹${parseFloat(record.sanitation_tax).toFixed(2)}</td>
                    <td>₹${parseFloat(record.total_sanitation_tax).toFixed(2)}</td>
                `;
                tbody.appendChild(row);
            });
            
            table.appendChild(tbody);
            resultsContent.appendChild(table);
            resultsContainer.style.display = 'block';
        }

        function goToHomepage() {
            window.location.href = '../Main/index.html';
        }
        const translations = {
        mr: {
            backText: "मुख्यपृष्ठावर परत जा",
            searchTitle: "स्वच्छता कर शोधा",
            propertyLabel: "मालमत्ता क्रमांक:",
            propertyPlaceholder: "मालमत्ता क्रमांक प्रविष्ट करा",
            ownerLabel: "मालकाचे नाव:",
            ownerPlaceholder: "मालकाचे नाव प्रविष्ट करा",
            searchButton: "शोधा",
            loading: "शोधत आहे...",
            resultsTitle: "शोध परिणाम",
            errorEmpty: "कृपया मालमत्ता क्रमांक किंवा मालकाचे नाव प्रविष्ट करा",
            errorGeneral: "एक त्रुटी आली. कृपया पुन्हा प्रयत्न करा.",
            propertyHeader: "मालमत्ता क्र.",
            ownerHeader: "मालकाचे नाव",
            prevTaxHeader: "मागील कर",
            currTaxHeader: "चालू कर",
            totalTaxHeader: "एकूण कर",
            or: "किंवा"
        },
        en: {
            backText: "Back to Homepage",
            searchTitle: "Search Sanitation Tax Records",
            propertyLabel: "Property Number:",
            propertyPlaceholder: "Enter property number",
            ownerLabel: "Owner Name:",
            ownerPlaceholder: "Enter owner name",
            searchButton: "Search",
            loading: "Searching...",
            resultsTitle: "Search Results",
            errorEmpty: "Please enter either Property Number or Owner Name",
            errorGeneral: "An error occurred. Please try again.",
            propertyHeader: "Property No.",
            ownerHeader: "Owner Name",
            prevTaxHeader: "Previous Tax",
            currTaxHeader: "Current Tax",
            totalTaxHeader: "Total Tax",
            or: "OR"
        }
    };

    function setLanguage(lang) {
        // Update active button
        document.querySelectorAll('.btn-language').forEach(btn => {
            btn.classList.remove('active');
        });
        document.querySelector(`.btn-language[onclick="setLanguage('${lang}')"]`).classList.add('active');

        // Update text content
        document.getElementById('backText').textContent = translations[lang].backText;
        document.getElementById('searchTitle').textContent = translations[lang].searchTitle;
        document.querySelector('label[for="property_number"]').textContent = translations[lang].propertyLabel;
        document.getElementById('property_number').placeholder = translations[lang].propertyPlaceholder;
        document.querySelector('label[for="ownerName"]').textContent = translations[lang].ownerLabel;
        document.getElementById('ownerName').placeholder = translations[lang].ownerPlaceholder;
        document.getElementById('searchButton').textContent = translations[lang].searchButton;
        document.querySelector('.divider').textContent = translations[lang].or;

        // Update loading text
        document.querySelector('#loading div:last-child').textContent = translations[lang].loading;

        // Update results title if visible
        if (document.getElementById('resultsContainer').style.display !== 'none') {
            document.querySelector('#resultsContainer h3').textContent = translations[lang].resultsTitle;

            // Update table headers if table exists
            const tableHeaders = document.querySelectorAll('#resultsContent table thead th');
            if (tableHeaders.length > 0) {
                tableHeaders[0].textContent = translations[lang].propertyHeader;
                tableHeaders[1].textContent = translations[lang].ownerHeader;
                tableHeaders[2].textContent = translations[lang].prevTaxHeader;
                tableHeaders[3].textContent = translations[lang].currTaxHeader;
                tableHeaders[4].textContent = translations[lang].totalTaxHeader;
            }
        }
    }

    // Initialize with default language
    setLanguage('mr');
    </script>
</body>
</html>