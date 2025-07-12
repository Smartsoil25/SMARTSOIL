<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit();
}

include("config.php");

// Fetch latest soil data
$result = $conn->query("SELECT * FROM soil_data ORDER BY timestamp DESC LIMIT 1");


if (!$result || $result->num_rows == 0) {
  die("<div class='alert alert-danger text-center mt-4'>No sensor data found!</div>");
}

$row = $result->fetch_assoc();
$moisture = $row['moisture_level'];
$temp = $row['temperature'];
$ph = $row['pH_level'];
$n = $row['nitrogen'];
$p = $row['phosphorous'];
$k = $row['potassium'];
$humidity = $row['humidity'];


// Predict crop using closest match from dataset table
$closest_crop = "⚠️ No crop match found";
$min_diff = PHP_INT_MAX;
$dataset_result = $conn->query("SELECT * FROM dataset");

while ($d = $dataset_result->fetch_assoc()) {
   $diff = abs($d['moisture'] - $moisture)
  + abs($d['temperature'] - $temp)
  + abs($d['pH'] - $ph)
  + abs($d['humidity'] - $humidity)  //✅ Added humidity comparison
  + abs($d['N'] - $n)
  + abs($d['P'] - $p)
  + abs($d['K'] - $k);
    if ($diff < $min_diff) {
        $min_diff = $diff;
        $closest_crop = "✅ Recommended Crop (AI): " . ucfirst($d['crop_label']);
    }
}
$farmer_id = $_SESSION['user_id'];  // farmer_id ya user aliyelogin
$soilQuery = $conn->query("SELECT * FROM soil_data WHERE farmer_id = $farmer_id ORDER BY timestamp DESC");

if (isset($_POST['send_message'])) {
    $message = $conn->real_escape_string($_POST['message']);
    $farmer_id = $_SESSION['user_id']; // Farmer ID from session (hakikisha ume-set kwenye login)

    if (!empty($message)) {
        $conn->query("INSERT INTO farmer_messages (farmer_id, message) VALUES ($farmer_id, '$message')");
        echo "<div class='alert alert-success text-center mt-3'>✅ Message sent successfully!</div>";
    } else {
        echo "<div class='alert alert-warning text-center mt-3'>⚠️ Message cannot be empty!</div>";
    }
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Dashboard - Smart Soil System</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>

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


<div class="container mt-4 mb-5">
 <!-- <h3 class="text-success">Welcome, <?= $_SESSION['User'] ?> 👩🏽‍🌾</h3>-->

  <table class="table table-bordered mt-4">
    <thead class="table-success">
      <tr>
        <th>#</th>
        <th>Sensor ID</th>
        <th>Moisture (%)</th>
        <th>pH</th>
        <th>Temp (°C)</th>
        <th>Humidity (%)</th>
        <th>Nitrogen</th>
        <th>Phosphorous</th>
        <th>Potassium</th>
        <th>Time</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $soilQuery = $conn->query("SELECT * FROM soil_data ORDER BY timestamp DESC");
      $i = 1;
      while ($row = $soilQuery->fetch_assoc()) {
        echo "<tr>
          <td>{$i}</td>
          <td>{$row['sensor_id']}</td>
          <td>{$row['moisture_level']}</td>
          <td>{$row['pH_level']}</td>
          <td>{$row['temperature']}</td>
          <td>{$row['humidity']}</td>
          <td>{$row['nitrogen']}</td>
          <td>{$row['phosphorous']}</td>
          <td>{$row['potassium']}</td>
          <td>{$row['timestamp']}</td>
        </tr>";
        $i++;
      }
      ?>
    </tbody>
  </table>

  <div class="row mt-4">
    <div class="col-md-6"><canvas id="sensorChart"></canvas></div>
    <div class="col-md-6">
      <div class="card border-success">
        <div class="card-header bg-success text-white">🌱 AI Crop Recommendation</div>
        <div class="card-body">
          <h5><?= $closest_crop ?></h5>
          <p class="mt-3">
  Temperature: <?= $temp ?>°C<br>
  Humidity: <?= $humidity ?>%<br>
  Moisture: <?= $moisture ?>%<br>
  pH: <?= $ph ?><br>
  Nitrogen: <?= $n ?><br>
  Phosphorous: <?= $p ?><br>
  Potassium: <?= $k ?>
</p>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
const ctx = document.getElementById('sensorChart').getContext('2d');
new Chart(ctx, {
  type: 'bar',
  data: {
    labels: ['Moisture', 'Temperature', 'Humidity', 'pH', 'Nitrogen', 'Phosphorous', 'Potassium'],
datasets: [{
  label: 'Sensor Readings',
  data: [<?= $moisture ?>, <?= $temp ?>, <?= $humidity ?>, <?= $ph ?>, <?= $n ?>, <?= $p ?>, <?= $k ?>],
      backgroundColor: ['#4CAF50','#2196F3','#FFC107','#FF5722','#9C27B0','#009688']
    }]
  },
  options: {
    scales: {
      y: { beginAtZero: true }
    }
  }
});
</script>

<div class="container mb-5">
  <div class="card border-success">
    <div class="card-header bg-success text-white">📩 Send Message/Alert to Admin</div>
    <div class="card-body">
      <form method="POST">
        <div class="mb-3">
          <textarea name="message" class="form-control" rows="4" placeholder="Type your message here..." required></textarea>
        </div>
        <button type="submit" name="send_message" class="btn btn-success w-100">
          <i class="bi bi-send-fill me-1"></i> Send Message
        </button>
      </form>
    </div>
  </div>
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
