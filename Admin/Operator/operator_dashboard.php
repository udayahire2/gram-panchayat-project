<?php
session_start();
// Check if user is logged in as operator
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'operator') {
    header('Location: ../../villager/Admin Login/admin_login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Operator Dashboard - Gram Panchayat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f0f2f5;
            font-family: 'Segoe UI', sans-serif;
        }
        .dashboard-header {
            background: #1a237e;
            color: white;
            padding: 1rem;
        }
        .sidebar {
            background: #232f4b;
            min-height: 100vh;
            padding-top: 30px;
        }
        .sidebar .nav-link {
            color: #fff;
            margin-bottom: 10px;
            font-size: 16px;
        }
        .sidebar .nav-link.active, .sidebar .nav-link:hover {
            background: #3949ab;
            color: #fff;
            border-radius: 8px;
        }
        .main-content {
            padding: 2rem 1rem;
        }
        .section-title {
            color: #1a237e;
            border-left: 4px solid #1a237e;
            padding-left: 10px;
            margin: 30px 0 10px 0;
        }
        .section-desc {
            color: #555;
            margin-bottom: 20px;
            font-size: 15px;
        }
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            transition: transform 0.2s, box-shadow 0.2s;
            margin-bottom: 24px;
        }
        .card:hover {
            transform: translateY(-3px) scale(1.03);
            box-shadow: 0 6px 16px rgba(0,0,0,0.13);
        }
        .icon-box {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 12px;
        }
        .tax-icon { background-color: #e3f2fd; }
        .certificate-icon { background-color: #f3e5f5; }
        .bi { font-size: 24px; }
        .card-title {
            margin: 0;
            font-size: 18px;
            font-weight: 600;
        }
        .stats {
            font-size: 14px;
            color: #666;
            margin-top: 8px;
        }
        .logout-btn {
            background-color: #ff5252;
            border: none;
        }
        .logout-btn:hover {
            background-color: #ff1744;
        }
        @media (max-width: 991px) {
            .sidebar { min-height: auto; }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="dashboard-header">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <img src="logo.png" alt="Logo" style="height:48px; margin-right:15px; border-radius:50%;">
                    <h1 class="h3 mb-0">Operator Dashboard</h1>
                </div>
                <a href="logout.php" class="btn btn-danger logout-btn">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </a>
            </div>
        </div>
    </div>

    <div class="container-fluid">
      <div class="row">
        <!-- Sidebar Navigation -->
        <nav class="col-lg-2 col-md-3 sidebar d-none d-md-block">
          <ul class="nav flex-column">
            <li class="nav-item"><a class="nav-link active" href="#villager" data-section="villager">Villager Management</a></li>
            <li class="nav-item"><a class="nav-link" href="#scheme" data-section="scheme">Scheme Management</a></li>
            <li class="nav-item"><a class="nav-link" href="#meeting" data-section="meeting">Meeting Management</a></li>
            <li class="nav-item"><a class="nav-link" href="#recipient" data-section="recipient">Recipient Management</a></li>
          </ul>
        </nav>
        <!-- Main Content -->
        <main class="col-lg-10 col-md-9 main-content">
          <!-- Villager Management Section -->
          <section id="villager" class="">
            <h2 class="section-title">Villager Management (Kusumba Village)</h2>
           
            <div class="row">
              <div class="col-md-6 col-lg-4">
                <a href="../Villager Manage/add_villagers.php" class="text-decoration-none">
                  <div class="card text-center">
                    <div class="card-body">
                      <div class="icon-box tax-icon mx-auto">
                        <i class="bi bi-person-plus text-primary"></i>
                      </div>
                      <h5 class="card-title text-dark">Add Villager</h5>
                      <div class="stats">Register new villager</div>
                    </div>
                  </div>
                </a>
              </div>
              <div class="col-md-6 col-lg-4">
                <a href="../Villager Manage/manage_villagers.php" class="text-decoration-none">
                  <div class="card text-center">
                    <div class="card-body">
                      <div class="icon-box certificate-icon mx-auto">
                        <i class="bi bi-people text-purple"></i>
                      </div>
                      <h5 class="card-title text-dark">View Villagers</h5>
                      <div class="stats">Manage existing villagers</div>
                    </div>
                  </div>
                </a>
              </div>
            </div>
          </section>

          <!-- Scheme Management Section -->
          <section id="scheme" class="d-none">
            <h2 class="section-title mt-5">Scheme Management</h2>
           
            <div class="row">
              <div class="col-md-6 col-lg-4">
                <a href="../Schemes and meetings Manage/add_scheme.php" class="text-decoration-none">
                  <div class="card text-center">
                    <div class="card-body">
                      <div class="icon-box certificate-icon mx-auto">
                        <i class="bi bi-file-earmark-plus text-success"></i>
                      </div>
                      <h5 class="card-title">Add Scheme</h5>
                      <div class="stats">Create new government scheme</div>
                    </div>
                  </div>
                </a>
              </div>
              <div class="col-md-6 col-lg-4">
                <a href="../Schemes and meetings Manage/view_schemes.php" class="text-decoration-none">
                  <div class="card text-center">
                    <div class="card-body">
                      <div class="icon-box certificate-icon mx-auto">
                        <i class="bi bi-files text-success"></i>
                      </div>
                      <h5 class="card-title">View Schemes</h5>
                      <div class="stats">Manage existing schemes</div>
                    </div>
                  </div>
                </a>
              </div>
            </div>
          </section>

          <!-- Meeting Management Section -->
          <section id="meeting" class="d-none">
            <h2 class="section-title mt-5">Meeting Management</h2>
            
            <div class="row">
              <div class="col-md-6 col-lg-4">
                <a href="../Schemes and meetings Manage/add_meeting.php" class="text-decoration-none">
                  <div class="card text-center">
                    <div class="card-body">
                      <div class="icon-box tax-icon mx-auto">
                        <i class="bi bi-calendar-plus text-warning"></i>
                      </div>
                      <h5 class="card-title">Schedule Meeting</h5>
                      <div class="stats">Add new meeting</div>
                    </div>
                  </div>
                </a>
              </div>
              <div class="col-md-6 col-lg-4">
                <a href="../Schemes and meetings Manage/view_meetings.php" class="text-decoration-none">
                  <div class="card text-center">
                    <div class="card-body">
                      <div class="icon-box tax-icon mx-auto">
                        <i class="bi bi-calendar-event text-warning"></i>
                      </div>
                      <h5 class="card-title">View Meetings</h5>
                      <div class="stats">See all meetings</div>
                    </div>
                  </div>
                </a>
              </div>
            </div>
          </section>

          <!-- Recipient Management Section -->
          <section id="recipient" class="d-none">
            <h2 class="section-title mt-5">Recipient Management</h2>
          
            <div class="row">
              <div class="col-md-6 col-lg-4">
                <a href="../Schemes and meetings Manage/add_scheme_recipients.php" class="text-decoration-none">
                  <div class="card text-center">
                    <div class="card-body">
                      <div class="icon-box certificate-icon mx-auto">
                        <i class="bi bi-person-check text-info"></i>
                      </div>
                      <h5 class="card-title">Add Recipient</h5>
                      <div class="stats">Add recipient for scheme</div>
                    </div>
                  </div>
                </a>
              </div>
              <div class="col-md-6 col-lg-4">
                <a href="../Schemes and meetings Manage/view_scheme_recipients.php" class="text-decoration-none">
                  <div class="card text-center">
                    <div class="card-body">
                      <div class="icon-box certificate-icon mx-auto">
                        <i class="bi bi-people-fill text-info"></i>
                      </div>
                      <h5 class="card-title">View Recipients</h5>
                      <div class="stats">Manage scheme recipients</div>
                    </div>
                  </div>
                </a>
              </div>
            </div>
          </section>
        </main>
      </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
      function showSectionFromHash() {
        var hash = window.location.hash ? window.location.hash.substring(1) : "villager";
        // Remove active from all links
        document.querySelectorAll('.sidebar .nav-link').forEach(function(l) {
          l.classList.remove('active');
        });
        // Hide all sections
        document.querySelectorAll('main section').forEach(function(sec) {
          sec.classList.add('d-none');
        });
        // Show the selected section and activate the link
        var section = document.getElementById(hash);
        var link = document.querySelector('.sidebar .nav-link[data-section="' + hash + '"]');
        if (section) section.classList.remove('d-none');
        if (link) link.classList.add('active');
      }

      // On page load
      showSectionFromHash();

      // On hash change (back/forward navigation)
      window.addEventListener('hashchange', showSectionFromHash);

      // Sidebar navigation logic
      document.querySelectorAll('.sidebar .nav-link').forEach(function(link) {
        link.addEventListener('click', function(e) {
          // Let the browser update the hash, then show the section
          setTimeout(showSectionFromHash, 0);
        });
      });
    </script>
</body>
</html>