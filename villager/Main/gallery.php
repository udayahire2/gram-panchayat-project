<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title data-translate="title">Kusumba Gram Panchayat</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="header.css">
   
    <style>
        /* Global Variables */
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --accent-color: #f39c12;
            --light-color: #f8f9fa;
            --dark-color: #343a40;
            --text-color: #333333;
            --border-color: #dee2e6;
            --hover-color: #e9ecef;
            --shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
        }

        /* Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        /* Gallery specific styles */
        .gallery-container {
            max-width: 1300px;
            margin: 30px auto;
            padding: 0 3%;
        }

        .gallery-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .gallery-header h1 {
            color: var(--primary-color);
            font-size: 2.5rem;
            margin-bottom: 15px;
        }

        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .gallery-item {
            position: relative;
            overflow: hidden;
            border-radius: 8px;
            box-shadow: var(--shadow);
            aspect-ratio: 16/9;
            cursor: pointer;
            transition: transform 0.3s ease;
        }

        .gallery-item:hover {
            transform: scale(1.02);
        }

        .gallery-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .gallery-item:hover img {
            transform: scale(1.1);
        }

        /* Modal styles */
        .modal-image {
            max-height: 80vh;
            object-fit: contain;
        }

        .modal-navigation {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 100%;
            display: flex;
            justify-content: space-between;
            padding: 0 20px;
        }

        .modal-nav-btn {
            background: rgba(0, 0, 0, 0.5);
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 50%;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .modal-nav-btn:hover {
            background: rgba(0, 0, 0, 0.8);
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
            display: block;
            padding: 15px 20px;
            color: var(--primary-color);
            font-weight: 500;
            transition: var(--transition);
            text-decoration: none;
        }

        .nav-links a:hover,
        .nav-links a.active {
            color: var(--secondary-color);
            background-color: var(--hover-color);
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
            padding: 0;
        }

        .dropdown:hover .dropdown-menu {
            display: block;
        }

        /* Mobile Menu */
        .menu-toggle {
            display: none;
            flex-direction: column;
            justify-content: space-between;
            width: 30px;
            height: 20px;
            cursor: pointer;
            position: absolute;
            right: 20px;
            top: 20px;
            z-index: 101;
        }

        .bar {
            height: 3px;
            width: 100%;
            background-color: white;
            border-radius: 3px;
            transition: var(--transition);
        }

        @media (max-width: 768px) {
            .menu-toggle {
                display: flex;
            }

            .nav-links {
                display: none;
                position: fixed;
                top: 0;
                right: -100%;
                width: 70%;
                height: 100vh;
                background: white;
                flex-direction: column;
                padding-top: 80px;
                transition: 0.3s ease;
            }

            .nav-links.active {
                right: 0;
                display: flex;
            }

            .dropdown-menu {
                position: static;
                box-shadow: none;
                width: 100%;
                padding-left: 20px;
            }
        }
    </style>
</head>

<body>
    <div class="menu-toggle">
        <span class="bar"></span>
        <span class="bar"></span>
        <span class="bar"></span>
    </div>

    <header>
        <div class="header-content">
            <div class="logo-container">
                <img src="../image/logo.png" alt="Gram Panchayat Logo" class="logo-img" />
                <h1 class="logo-name" data-translate="logoName">
                    Kusumba Grampanchayat
                </h1>
            </div>


            <div class="login-container">
                <button class="login-btn" onclick="handleLogin()" data-translate="login">
                    Login
                </button>
            </div>
            <div class="profile-container" style="display: none;">
                <div class="profile-logo" style="cursor: pointer;" onclick="showProfileDetails()">
                    <img src="../image/user (2).png" alt="Profile"
                        style="width: 40px; height: 40px; border-radius: 50%;">
                </div>
            </div>
        </div>
        </div>
    </header>

    <!-- nav.html -->
    <nav class="navbar">
        <div class="mobile-menu-toggle">
            <span class="bar"></span>
            <span class="bar"></span>
            <span class="bar"></span>
        </div>
        <ul class="nav-links">
            <li><a href="../Main/index.html" data-translate="home">Home</a></li>
            <li><a href="../Main/about_village.html" data-translate="aboutVillage">About Village</a></li>
            <li><a href="../Main/about_us.html" data-translate="aboutUs">About Us</a></li>
            <li><a href="gallery.php" class="active" data-translate="gallery">Gallery</a></li>
            <li class="dropdown">
                <a href="#" data-translate="notices">Notices</a>
                <ul class="dropdown-menu">
                    <li><a href="../Schemes and meetings/view_meetings.php" data-translate="schemes">Schemes</a></li>
                    <li><a href="../Schemes and meetings/recipient.php" data-translate="recipient">Recipient</a></li>
                    <li><a href="../Schemes and meetings/view_schemes.php" data-translate="meetings">Meetings</a></li>
                </ul>
            </li>
            <li class="dropdown">
                <a href="#" data-translate="tax">Tax</a>
                <ul class="dropdown-menu">
                    <li><a href="../Tax/home_tax.php" data-translate="homeTax">Home Tax</a></li>
                    <li><a href="../Tax/water_tax.html" data-translate="waterTax">Water Tax</a></li>
                    <li><a href="../Tax/sanitation_tax.html" data-translate="sanitationTax">Sanitation Tax</a></li>
                </ul>
            </li>
            <li class="dropdown">
                <a href="#" data-translate="certificates">Certificates</a>
                <ul class="dropdown-menu">
                    <li><a href="../Certificate/Birth_certificate.php" data-translate="birthCertificate">Birth
                            Certificate</a></li>
                    <li><a href="../Certificate/Death_certificate.php" data-translate="deathCertificate">Death
                            Certificate</a></li>
                    <li><a href="../Certificate/Marriage_certificate.php" data-translate="marriageCertificate">Marriage
                            Certificate</a></li>
                </ul>
            </li>
        </ul>
    </nav>
    <div class="gallery-container">
        <div class="gallery-header">
            <h1 data-translate="galleryTitle">Village Gallery</h1>
            <p class="text-muted" data-translate="galleryDescription">Explore the beautiful moments and places of
                Kusumba Village</p>
        </div>

        <div class="gallery-grid">
            <!-- Add your existing hero section images here -->
            <div class="gallery-item" onclick="openModal(0)">
                <img src="../image/Gram panchayat Image.jpg" alt="Kusumba Village" loading="lazy">
            </div>
            <div class="gallery-item" onclick="openModal(1)">
                <img src="../image/photo2.jpg" alt="Kusumba Village" loading="lazy">
            </div>
            <div class="gallery-item" onclick="openModal(2)">
                <img src="../image/photo4.jpg" alt="Kusumba Village" loading="lazy">
            </div>
            <div class="gallery-item" onclick="openModal(3)">
                <img src="../image/kusumbano1.jpg" alt="Kusumba Village" loading="lazy">
            </div>
            <div class="gallery-item" onclick="openModal(4)">
                <img src="../image/kusumbano2.jpg" alt="Kusumba Village" loading="lazy">
            </div>
            <div class="gallery-item" onclick="openModal(5)">
                <img src="../image/kusumbano3.jpg" alt="Kusumba Village" loading="lazy">
            </div>
            <div class="gallery-item" onclick="openModal(6)">
                <img src="../image/kusumbano4.jpg" alt="Kusumba Village" loading="lazy">
            </div>
            <div class="gallery-item" onclick="openModal(7)">
                <img src="../image/kusumbano5.jpg" alt="Kusumba Village" loading="lazy">
            </div>
            <div class="gallery-item" onclick="openModal(8)">
                <img src="../image/kusumbano6.jpg" alt="Kusumba Village" loading="lazy">
            </div>
        </div>
    </div>

    <!-- Image Modal -->
    <div class="modal fade" id="imageModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body p-0 position-relative">
                    <img src="" alt="Gallery Image" class="modal-image w-100">
                    <div class="modal-navigation">
                        <button class="modal-nav-btn prev-btn">
                            <i class="bi bi-chevron-left"></i>
                        </button>
                        <button class="modal-nav-btn next-btn">
                            <i class="bi bi-chevron-right"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <footer class="footer">
      <div class="container footer-grid">
        <div class="footer-section">
          <h4>Quick Links</h4>
          <ul>
            <li><a href="../Main/index.html">Home</a></li>
            <li><a href="../Main/about_village.html">About Village</a></li>
            <li><a href="../Main/about_us.html">About Us</a></li>
          </ul>
        </div>
        <div class="footer-section">
          <h4>Tax Services</h4>
          <ul>
            <li><a href="../Tax/home_tax.php">Home Tax</a></li>
            <li><a href="../Tax/water_tax.php">water Tax</a></li>
            <li><a href="../Tax/sanitation_tax.php">Sanitation Tax</a></li>
            
          </ul>
        </div>
        <div class="footer-section">
          <h4>Certificate Services</h4>
          <ul>
            <li><a href="../Certificate/Birth_certificate.php">Apply Birth Certificate</a></li>
            <li><a href="../Certificate/Death_certificate.php">Apply Death Certificate</a></li>
            <li><a href="../Certificate/Marriage_certificate.php">Apply Marriage Certificate</a></li>
          </ul>
        </div>
        <div class="footer-section">
          <h4>Contact Info</h4>
          <ul class="contact-info">
            <li><i class="fas fa-map-marker-alt"></i> Kusumba Village, Maharashtra</li>
          </ul>
        </div>
      
        </div>
      </div>
      <div class="footer-bottom">
        <p>&copy; 2025 Kusumba Gram Panchayat. All rights reserved.</p>
      </div>
    </footer>
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const menuToggle = document.querySelector('.menu-toggle');
            const navLinks = document.querySelector('.nav-links');
            const dropdowns = document.querySelectorAll('.dropdown');

            menuToggle.addEventListener('click', () => {
                menuToggle.classList.toggle('active');
                navLinks.classList.toggle('active');
            });

            dropdowns.forEach(dropdown => {
                const link = dropdown.querySelector('a');
                if (window.innerWidth <= 768) {
                    link.addEventListener('click', (e) => {
                        e.preventDefault();
                        const menu = dropdown.querySelector('.dropdown-menu');
                        menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
                    });
                }
            });

            // Close menu when clicking outside
            document.addEventListener('click', (e) => {
                if (!menuToggle.contains(e.target) && !navLinks.contains(e.target)) {
                    menuToggle.classList.remove('active');
                    navLinks.classList.remove('active');
                }
            });
        });

        function handleLogin() {
            window.location.href = '../login/login.php';
        }

        // Array of image paths
        const images = [
            '../image/Gram panchayat Image.jpg',
            '../image/photo2.jpg',
            '../image/photo4.jpg',
            '../image/kusumbano1.jpg',
            '../image/kusumbano2.jpg',
            '../image/kusumbano3.jpg',
            '../image/kusumbano4.jpg',
            '../image/kusumbano5.jpg',
            '../image/kusumbano6.jpg'
        ];

        let currentImageIndex = 0;
        const imageModal = new bootstrap.Modal(document.getElementById('imageModal'));
        const modalImage = document.querySelector('.modal-image');

        function openModal(index) {
            currentImageIndex = index;
            modalImage.src = images[index];
            imageModal.show();
        }

        // Navigation buttons
        document.querySelector('.prev-btn').addEventListener('click', () => {
            currentImageIndex = (currentImageIndex - 1 + images.length) % images.length;
            modalImage.src = images[currentImageIndex];
        });

        document.querySelector('.next-btn').addEventListener('click', () => {
            currentImageIndex = (currentImageIndex + 1) % images.length;
            modalImage.src = images[currentImageIndex];
        });

        // Keyboard navigation
        document.addEventListener('keydown', (e) => {
            if (!imageModal._isShown) return;

            if (e.key === 'ArrowLeft') {
                currentImageIndex = (currentImageIndex - 1 + images.length) % images.length;
                modalImage.src = images[currentImageIndex];
            }
            else if (e.key === 'ArrowRight') {
                currentImageIndex = (currentImageIndex + 1) % images.length;
                modalImage.src = images[currentImageIndex];
            }
            else if (e.key === 'Escape') {
                imageModal.hide();
            }
        });

        // Add to translations object
        translations.en.galleryTitle = "Village Gallery";
        translations.en.galleryDescription = "Explore the beautiful moments and places of Kusumba Village";
        translations.mr.galleryTitle = "गाव गॅलरी";
        translations.mr.galleryDescription = "कुसुंबा गावातील सुंदर क्षण आणि ठिकाणे पहा";

        function handleLogin() {
            window.location.href = '../login/login.php';
        }
        function showProfileDetails() {
            fetch('../login/get_user_details.php').then(Response => {
                if (!Response.ok) {
                    throw new Error('Not logged in');
                }
                return Response.json();
            }).then(user => {
                const profileModal = document.createElement('div');
                profileModal.innerHTML = `
                    <div style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); display: flex; justify-content: center; align-items: center; z-index: 1000;">
                        <div style="background: white; padding: 2rem; border-radius: 10px; max-width: 500px; width: 90%; text-align: center;">
                            <img src="../image/user (2).png" alt="Profile" style="width: 100px; height: 100px; border-radius: 50%; margin-bottom: 1rem;">
                            <h2>Profile Details</h2>
                            <p><strong>Name:</strong> ${user.name}</p>
                            <p><strong>Email:</strong> ${user.email}</p>
                            <p><strong>Mobile:</strong> ${user.mobile}</p>
                            <p><strong>Address:</strong> ${user.address}</p>
                            <button onclick="this.parentElement.parentElement.remove()" style="background: #dc3545; color: white; border: none; padding: 0.5rem 1rem; border-radius: 5px; margin-top: 1rem;">Close</button>
                        </div>
                    </div>
                `;
                document.body.appendChild(profileModal);
            })
                .catch(error => {
                    alert('You are not logged in. Please log in to view your profile.');
                    sessionStorage.removeItem('isLoggedIn');
                    window.location.href = '../login/login.php';
                });
        }
        document.addEventListener('DOMContentLoaded', function () {
            checkLoginStatus();
        });

        function checkLoginStatus() {
            // Check if session storage indicates user is logged in
            const isLoggedIn = sessionStorage.getItem('isLoggedIn') === 'true';
            fetch('../login/session_check.php')
                .then(response => response.json())
                .then(data => {
                    updateLoginUI(data.isLoggedIn);
                    // Also update session storage
                    sessionStorage.setItem('isLoggedIn', data.isLoggedIn);
                })
                .catch(error => {
                    // Fallback to client-side check if server check fails
                    console.error('Error checking login status:', error);
                    updateLoginUI(isLoggedIn);
                });
        }
        // Update UI based on login status
        function updateLoginUI(isLoggedIn) {
            const loginContainer = document.querySelector('.login-container');
            const profileContainer = document.querySelector('.profile-container');

            if (isLoggedIn) {
                // User is logged in - show profile, hide login
                loginContainer.style.display = 'none';
                profileContainer.style.display = 'block';
            } else {
                // User is not logged in - show login, hide profile
                loginContainer.style.display = 'block';
                profileContainer.style.display = 'none';
            }
        }
    </script>
</body>

</html>