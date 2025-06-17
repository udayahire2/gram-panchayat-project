<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Meetings</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
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

    // Fetch meetings
    $sql = "SELECT * FROM meetings_db ORDER BY meeting_date DESC";
    $result = $conn->query($sql);
    ?>

    <!-- Header -->
<header>
    <div class="header-content">
        <div class="logo-container">
            <img src="../image/logo.png" alt="Gram Panchayat Logo" class="logo-img" />
            <h1 class="logo-name" data-translate="logoName">Kusumba Grampanchayat</h1>
        </div>
        <div class="nav-controls">
            <button class="login-btn" onclick="handleLogin()" data-translate="login">
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




    <!-- Main Content -->
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Meetings</h1>
            <div class="d-flex align-items-center">
                <div class="btn-group me-3" role="group" aria-label="Language Toggle">
                    <button type="button" class="btn btn-outline-primary active" id="englishToggle">English</button>
                    <button type="button" class="btn btn-outline-primary" id="marathiToggle">मराठी</button>
                </div>
            </div>
        </div>

        <div class="row">
            <?php
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    $statusClass = 'badge-' . $row['status'];
                    
                    // Prepare multilingual content
                    $titleEn = htmlspecialchars($row['title']);
                    $titleMr = htmlspecialchars($row['title_marathi'] ?? $row['title']);
                    
                    $descEn = htmlspecialchars($row['description']);
                    $descMr = htmlspecialchars($row['description_marathi'] ?? $row['description']);
                    
                    // Calculate meeting duration and progress
                    $meetingDate = new DateTime($row['meeting_date']);
                    $currentDate = new DateTime();
                    $interval = $meetingDate->diff($currentDate);
                    $daysUntilMeeting = $interval->invert ? $interval->days : -$interval->days;
                    $progressPercentage = max(0, min(100, (($daysUntilMeeting / 30) * 100))); // Assume 30 days context

                    echo "<div class='col-md-4 mb-4 meeting-card'>";
                    echo "<div class='card h-100 meeting-card-inner'>";
                    echo "<div class='card-header d-flex justify-content-between align-items-center'>";
                    echo "<span class='badge {$statusClass} meeting-status'>" . ucfirst($row['status']) . "</span>";
                    echo "<small class='text-muted'>Created: " . date('M d, Y', strtotime($row['created_at'])) . "</small>";
                    echo "</div>";
                    echo "<div class='card-body'>";
                    echo "<h5 class='card-title meeting-title-en fw-bold mb-3'>{$titleEn}</h5>";
                    echo "<h5 class='card-title meeting-title-mr fw-bold mb-3' style='display:none;'>{$titleMr}</h5>";
                    echo "<p class='card-text meeting-desc-en mb-3'>" . (strlen($descEn) > 100 ? substr($descEn, 0, 100) . '...' : $descEn) . "</p>";
                    echo "<p class='card-text meeting-desc-mr mb-3' style='display:none;'>" . (strlen($descMr) > 100 ? substr($descMr, 0, 100) . '...' : $descMr) . "</p>";
                    
                    echo "<div class='meeting-details'>";
                    echo "<div class='row g-2'>";
                    echo "<div class='col-6'>";
                    echo "<small class='text-muted d-block'>Date & Time</small>";
                    echo "<strong>" . date('M d, Y H:i', strtotime($row['meeting_date'])) . "</strong>";
                    echo "</div>";
                    echo "<div class='col-6'>";
                    echo "<small class='text-muted d-block'>Venue</small>";
                    echo "<strong>" . htmlspecialchars(strlen($row['venue']) > 15 ? substr($row['venue'], 0, 15) . '...' : $row['venue']) . "</strong>";
                    echo "</div>";
                    echo "</div>";
                    
                    echo "<div class='progress'>";
                    echo "<div class='progress-bar bg-primary' role='progressbar' style='width: {$progressPercentage}%' aria-valuenow='{$progressPercentage}' aria-valuemin='0' aria-valuemax='100'></div>";
                    echo "</div>";
                    echo "</div>";
                    
                    echo "</div>";
                    echo "<div class='card-footer bg-transparent border-0 d-flex justify-content-end align-items-center'>";
                    echo "<a href='#' class='text-primary view-details' data-bs-toggle='modal' data-bs-target='#meetingDetailsModal{$row['id']}'>View Details <i class='bi bi-arrow-right'></i></a>";
                    echo "</div>";
                    echo "</div>";
                    echo "</div>";

                    // Meeting Details Modal
                   echo "<div class='modal fade' id='meetingDetailsModal{$row['id']}' tabindex='-1' aria-labelledby='meetingDetailsModalLabel{$row['id']}' aria-hidden='true'>";
                    echo "<div class='modal-dialog modal-lg'>";
                    echo "<div class='modal-content'>";
                    echo "<div class='modal-header'>";
                    echo "<h5 class='modal-title' id='meetingDetailsModalLabel{$row['id']}'>Meeting Details</h5>";
                    echo "<button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>";
                    echo "</div>";
                    echo "<div class='modal-body'>";
                    echo "<div class='row'>";
                    echo "<div class='col-md-8'>";
                    echo "<h4 class='mb-3 meeting-title-en'>{$titleEn}</h4>";
                    echo "<h4 class='mb-3 meeting-title-mr' style='display:none;'>{$titleMr}</h4>";
                    echo "<p class='text-muted meeting-desc-en'>{$descEn}</p>";
                    echo "<p class='text-muted meeting-desc-mr' style='display:none;'>{$descMr}</p>";
                    echo "<hr>";
                    echo "<h5>Meeting Information</h5>";
                    echo "<ul class='list-group list-group-flush'>";
                    echo "<li class='list-group-item'><strong>Date & Time:</strong> " . date('F d, Y h:i A', strtotime($row['meeting_date'])) . "</li>";
                    echo "<li class='list-group-item'><strong>Venue:</strong> " . htmlspecialchars($row['venue']) . "</li>";
                    echo "</ul>";
                    echo "</div>";
                    echo "<div class='col-md-4'>";
                    echo "<div class='card bg-light'>";
                    echo "<div class='card-body'>";
                    echo "<h6 class='card-title'>Meeting Overview</h6>";
                    echo "<ul class='list-unstyled'>";
                    echo "<li><strong>Status:</strong> <span class='badge {$statusClass}'>" . ucfirst($row['status']) . "</span></li>";
                    echo "<li><strong>Created:</strong> " . date('M d, Y', strtotime($row['created_at'])) . "</li>";
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
                echo "<div class='col-12'>";
                echo "<div class='alert alert-info text-center'>";
                echo "<i class='bi bi-info-circle me-2'></i>No meetings found. Schedule a new meeting!";
                echo "</div>";
                echo "</div>";
            }
            ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Language Toggle Functionality
        (function() {
            const englishToggle = document.getElementById('englishToggle');
            const marathiToggle = document.getElementById('marathiToggle');
            const titleEn = document.querySelectorAll('.meeting-title-en');
            const titleMr = document.querySelectorAll('.meeting-title-mr');
            const descEn = document.querySelectorAll('.meeting-desc-en');
            const descMr = document.querySelectorAll('.meeting-desc-mr');

            function updateLanguage(language) {
                // Toggle active state on language buttons
                englishToggle.classList.toggle('active', language === 'en');
                marathiToggle.classList.toggle('active', language === 'mr');

                // Update visibility of content
                const showEn = language === 'en';
                titleEn.forEach(el => el.style.display = showEn ? 'block' : 'none');
                titleMr.forEach(el => el.style.display = showEn ? 'none' : 'block');
                descEn.forEach(el => el.style.display = showEn ? 'block' : 'none');
                descMr.forEach(el => el.style.display = showEn ? 'none' : 'block');
            }

            // Initialize language to English
            updateLanguage('en');

            // Add event listeners to language toggle buttons
            englishToggle.addEventListener('click', () => updateLanguage('en'));
            marathiToggle.addEventListener('click', () => updateLanguage('mr'));

            // Update modal language when opened
            document.querySelectorAll('.view-details').forEach(link => {
                link.addEventListener('click', function() {
                    const modalId = this.getAttribute('data-bs-target');
                    const modal = document.querySelector(modalId);
                    
                    if (modal) {
                        const currentLanguage = englishToggle.classList.contains('active') ? 'en' : 'mr';
                        
                        // Update modal content
                        modal.querySelectorAll('.meeting-title-en, .meeting-title-mr').forEach(el => {
                            el.style.display = el.classList.contains(`meeting-title-${currentLanguage}`) ? 'block' : 'none';
                        });

                        modal.querySelectorAll('.meeting-desc-en, .meeting-desc-mr').forEach(el => {
                            el.style.display = el.classList.contains(`meeting-desc-${currentLanguage}`) ? 'block' : 'none';
                        });
                    }
                });
            });
        })();
