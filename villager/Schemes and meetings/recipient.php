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

// Fetch all schemes for filter dropdown
$schemes_query = "SELECT id, scheme_name FROM scheme_db";
$schemes_result = $conn->query($schemes_query);

// Build the query based on filters
$where_clause = "";
if (isset($_GET['scheme_id']) && !empty($_GET['scheme_id'])) {
    $scheme_id = $conn->real_escape_string($_GET['scheme_id']);
    $where_clause = "WHERE sr.scheme_id = '$scheme_id'";
}

// Fetch recipients with scheme details
$sql = "SELECT sr.*, s.scheme_name 
        FROM scheme_recipients sr 
        LEFT JOIN scheme_db s ON sr.scheme_id = s.id 
        $where_clause 
        ORDER BY sr.created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scheme Recipients | Kusumba Grampanchayat</title>
    
    <!-- Preload Critical Assets -->
    <link rel="preload" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" as="style">
    <link rel="preload" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" as="style">
    
    <!-- Stylesheets -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    
    <style>
       /* Global Variables */
:root {
  --primary-color: #2c3e50;
  --secondary-color: #3498db;
  --accent-color: #f39c12;
  --success-color: #2ecc71;
  --danger-color: #e74c3c;
  --warning-color: #f1c40f;
  --info-color: #1abc9c;
  --light-color: #f8f9fa;
  --dark-color: #343a40;
  --text-color: #333333;
  --border-color: #dee2e6;
  --hover-color: #e9ecef;
  --shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  --transition: all 0.3s ease;
}
/* Header Styles */
header {
  background-color: var(--primary-color);
  color: white;
  box-shadow: var(--shadow);
  position: sticky;
  top: 0;
  z-index: 100;
}

.header-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem 3%;
  max-width: 1300px;
  margin: 0 auto;
}

.logo-container {
  display: flex;
  align-items: center;
}

.logo-img {
  height: 60px;
  margin-right: 15px;
  border-radius: 50px;
}

.logo-name {
  font-size: 1.4rem;
  font-weight: 600;
}

.nav-controls {
  display: flex;
  align-items: center;
  gap: 15px;
}

.login-btn {
  background-color: var(--secondary-color);
  color: white;
  border: none;
  padding: 8px 15px;
  border-radius: 4px;
  cursor: pointer;
  transition: var(--transition);
}

.login-btn:hover {
  background-color: #2980b9;
}

/* Navigation Styles */
.navbar {
  background-color: #ffffff;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
}

.nav-links {
  display: flex;
  justify-content: center;
  list-style: none;
  max-width: 1300px;
  margin: 0 auto;
  padding: 0 3%;
}

.nav-links li {
  position: relative;
}

.nav-links a {
  text-decoration: none;
  display: block;
  padding: 15px 20px;
  color: var(--primary-color);
  font-weight: 500;
  transition: var(--transition);
}

.nav-links a:hover,
.nav-links a.active {
  color: var(--secondary-color);
  background-color: var(--hover-color);
}

/* Dropdown Menu */
.dropdown {
  position: relative;
}

.dropdown-menu {
  position: absolute;
  top: 100%;
  left: 0;
  background-color: white;
  border-radius: 4px;
  box-shadow: var(--shadow);
  min-width: 180px;
  display: none;
  z-index: 10;
  list-style: none;
}

.dropdown:hover .dropdown-menu {
  display: block;
}

.dropdown-menu li a {
  display: block;
  padding: 10px;
  color: var(--primary-color);
  transition: var(--transition);
}

.dropdown-menu li a:hover {
  background-color: var(--hover-color);
}

/* Mobile Menu */
.mobile-menu-toggle {
  display: none;
  flex-direction: column;
  justify-content: space-between;
  width: 30px;
  height: 20px;
  cursor: pointer;
  z-index: 101;
}

.bar {
  height: 3px;
  width: 100%;
  background-color: var(--primary-color);
  border-radius: 3px;
  transition: var(--transition);
}

