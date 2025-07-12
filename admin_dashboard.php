<?php
session_start();
include("config.php");

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    // If not admin, redirect
    header("Location: login.php");
    exit();
}


// Fetch all farmers
$farmers_result = $conn->query("SELECT * FROM farmers");

// Fetch all soil data
$soil_result = $conn->query("SELECT * FROM soil_data ORDER BY timestamp DESC");

// Placeholder for crop rules (future)
$rules_result = $conn->query("SELECT * FROM crop_rules ORDER BY created_at DESC");




?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard - Smart Soil</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body, html { height: 100%; display: flex; flex-direction: column; }
    .content { flex: 1; }
    .footer {
      background-color: #1b5e20;
      color: white;
      padding: 30px 0 20px;
    }
    .footer a { color: #c8e6c9; text-decoration: none; }
    .footer a:hover { text-decoration: underline; }
    .navbar, .footer {
      background-color: #0b470d;
    }
    .navbar-brand, .nav-link {
      color: white !important;
    }
    .hero-section {
      padding: 80px 20px;
      background: #e8f5e9;
      text-align: center;
    }
    .hero-section h1 {
      color: #2e7d32;
      font-weight: 700;
      font-size: 2.6rem;
    }
    .feature-box {
      border-radius: 15px;
      background-color: white;
      padding: 30px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      margin: 15px 0;
    }
    .feature-box i {
      font-size: 2rem;
      color: #2c7a25;
    }
    .footer {
      color: white;
      padding: 40px 0 20px;
    }
    .footer a {
      color: #c8e6c9;
      text-decoration: none;
    }
    .footer a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>

<nav class="navbar navbar-expand-lg py-3 shadow-sm" style="background-color: #2e7d32;">
  <div class="container">
    <a class="navbar-brand text-white fw-bold d-flex align-items-center" href="index.php">
      <span style="font-size: 1.5rem;">Smart Soil Monitoring and Crop Prediction System🌿</span>
    </a>

    
    <div class="collapse navbar-collapse" id="navbarMain">
      <!-- 🌿 Left side: navigation links -->
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
        <li class="nav-item"><a class="nav-link active" href="add_soil_data.php">Add Soil Data</a></li>
  

       <!-- <li class="nav-item"><a class="nav-link" href="recommendations.php">Crop Advice</a></li>
        <li class="nav-item"><a class="nav-link" href="soil_tips.php">Soil Tips</a></li>
        <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>-->
      </ul>
    <div class="ms-auto">
      <span class="text-white me-3 fw-bold">Welcome, <?= $_SESSION['username'] ?> 👨🏽‍💼</span>

      <a href="logout.php" class="btn btn-outline-light"><i class="bi bi-box-arrow-right"></i> Logout</a>
    </div>
  </div>
</nav>

<!-- 🔰 Top Logo Centered with Light Gray Background and Large Marquee Text -->
<div class="container-fluid text-center py-4" style="background-color: #f2f2f2;">
  <img src="images/logo1.png" alt="Logo" style="height: 200px;" class="mb-3">
  <div>
    <marquee behavior="scroll" direction="left" scrollamount="5" class="text-success fw-bold" style="font-size: 1.9rem;">
      Welcome to the Smart Soil Monitoring and Crop Prediction System – Empowering agriculture with smart technology!
    </marquee>
  </div>
</div>

<div class="container content mt-4">
  <h3 class="text-success">Welcome Admin 👨🏽‍💼</h3>

<div class="card mb-4">
  <div class="card-header bg-success text-white">👥 Register New Farmer</div>
  <div class="card-body">
    <form method="POST" action="register_user_by_admin.php" class="row g-3">
      <div class="col-md-3">
        <input type="text" name="FarmerName" class="form-control" placeholder="Full Name" required>
      </div>
      <div class="col-md-2">
        <input type="text" name="username" class="form-control" placeholder="Username" required>
      </div>
      <div class="col-md-2">
        <input type="text" name="phone" class="form-control" placeholder="Phone" required>
      </div>
      <div class="col-md-2">
        <input type="text" name="location" class="form-control" placeholder="Location" required>
      </div>
      <div class="col-md-2">
        <input type="text" name="device_id" class="form-control" placeholder="Device ID" required>
      </div>
      <div class="col-md-2">
        <input type="password" name="password" class="form-control" placeholder="Password" required>
      </div>
      <div class="col-md-2">
        <button type="submit" class="btn btn-success w-100">Add</button>
      </div>
    </form>
  </div>
</div>


  <div class="card mb-4">
    <div class="card-header bg-success text-white">👥 Manage Farmers</div>
    <div class="card-body table-responsive">
      <table class="table table-bordered">
        <thead class="table-success">
          <tr><th>ID</th><th>Name</th><th>Username</th><th>Action</th></tr>
        </thead>
        <tbody>
          <?php while ($f = $farmers_result->fetch_assoc()): ?>
            <tr>
              <td><?= $f['farmer_id'] ?></td>
              <td><?= $f['FarmerName'] ?></td>
              <td><?= $f['username'] ?></td>
              <td>
                <a href="delete_farmer.php?id=<?= $f['farmer_id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this farmer?')">
                  <i class="bi bi-trash"></i> Delete
                </a>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>

  <div class="card mb-4">
    <div class="card-header bg-success text-white">📋 Soil Data Overview</div>
    <div class="card-body table-responsive">
      <table class="table table-bordered table-striped">
       <thead class="table-success">
  <tr>
    <th>ID</th><th>Sensor</th><th>Moisture</th><th>pH</th><th>Temp</th>
    <th>N</th><th>P</th><th>K</th><th>Time</th><th>AI Recommendation</th>
  </tr>
</thead>

       <tbody>
  <?php while ($s = $soil_result->fetch_assoc()): ?>
    <tr>
      <td><?= $s['soil_id'] ?></td>
      <td><?= $s['sensor_id'] ?></td>
      <td><?= $s['moisture_level'] ?></td>
      <td><?= $s['pH_level'] ?></td>
      <td><?= $s['temperature'] ?></td>
      <td><?= $s['nitrogen'] ?></td>
      <td><?= $s['phosphorous'] ?></td>
      <td><?= $s['potassium'] ?></td>
      <td><?= $s['timestamp'] ?></td>
      <td><?= $s['recommended_crop'] ?? '<span class="text-muted">N/A</span>' ?></td>
    </tr>
  <?php endwhile; ?>
</tbody>

      </table>
    </div>
  </div>

  <div class="card mb-4">
    <div class="card-header bg-success text-white">🧪 Crop Rules Management</div>
    <div class="card-body">
      <form method="POST" action="add_crop_rule.php" class="row g-3">
        <div class="col-md-4">
          <input type="text" name="crop" class="form-control" placeholder="Crop Name" required>
        </div>
        <div class="col-md-8">
          <input type="text" name="conditions" class="form-control" placeholder="Rule conditions (e.g., Moisture > 30, pH between 6-7)" required>
        </div>
        <div class="col-12 text-end">
          <button type="submit" class="btn btn-success">Add Rule</button>
        </div>
      </form>
      <hr>
      <table class="table table-bordered mt-3">
        <thead class="table-success">
          <tr><th>Crop</th><th>Rule Conditions</th><th>Created</th></tr>
        </thead>
        <tbody>
          <?php if ($rules_result && $rules_result->num_rows > 0): 
            while ($r = $rules_result->fetch_assoc()): ?>
              <tr>
                <td><?= $r['crop'] ?></td>
                <td><?= $r['conditions'] ?></td>
                <td><?= $r['created_at'] ?></td>
              </tr>
          <?php endwhile; endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<a href="view_messages.php" class="btn btn-outline-info">
  <i class="bi bi-envelope"></i> View Messages
</a>

 <li class="nav-item">
  <a class="nav-link" href="view_alerts.php">View Alerts</a>
</li>


<!-- ✅ Footer -->
<footer class="footer">
  <div class="container">
    <div class="row text-start">
      <div class="col-md-3">
        <h5><i class="bi bi-seedling text-warning me-1"></i>SmartSoil System</h5>
        <p>We provide smart solutions for soil monitoring and AI-powered crop predictions to empower farmers.</p>
        <a href="#"><i class="bi bi-facebook me-2"></i></a>
        <a href="#"><i class="bi bi-twitter me-2"></i></a>
        <a href="#"><i class="bi bi-linkedin"></i></a>
      </div>
      <div class="col-md-3">
        <h5>Our Services</h5>
        <ul class="list-unstyled">
          <li><i class="bi bi-check2-circle me-1"></i> Soil Monitoring</li>
          <li><i class="bi bi-check2-circle me-1"></i> Crop Prediction</li>
          <li><i class="bi bi-check2-circle me-1"></i> Mobile App</li>
          <li><i class="bi bi-check2-circle me-1"></i> Online Support</li>
        </ul>
      </div>
      <div class="col-md-3">
        <h5>Navigation</h5>
        <ul class="list-unstyled">
          <li><a href="index.php">Home</a></li>
          <li><a href="login.php">Login</a></li>
          <li><a href="register.php">Register</a></li>
          <li><a href="dashboard.php">Dashboard</a></li>
          <li><a href="add_soil_data.php">Add Soil Data</a></li>
        </ul>
      </div>
      <div class="col-md-3">
        <h5>Contact Us</h5>
        <p><i class="bi bi-envelope me-2"></i>ashakhaulat@gmail.com</p>
        <p><i class="bi bi-phone me-2"></i>+255 653090234 or +255 776240223</p>
        <p><i class="bi bi-geo-alt me-2"></i>Chukwani, Zanzibar</p>
      </div>
    </div>
    <hr class="border-light">
    <p class="text-center small">© <?= date('Y') ?> Smart Soil Monitoring System. All rights reserved.</p>
  </div>
</footer>
<!-- ✅ JS Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>