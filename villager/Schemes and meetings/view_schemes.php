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

// Fetch schemes
$sql = "SELECT * FROM scheme_db ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Village Schemes | Kusumba Grampanchayat</title>
    
    <!-- Preload Critical Assets -->
    <link rel="preload" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" as="style">
    <link rel="preload" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" as="style">
    
    <!-- Stylesheets -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<!-- Header Section -->
<header>
    <div class="header-content">
        <div class="logo-container">
            <img src="../image/logo.png" alt="Gram Panchayat Logo" class="logo-img" />
            <h1 class="logo-name" data-translate="logoName">Kusumba Grampanchayat</h1>
        </div>
        <div class="nav-controls">
            <button class="login-btn" onclick="handleLogin()" data-translate="login">
              Login
            </button>
        </div>
    </div>
</header>

<!-- Navigation Section -->

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


    <div class="schemes-container">
        <div class="schemes-header">
            <h1 class="mb-0">Village Schemes</h1>
            <div class="language-toggle btn-group" role="group">
                <button type="button" class="btn btn-outline-light active" id="englishToggle">English</button>
                <button type="button" class="btn btn-outline-light" id="marathiToggle">मराठी</button>
            </div>
        </div>

        <div class="row g-4">
            <?php
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    $statusClass = $row['status'] === 'active' ? 'bg-success' : 'bg-secondary';
                    
                    // Prepare multilingual content
                    $nameEn = htmlspecialchars($row['scheme_name']);
                    $nameMr = !empty($row['scheme_name_marathi']) ? htmlspecialchars($row['scheme_name_marathi']) : $nameEn;
                    $descEn = htmlspecialchars($row['description']);
                    $descMr = !empty($row['description_marathi']) ? htmlspecialchars($row['description_marathi']) : $descEn;

                    // Calculate scheme duration and progress
                    $startDate = new DateTime($row['start_date']);
                    $currentDate = new DateTime();
                    $interval = $startDate->diff($currentDate);
                    $durationDays = $interval->days;
                    $progressPercentage = min(100, max(0, ($durationDays / 365) * 100));

                    echo "<div class='col-md-4 mb-4'>";
                    echo "<div class='card scheme-card h-100 shadow-sm'>";
                    echo "<div class='scheme-card-header'>";
                    echo "<span class='badge {$statusClass} rounded-pill'>" . ucfirst($row['status']) . "</span>";
                    echo "<small class='text-white'>Created: " . date('M d, Y', strtotime($row['created_at'])) . "</small>";
                    echo "</div>";
                    echo "<div class='scheme-card-body'>";
                    echo "<h5 class='card-title schemes-title-en fw-bold mb-3'>{$nameEn}</h5>";
                    echo "<h5 class='card-title schemes-title-mr fw-bold mb-3' style='display:none;'>{$nameMr}</h5>";
                    echo "<p class='card-text schemes-desc-en text-muted mb-3'>" . (strlen($descEn) > 100 ? substr($descEn, 0, 100) . '...' : $descEn) . "</p>";
                    echo "<p class='card-text schemes-desc-mr text-muted mb-3' style='display:none;'>" . (strlen($descMr) > 100 ? substr($descMr, 0, 100) . '...' : $descMr) . "</p>";
                    echo "<div class='scheme-progress'>";
                    echo "<div class='scheme-progress-bar' role='progressbar' style='width: {$progressPercentage}%' aria-valuenow='{$progressPercentage}' aria-valuemin='0' aria-valuemax='100'></div>";
                    echo "</div>";
                    echo "</div>";
                    echo "<div class='card-footer bg-transparent border-0 d-flex justify-content-end p-3'>";
                    echo "<a href='#' class='text-primary view-details' data-bs-toggle='modal' data-bs-target='#schemeDetailsModal{$row['id']}'>View Details <i class='bi bi-arrow-right ms-2'></i></a>";
                    echo "</div>";
                    echo "</div>";
                    echo "</div>";

                    // Scheme Details Modal
                    // Add these lines before the modal generation code
                    $docsEn = !empty($row['required_documents']) ? nl2br(htmlspecialchars($row['required_documents'])) : 'No documents required';
                    $docsMr = !empty($row['required_documents_marathi']) ? nl2br(htmlspecialchars($row['required_documents_marathi'])) : $docsEn;
                    
                    // Then continue with your existing modal generation code
                    echo "<div class='modal fade' id='schemeDetailsModal{$row['id']}' tabindex='-1' aria-labelledby='schemeDetailsModalLabel{$row['id']}' aria-hidden='true'>";
                    // ... rest of the code remains the same
                    echo "<div class='modal-dialog modal-lg'>";
                    echo "<div class='modal-content'>";
                    echo "<div class='modal-header'>";
                    echo "<h5 class='modal-title' id='schemeDetailsModalLabel{$row['id']}'>Scheme Details</h5>";
                    echo "<button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>";
                    echo "</div>";
                    echo "<div class='modal-body'>";
                    echo "<div class='row'>";
                    echo "<div class='col-md-8'>";
                    echo "<h4 class='mb-3 schemes-title-en'>{$nameEn}</h4>";
                    echo "<h4 class='mb-3 schemes-title-mr' style='display:none;'>{$nameMr}</h4>";
                    echo "<p class='text-muted schemes-desc-en'>{$descEn}</p>";
                    echo "<p class='text-muted schemes-desc-mr' style='display:none;'>{$descMr}</p>";
                    echo "<hr>";
                    echo "<h5>Required Documents</h5>";
                    echo "<ul class='list-group list-group-flush schemes-docs-en'>";
                    foreach(explode("\n", $docsEn) as $doc) {
                        if(trim($doc)) {
                            echo "<li class='list-group-item'><i class='bi bi-check-circle text-success me-2'></i>" . trim($doc) . "</li>";
                        }
                    }
                    echo "</ul>";
                    echo "<ul class='list-group list-group-flush schemes-docs-mr' style='display:none;'>";
                    foreach(explode("\n", $docsMr) as $doc) {
                        if(trim($doc)) {
                            echo "<li class='list-group-item'><i class='bi bi-check-circle text-success me-2'></i>" . trim($doc) . "</li>";
                        }
                    }
                    echo "</ul>";
                    echo "</div>";
                    echo "<div class='col-md-4'>";
                    echo "<div class='card bg-light'>";
                    echo "<div class='card-body'>";
                    echo "<h6 class='card-title'>Scheme Overview</h6>";
                    echo "<ul class='list-unstyled'>";
                    echo "<li><strong>Status:</strong> <span class='badge {$statusClass}'>" . ucfirst($row['status']) . "</span></li>";
                    echo "<li><strong>Start Date:</strong> " . date('M d, Y', strtotime($row['start_date'])) . "</li>";
                    echo "<li><strong>Duration:</strong> {$durationDays} days</li>";
                    echo "</ul>";
                    echo "</div>";
                    echo "</div>";
                    echo "</div>";
                    echo "</div>";
                    echo "</div>";
                    echo "</div>";
                    echo "</div>";
                    echo "</div>";
                }
            } else {
                echo "<div class='col-12'><div class='alert alert-info text-center'><i class='bi bi-info-circle me-2'></i>No schemes found. Start by adding a new scheme!</div></div>";
            }
            ?>
        </div>
    </div>

    <!-- Language Toggle Script -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const englishToggle = document.getElementById('englishToggle');
        const marathiToggle = document.getElementById('marathiToggle');
        
        // Select all language-specific elements
        const languageElements = {
            titles: {
                en: document.querySelectorAll('.schemes-title-en'),
                mr: document.querySelectorAll('.schemes-title-mr')
            },
            descriptions: {
                en: document.querySelectorAll('.schemes-desc-en'),
                mr: document.querySelectorAll('.schemes-desc-mr')
            },
            documents: {
                en: document.querySelectorAll('.schemes-docs-en'),
                mr: document.querySelectorAll('.schemes-docs-mr')
            }
        };

        function updateLanguage(language) {
            // Update toggle button states
            englishToggle.classList.toggle('active', language === 'en');
            marathiToggle.classList.toggle('active', language === 'mr');

            // Toggle visibility of titles
            languageElements.titles.en.forEach(el => el.style.display = language === 'en' ? 'block' : 'none');
            languageElements.titles.mr.forEach(el => el.style.display = language === 'mr' ? 'block' : 'none');

            // Toggle visibility of descriptions
            languageElements.descriptions.en.forEach(el => el.style.display = language === 'en' ? 'block' : 'none');
            languageElements.descriptions.mr.forEach(el => el.style.display = language === 'mr' ? 'block' : 'none');

            // Toggle visibility of documents
            languageElements.documents.en.forEach(el => el.style.display = language === 'en' ? 'block' : 'none');
            languageElements.documents.mr.forEach(el => el.style.display = language === 'mr' ? 'block' : 'none');
        }

        // Initial language setup
        updateLanguage('en');  // Default to English

        // Event listeners for language toggle buttons
        englishToggle.addEventListener('click', () => updateLanguage('en'));
        marathiToggle.addEventListener('click', () => updateLanguage('mr'));

        // Update modal language when opened
        document.querySelectorAll('.view-details').forEach(link => {
            link.addEventListener('click', function() {
                const modalId = this.getAttribute('data-bs-target');
                const modal = document.querySelector(modalId);
                
                if (modal) {
                    const currentLanguage = englishToggle.classList.contains('active') ? 'en' : 'mr';
                    
                    // Update modal titles and descriptions
                    modal.querySelectorAll('.schemes-title-en').forEach(el => el.style.display = currentLanguage === 'en' ? 'block' : 'none');
                    modal.querySelectorAll('.schemes-title-mr').forEach(el => el.style.display = currentLanguage === 'mr' ? 'block' : 'none');
                    modal.querySelectorAll('.schemes-desc-en').forEach(el => el.style.display = currentLanguage === 'en' ? 'block' : 'none');
                    modal.querySelectorAll('.schemes-desc-mr').forEach(el => el.style.display = currentLanguage === 'mr' ? 'block' : 'none');
                    modal.querySelectorAll('.schemes-docs-en').forEach(el => el.style.display = currentLanguage === 'en' ? 'block' : 'none');
                    modal.querySelectorAll('.schemes-docs-mr').forEach(el => el.style.display = currentLanguage === 'mr' ? 'block' : 'none');
                }
            });
        });
    });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
$conn->close();
?>
