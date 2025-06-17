<?php
session_start();
// Check if user is logged in as clerk
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'clerk') {
    header('Location: ../../villager/Admin Login/admin_login.php');
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clerk Dashboard - Gram Panchayat</title>
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

        /* Sidebar Styles */
        .sidebar {
            background: #232f4b;
            min-height: 100vh;
            padding-top: 30px;
        }
        .sidebar .nav-link {
            color: #fff;
            margin-bottom: 10px;
            font-size: 16px;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 15px;
            border-radius: 8px;
            transition: background 0.2s, color 0.2s;
        }
        .sidebar .nav-link.active,
        .sidebar .nav-link:hover {
            background: #3949ab;
            color: #fff;
        }
        .sidebar .nav-link i {
            font-size: 20px;
        }

        .main-content {
            padding: 2rem 1rem;
        }

        .section-title {
            color: #1a237e;
            border-left: 4px solid #1a237e;
            padding-left: 10px;
            margin: 20px 0;
        }
        
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: transform 0.3s, box-shadow 0.3s;
            margin-bottom: 20px;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        
        .card-body {
            padding: 1.5rem;
        }
        
        .icon-box {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
        }
        
        .tax-icon { background-color: #e3f2fd; }
        .certificate-icon { background-color: #f3e5f5; }
        
        .bi {
            font-size: 24px;
        }
        
        .card-title {
            margin: 0;
            font-size: 18px;
            font-weight: 600;
        }
        
        .stats {
            font-size: 14px;
            color: #666;
            margin-top: 10px;
        }
        
        .logout-btn {
            background-color: #ff5252;
            border: none;
        }
        
        .logout-btn:hover {
            background-color: #ff1744;
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
                    <h1 class="h3 mb-0">Clerk Dashboard</h1>
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
            <li class="nav-item">
              <a class="nav-link active" href="#tax" data-section="tax">
                <i class="bi bi-cash-coin"></i>
                Tax Management
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="#certificate" data-section="certificate">
                <i class="bi bi-file-earmark-medical"></i>
                Certificate Management
              </a>
            </li>
          </ul>
        </nav>
        <!-- Main Content -->
        <main class="col-lg-10 col-md-9 main-content">
          <!-- Tax Management Section -->
          <section id="tax" class="">
            <h2 class="section-title">Tax Management</h2>
            <div class="row">
                <div class="col-md-3">
                    <a href="../Tax Manage/add_records.php" class="text-decoration-none">
                        <div class="card">
                            <div class="card-body">
                                <div class="icon-box tax-icon">
                                    <i class="bi bi-plus-circle text-primary"></i>
                                </div>
                                <h5 class="card-title text-dark">Add Records</h5>
                                <div class="stats">Manage tax records</div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="../Tax Manage/view_home.php" class="text-decoration-none">
                        <div class="card">
                            <div class="card-body">
                                <div class="icon-box tax-icon">
                                    <i class="bi bi-house text-primary"></i>
                                </div>
                                <h5 class="card-title text-dark">Home Tax</h5>
                                <div class="stats">View home tax records</div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="../Tax Manage/view_water.php" class="text-decoration-none">
                        <div class="card">
                            <div class="card-body">
                                <div class="icon-box tax-icon">
                                    <i class="bi bi-droplet text-primary"></i>
                                </div>
                                <h5 class="card-title text-dark">Water Tax</h5>
                                <div class="stats">View water tax records</div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="../Tax Manage/view_sanitation.php" class="text-decoration-none">
                        <div class="card">
                            <div class="card-body">
                                <div class="icon-box tax-icon">
                                    <i class="bi bi-trash text-primary"></i>
                                </div>
                                <h5 class="card-title text-dark">Sanitation Tax</h5>
                                <div class="stats">View sanitation records</div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
          </section>

          <!-- Certificate Management Section -->
          <section id="certificate" class="d-none">
            <h2 class="section-title mt-5">Certificate Management</h2>
            <div class="row">
                <!-- Birth Certificates -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="icon-box certificate-icon">
                                <i class="bi bi-file-earmark-text text-purple"></i>
                            </div>
                            <h5 class="card-title">Birth Certificates</h5>
                            <div class="mt-3">
                                <a href="../../villager/Certificate/view_birth.php" class="btn btn-sm btn-outline-primary mb-2 w-100">New Applications</a>
                                <a href="../../villager/Certificate/view_approved_birth.php" class="btn btn-sm btn-outline-success w-100">Approved Records</a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Death Certificates -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="icon-box certificate-icon">
                                <i class="bi bi-file-earmark text-purple"></i>
                            </div>
                            <h5 class="card-title">Death Certificates</h5>
                            <div class="mt-3">
                                <a href="../../villager/Certificate/view_death.php" class="btn btn-sm btn-outline-primary mb-2 w-100">New Applications</a>
                                <a href="../../villager/Certificate/view_approved_death.php" class="btn btn-sm btn-outline-success w-100">Approved Records</a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Marriage Certificates -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="icon-box certificate-icon">
                                <i class="bi bi-hearts text-purple"></i>
                            </div>
                            <h5 class="card-title">Marriage Certificates</h5>
                            <div class="mt-3">
                                <a href="../../villager/Certificate/view_marriage.php" class="btn btn-sm btn-outline-primary mb-2 w-100">New Applications</a>
                                <a href="../../villager/Certificate/view_approved_marriage.php" class="btn btn-sm btn-outline-success w-100">Approved Records</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
          </section>
        </main>
      </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
      function showSectionFromHash() {
        var hash = window.location.hash ? window.location.hash.substring(1) : "tax";
        document.querySelectorAll('.sidebar .nav-link').forEach(function(l) {
          l.classList.remove('active');
        });
        document.querySelectorAll('main section').forEach(function(sec) {
          sec.classList.add('d-none');
        });
        var section = document.getElementById(hash);
        var link = document.querySelector('.sidebar .nav-link[data-section="' + hash + '"]');
        if (section) section.classList.remove('d-none');
        if (link) link.classList.add('active');
      }
      showSectionFromHash();
      window.addEventListener('hashchange', showSectionFromHash);
      document.querySelectorAll('.sidebar .nav-link').forEach(function(link) {
        link.addEventListener('click', function(e) {
          setTimeout(showSectionFromHash, 0);
        });
      });
    </script>
</body>
</html>