// JavaScript for mobile menu toggle
document.addEventListener('DOMContentLoaded', function() {
    const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
    const navLinks = document.querySelector('.nav-links');
    const mobileMenuOverlay = document.querySelector('.mobile-menu-overlay');
    const dropdowns = document.querySelectorAll('.dropdown');
    
    // Toggle mobile menu
    if (mobileMenuToggle) {
        mobileMenuToggle.addEventListener('click', function() {
            mobileMenuToggle.classList.toggle('active');
            navLinks.classList.toggle('active');
            mobileMenuOverlay.classList.toggle('active');
            document.body.style.overflow = navLinks.classList.contains('active') ? 'hidden' : '';
        });
    }
    
    // Close menu when clicking overlay
    if (mobileMenuOverlay) {
        mobileMenuOverlay.addEventListener('click', function() {
            mobileMenuToggle.classList.remove('active');
            navLinks.classList.remove('active');
            mobileMenuOverlay.classList.remove('active');
            document.body.style.overflow = '';
        });
    }
    
    // Handle dropdown toggles on mobile
    dropdowns.forEach(dropdown => {
        const dropdownLink = dropdown.querySelector('a');
        
        dropdownLink.addEventListener('click', function(e) {
            if (window.innerWidth <= 768) {
                e.preventDefault();
                dropdown.classList.toggle('show');
                
                // Close other dropdowns
                dropdowns.forEach(otherDropdown => {
                    if (otherDropdown !== dropdown) {
                        otherDropdown.classList.remove('show');
                    }
                });
            }
        });
    });
    //for debugging
    document.querySelectorAll('.view-details').forEach(link => {
        link.addEventListener('click', function() {
            console.log('View Details clicked for ID:', this.getAttribute('data-bs-target'));
            const modalId = this.getAttribute('data-bs-target');
            const modal = document.querySelector(modalId);
            if (modal) {
                modal.classList.add('show');
                modal.style.display = 'block'; // Ensure it's displayed
                console.log('Modal should be displayed:', modalId);
            }
        });
    });
        
    // Resize handling
    window.addEventListener('resize', function() {
        if (window.innerWidth > 768) {
            navLinks.classList.remove('active');
            mobileMenuToggle.classList.remove('active');
            mobileMenuOverlay.classList.remove('active');
            document.body.style.overflow = '';
            
            // Reset all dropdowns
            dropdowns.forEach(dropdown => {
                dropdown.classList.remove('show');
            });
        }
    });
});
         // Function for login button
         window.handleLogin = function() {
                // Implement login functionality
                console.log('Login button clicked');
            };
    </script>
</body>
</html>