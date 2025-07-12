<?php
include("config.php");
session_start();

$recommended_crop = "";
$message = "";



// Get random sample from dataset table
$sample_result = $conn->query("SELECT * FROM dataset ORDER BY RAND() LIMIT 1");
$sample_data = $sample_result->fetch_assoc();

if (isset($_POST['submit'])) {
  $moisture = (float)$_POST['moisture'];
  $ph = (float)$_POST['ph'];
  $temp = (float)$_POST['temperature'];
  $humidity = (float)$_POST['humidity'];
  $n = (float)$_POST['nitrogen'];
  $p = (float)$_POST['phosphorous'];
  $k = (float)$_POST['potassium'];
  $sensor_id = (int)$_POST['sensor_id'];

  // Recommendation from dataset sample
  $recommended_crop = $sample_data['crop_label'];
$farmer_id = $_SESSION['user_id'];  // Farmer ID ya user aliyelogin
  // Save data to soil_data table
  $sql = "INSERT INTO soil_data (sensor_id, moisture_level, pH_level, temperature, humidity, nitrogen, phosphorous, potassium, recommended_crop)
          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
     
     


  $stmt = $conn->prepare($sql);
  $stmt->bind_param("iddddddds", $sensor_id, $moisture, $ph, $temp, $humidity, $n, $p, $k, $recommended_crop);
  $stmt->execute();

  $message = "<div class='alert alert-success mt-3'>✅ Data added successfully! 🌾 Recommended Crop: <strong>$recommended_crop</strong></div>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add Soil Data</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

   <style>
    html, body {
      height: 100%;
      margin: 0;
      display: flex;
      flex-direction: column;
      background-color: #e0e0e0;  /* Light gray background */
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    .main-content {
      flex: 1;
    }
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
<body style="background-color: #e0e0e0;">

 <!-- ✅ Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #0b470d;">
  <div class="container">
    <a class="navbar-brand fw-bold" href="index.php">
      <i class="bi bi-leaf-fill text-warning me-2"></i> Smart Soil Monitoring and Crop Prediction System 🌿
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
      <span class="navbar-toggler-icon"></span>
    </button>

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

      <!-- 🌿 Right side: Login / Sign Up OR Logout -->
      <ul class="navbar-nav ms-auto d-flex align-items-center gap-2">
        <?php if (!isset($_SESSION['user'])): ?>
          <li class="nav-item">
            <a href="login.php" class="btn btn-outline-light btn-sm">
              <i class="bi bi-box-arrow-in-right me-1"></i> Login
            </a>
          </li>
          <li class="nav-item">
            <a href="register.php" class="btn btn-warning text-dark btn-sm">
              <i class="bi bi-person-plus-fill me-1"></i> Sign Up
            </a>
          </li>
        <?php else: ?>
          <li class="nav-item text-white small me-2">
            👤 <?= $_SESSION['user'] ?>
          </li>
          <li class="nav-item">
            <a href="logout.php" class="btn btn-danger btn-sm">
              <i class="bi bi-box-arrow-right me-1"></i> Logout
            </a>
          </li>
        <?php endif; ?>
      </ul>
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

<div class="container mt-5">
  <h2 class="text-success text-center mb-4"><i class="bi bi-seedling"></i> Add Soil Data</h2>

  <?= $message ?>

  <form method="POST" action="add_soil_data.php" class="bg-white p-4 rounded shadow-sm">
    <div class="row g-3">
      <div class="col-md-6">
        <label>Moisture Level (%)</label>
        <input type="number" step="0.1" class="form-control" name="moisture" value="<?= $sample_data['moisture'] ?>" required>
      </div>
      <div class="col-md-6">
        <label>pH Level</label>
        <input type="number" step="0.1" class="form-control" name="ph" value="<?= $sample_data['pH'] ?>" required>
      </div>
      <div class="col-md-6">
        <label>Temperature (°C)</label>
        <input type="number" step="0.1" class="form-control" name="temperature" value="<?= $sample_data['temperature'] ?>" required>
      </div>
      <div class="col-md-6">
        <label>Nitrogen (N)</label>
        <input type="number" step="0.1" class="form-control" name="nitrogen" value="<?= $sample_data['N'] ?>" required>
      </div>
      <div class="col-md-6">
        <label>Phosphorous (P)</label>
        <input type="number" step="0.1" class="form-control" name="phosphorous" value="<?= $sample_data['P'] ?>" required>
      </div>
      <div class="col-md-6">
        <label>Potassium (K)</label>
        <input type="number" step="0.1" class="form-control" name="potassium" value="<?= $sample_data['K'] ?>" required>
      </div>
      <div class="col-md-6">
        <label>Sensor ID</label>
        <input type="number" class="form-control" name="sensor_id" required>
      </div>
      <div class="col-md-6">
        <label>Humidity (%)</label>
        <input type="number" step="0.1" class="form-control" name="humidity" value="<?= $sample_data['humidity'] ?>" required>
      </div>
    </div>
    <button type="submit" name="submit" class="btn btn-success w-100 mt-4">
      <i class="bi bi-upload me-1"></i> Submit Soil Data
    </button>
  </form>
</div>


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