.mobile-menu-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.7);
  z-index: 99;
  display: none;
}


/* Recipients Container Styles */
.recipients-container {
  max-width: 1300px;
  margin: 30px auto;
  padding: 0 3%;
}

.recipients-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 25px;
  padding-bottom: 15px;
  border-bottom: 1px solid var(--border-color);
}

.recipients-header h1 {
  color: var(--primary-color);
  font-weight: 700;
  font-size: 1.8rem;
}

/* Filter Section Styles */
.filter-section {
  background-color: var(--light-color);
  padding: 20px;
  border-radius: 8px;
  margin-bottom: 25px;
  box-shadow: var(--shadow);
}

.filter-section .form-label {
  color: var(--primary-color);
}

.filter-section .form-select {
  border-color: var(--border-color);
  transition: var(--transition);
}

.filter-section .form-select:focus {
  border-color: var(--secondary-color);
  box-shadow: 0 0 0 0.25rem rgba(52, 152, 219, 0.25);
}

.filter-section .btn-primary {
  background-color: var(--secondary-color);
  border-color: var(--secondary-color);
  transition: var(--transition);
}

.filter-section .btn-primary:hover {
  background-color: #2980b9;
  border-color: #2980b9;
}

/* Table Container Styles */
.table-container {
  background-color: white;
  border-radius: 8px;
  box-shadow: var(--shadow);
  overflow: hidden;
}

.table {
  margin-bottom: 0;
}

.table thead {
  background-color: var(--primary-color);
  color: white;
}

.table thead th {
  padding: 12px 15px;
  font-weight: 600;
  border-bottom: none;
}

.table tbody td {
  padding: 12px 15px;
  vertical-align: middle;
}

.table tbody tr:hover {
  background-color: var(--hover-color);
}

.table .badge {
  padding: 5px 10px;
  font-weight: 500;
  border-radius: 20px;
  font-size: 0.75rem;
}

/* Language Toggle Styles */
.language-toggle {
  display: flex;
  gap: 10px;
}

.language-toggle .btn {
  padding: 6px 12px;
  border-radius: 4px;
  font-weight: 500;
  transition: var(--transition);
}

.language-toggle .btn-outline-primary {
  color: var(--secondary-color);
  border-color: var(--secondary-color);
}

.language-toggle .btn-outline-primary:hover,
.language-toggle .btn-outline-primary.active {
  background-color: var(--secondary-color);
  border-color: var(--secondary-color);
  color: white;
}

/* DataTables Customization */
div.dataTables_wrapper div.dataTables_filter input {
  margin-left: 0.5em;
  border: 1px solid var(--border-color);
  border-radius: 4px;
  padding: 0.375rem 0.75rem;
}

div.dataTables_wrapper div.dataTables_length select {
  width: auto;
  display: inline-block;
  border: 1px solid var(--border-color);
  border-radius: 4px;
  padding: 0.375rem 2.25rem 0.375rem 0.75rem;
}

.pagination .page-link {
  color: var(--secondary-color);
  border-color: var(--border-color);
}

.pagination .page-item.active .page-link {
  background-color: var(--secondary-color);
  border-color: var(--secondary-color);
}

.pagination .page-link:hover {
  background-color: var(--hover-color);
  border-color: var(--border-color);
  color: var(--secondary-color);
}

/* Alert Styles */
.alert-info {
  background-color: rgba(52, 152, 219, 0.1);
  color: var(--secondary-color);
  border-color: rgba(52, 152, 219, 0.2);
}

/* Responsive Styles */
@media (max-width: 991px) {
  .recipients-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 15px;
  }
  
  .language-toggle {
    align-self: flex-start;
  }
}

@media (max-width: 768px) {
  .recipients-container {
    padding: 0 20px;
    margin: 20px auto;
  }
  
  .recipients-header h1 {
    font-size: 1.5rem;
  }
  
  .filter-section {
    padding: 15px;
  }
  
  .filter-section .row {
    flex-direction: column;
  }
  
  .filter-section .col-md-2,
  .filter-section .col-md-4 {
    width: 100%;
    margin-bottom: 15px;
  }
}

@media (max-width: 576px) {
  .recipients-header h1 {
    font-size: 1.3rem;
  }
  
  .language-toggle .btn {
    padding: 5px 10px;
    font-size: 0.875rem;
  }
  
  .table thead {
    font-size: 0.85rem;
  }
  
  .table tbody {
    font-size: 0.85rem;
  }
}
    </style>
</head>
<body>
    <!-- Header Section -->
    <header>
        <div class="header-content">
            <div class="logo-container">
                <img src="../image/logo.png" alt="Gram Panchayat Logo" class="logo-img" />
                <h1 class="logo-name">Kusumba Grampanchayat</h1>
            </div>
            <div class="nav-controls">
                <button class="login-btn" onclick="handleLogin()">
                    <i class="bi bi-person"></i> Login
                </button>
            </div>
        </div>
    </header>

    <!-- Responsive Navbar -->
    <nav class="navbar">
    <div class="mobile-menu-toggle">
      <span class="bar"></span>
      <span class="bar"></span>
      <span class="bar"></span>
    </div>
    <ul class="nav-links">
      <li><a href="../Main/index.html" data-translate="nav_home">Home</a></li>
      <li><a href="../Main/about_village.html" data-translate="nav_about_village">About Village</a></li>
      <li><a href="../Main/about_us.html" data-translate="nav_about_us">About Us</a></li>
      <li><a href="../Main/gallery.php" data-translate="nav_gallery">Gallery</a></li>
      <li class="dropdown">
        <a href="#" data-translate="nav_notices">Notices</a>
        <ul class="dropdown-menu">
          <li><a href="../Schemes and meetings/view_schemes.php" data-translate="nav_schemes">Schemes</a></li>
          <li><a href="../Schemes and meetings/recipient.php" data-translate="nav_recipients">Recipients</a></li>
          <li><a href="../Schemes and meetings/view_meetings.php" data-translate="nav_meetings">Meetings</a></li>
        </ul>
      </li>
      <li class="dropdown">
        <a href="#" data-translate="nav_tax">Tax</a>
        <ul class="dropdown-menu">
          <li><a href="../Tax/home_tax.php" data-translate="nav_home_tax">Home Tax</a></li>
          <li><a href="../Tax/water_tax.php" data-translate="nav_water_tax">Water Tax</a></li>
          <li><a href="../Tax/sanitation_tax.php" data-translate="nav_sanitation_tax">Sanitation Tax</a></li>
        </ul>
      </li>
      <li class="dropdown">
        <a href="#" data-translate="nav_certificates">Certificates</a>
        <ul class="dropdown-menu">
          <li><a href="../Certificate/Birth_certificate.php" data-translate="nav_birth_certificate">Birth Certificate</a></li>
          <li><a href="../Certificate/Death_certificate.php" data-translate="nav_death_certificate">Death Certificate</a></li>
          <li><a href="../Certificate/Marriage_certificate.php" data-translate="nav_marriage_certificate">Marriage Certificate</a></li>
        </ul>
      </li>
    </ul>
  </nav>
  <div class="mobile-menu-overlay"></div>


<!-- Mobile menu overlay -->
<div class="mobile-menu-overlay"></div>


    <div class="recipients-container" style="margin-top: 20px;">
        <div class="recipients-header">
            <h1 class="mb-0">Scheme Recipients</h1>
            <div class="language-toggle btn-group">
                <button type="button" class="btn btn-outline-primary active" id="englishToggle">English</button>
                <button type="button" class="btn btn-outline-primary" id="marathiToggle">मराठी</button>
            </div>
        </div>

        <div class="filter-section">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <label for="scheme_id" class="form-label fw-bold">Filter by Scheme</label>
                    <select class="form-select" name="scheme_id" id="scheme_id">
                        <option value="">All Schemes</option>
                        <?php while($scheme = $schemes_result->fetch_assoc()): ?>
                            <option value="<?php echo $scheme['id']; ?>" 
                                <?php echo (isset($_GET['scheme_id']) && $_GET['scheme_id'] == $scheme['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($scheme['scheme_name']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-filter"></i> Apply Filter
                    </button>
                </div>
            </form>
        </div>

        <div class="table-container">
            <table id="recipientsTable" class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Sr. No</th>
                        <th>Recipient ID</th>
                        <th>Recipient Name</th>
                        <th>Scheme Name</th>
                        <th>Date Added</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['sr_no']); ?></td>
                                <td><?php echo htmlspecialchars($row['recipient_id']); ?></td>
                                <td><?php echo htmlspecialchars($row['recipient_name']); ?></td>
                                <td>
                                    <span class="badge bg-primary">
                                        <?php echo htmlspecialchars($row['scheme_name']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('d M, Y', strtotime($row['created_at'])); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center">
                                <div class="alert alert-info mb-0">
                                    <i class="bi bi-info-circle me-2"></i>No recipients found
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script>
        // Translations object
        const translations = {
            en: {
                pageTitle: 'Scheme Recipients',
                filterByScheme: 'Filter by Scheme',
                allSchemes: 'All Schemes',
                applyFilter: 'Apply Filter',
                srNo: 'Sr. No',
                recipientId: 'Recipient ID',
                recipientName: 'Recipient Name',
                schemeName: 'Scheme Name',
                dateAdded: 'Date Added',
                noRecipients: 'No recipients found',
                search: 'Search:',
                logoName: 'Kusumba Grampanchayat',
                login: 'Login',
                home: 'Home',
                aboutVillage: 'About Village',
                aboutUs: 'About Us',
                notices: 'Notices',
                schemes: 'Schemes',
                recipients: 'Recipients',
                meetings: 'Meetings',
                tax: 'Tax',
                homeTax: 'Home Tax',
                waterTax: 'Water Tax',
                sanitationTax: 'Sanitation Tax',
                certificates: 'Certificates',
                birthCertificate: 'Birth Certificate',
                deathCertificate: 'Death Certificate',
                marriageCertificate: 'Marriage Certificate'
            },
            mr: {
                pageTitle: 'योजना लाभार्थी',
                filterByScheme: 'योजनेनुसार फिल्टर करा',
                allSchemes: 'सर्व योजना',
                applyFilter: 'फिल्टर लागू करा',
                srNo: 'अनु. क्र.',
                recipientId: 'लाभार्थी आयडी',
                recipientName: 'लाभार्थीचे नाव',
                schemeName: 'योजनेचे नाव',
                dateAdded: 'जोडलेली तारीख',
                noRecipients: 'कोणतेही लाभार्थी सापडले नाहीत',
                search: 'शोधा:',
                logoName: 'कुसुंबा ग्रामपंचायत',
                login: 'लॉगिन',
                home: 'मुख्यपृष्ठ',
                aboutVillage: 'गावाविषयी',
                aboutUs: 'आमच्याबद्दल',
                notices: 'सूचना',
                schemes: 'योजना',
                recipients: 'लाभार्थी',
                meetings: 'बैठका',
                tax: 'कर',
                homeTax: 'घर कर',
                waterTax: 'पाणी कर',
                sanitationTax: 'स्वच्छता कर',
                certificates: 'प्रमाणपत्रे',
                birthCertificate: 'जन्म प्रमाणपत्र',
                deathCertificate: 'मृत्यू प्रमाणपत्र',
                marriageCertificate: 'विवाह प्रमाणपत्र'
            }
        };

        document.addEventListener('DOMContentLoaded', function() {
            const englishToggle = document.getElementById('englishToggle');
            const marathiToggle = document.getElementById('marathiToggle');
            let currentLanguage = 'en';

            // Function to update the language
            function updateLanguage(language) {
                currentLanguage = language;
                
                // Update toggle button states
                englishToggle.classList.toggle('active', language === 'en');
                marathiToggle.classList.toggle('active', language === 'mr');

                // Update page title
                document.querySelector('.recipients-header h1').textContent = translations[language].pageTitle;
                
                // Update filter section
                document.querySelector('label[for="scheme_id"]').textContent = translations[language].filterByScheme;
                document.querySelector('#scheme_id option[value=""]').textContent = translations[language].allSchemes;
                document.querySelector('.filter-section button').innerHTML = 
                    `<i class="bi bi-filter"></i> ${translations[language].applyFilter}`;

                // Update table headers
                const headers = document.querySelectorAll('#recipientsTable thead th');
                headers[0].textContent = translations[language].srNo;
                headers[1].textContent = translations[language].recipientId;
                headers[2].textContent = translations[language].recipientName;
                headers[3].textContent = translations[language].schemeName;
                headers[4].textContent = translations[language].dateAdded;

                // Update logo name and login button
                document.querySelector('.logo-name').textContent = translations[language].logoName;
                document.querySelector('.login-btn').innerHTML = 
                    `<i class="bi bi-person"></i> ${translations[language].login}`;

                // Update DataTables language
                const dataTable = $('#recipientsTable').DataTable();
                dataTable.destroy(); // Destroy existing instance

                // Reinitialize DataTables with new language settings
            $('#recipientsTable').DataTable({
                "pageLength": 10,
                "ordering": true,
                    "searching": true,
                    "responsive": true,
                    "language": {
                        "search": `<i class='bi bi-search'></i> ${translations[language].search}`,
                        "paginate": {
                            "next": "<i class='bi bi-chevron-right'></i>",
                            "previous": "<i class='bi bi-chevron-left'></i>"
                        },
                        "zeroRecords": translations[language].noRecipients,
                        "info": language === 'en' ? 
                            "Showing _START_ to _END_ of _TOTAL_ entries" :
                            "एकूण _TOTAL_ पैकी _START_ ते _END_ नोंदी दर्शवत आहे",
                        "infoEmpty": language === 'en' ? 
                            "No entries to show" :
                            "दर्शवण्यासाठी कोणत्याही नोंदी नाहीत",
                        "infoFiltered": language === 'en' ? 
                            "(filtered from _MAX_ total entries)" :
                            "(एकूण _MAX_ नोंदींमधून फिल्टर केलेले)"
                    }
                });

                // Update navigation menu items
                const navLinks = {
                    'Home': translations[language].home,
                    'About Village': translations[language].aboutVillage,
                    'About Us': translations[language].aboutUs,
                    'Notices': translations[language].notices,
                    'Schemes': translations[language].schemes,
                    'Recipients': translations[language].recipients,
                    'Meetings': translations[language].meetings,
                    'Tax': translations[language].tax,
                    'Home Tax': translations[language].homeTax,
                    'Water Tax': translations[language].waterTax,
                    'Sanitation Tax': translations[language].sanitationTax,
                    'Certificates': translations[language].certificates,
                    'Birth Certificate': translations[language].birthCertificate,
                    'Death Certificate': translations[language].deathCertificate,
                    'Marriage Certificate': translations[language].marriageCertificate
                };

                // Update all navigation links
                document.querySelectorAll('.nav-links a').forEach(link => {
                    const originalText = link.getAttribute('data-original-text') || link.textContent.trim();
                    if (!link.getAttribute('data-original-text')) {
                        link.setAttribute('data-original-text', originalText);
                    }
                    if (navLinks[originalText]) {
                        link.textContent = navLinks[originalText];
                    }
                });
            }

            // Event listeners for language toggle buttons
            englishToggle.addEventListener('click', () => updateLanguage('en'));
            marathiToggle.addEventListener('click', () => updateLanguage('mr'));

            // Initialize with English
            updateLanguage('en');

            // Store original text values when page loads
            document.querySelectorAll('.nav-links a').forEach(link => {
                const originalText = link.textContent.trim();
                link.setAttribute('data-original-text', originalText);
            });
        });
    </script>
</body>
</html>