<?php
session_start();
include("config.php");

$feedback = ""; // message holder

if (isset($_POST['submit_farmer'])) {
    $name = $_POST['fullname'];
    $location = $_POST['location'];
    $phone = $_POST['phone'];
    $device_id = $_POST['device_id'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // ✅ Check for duplicate username
    $check_user = $conn->prepare("SELECT * FROM farmers WHERE username = ?");
    $check_user->bind_param("s", $username);
    $check_user->execute();
    $user_result = $check_user->get_result();

    // ✅ Check for duplicate device_id
    $check_device = $conn->prepare("SELECT * FROM farmers WHERE device_id = ?");
    $check_device->bind_param("s", $device_id);
    $check_device->execute();
    $device_result = $check_device->get_result();

    if ($user_result->num_rows > 0) {
        $feedback = "<div class='alert alert-warning text-center'>Username already exists. Please use a different one.</div>";
    } elseif ($device_result->num_rows > 0) {
        $feedback = "<div class='alert alert-warning text-center'>Device ID already exists. Please use a unique one.</div>";
    } else {
        // Proceed with registration
        $sql = "INSERT INTO farmers (FarmerName, Location, Phone, device_id, Username, Password) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssss", $name, $location, $phone, $device_id, $username, $password);

        if ($stmt->execute()) {
            $_SESSION['user'] = $username;
            header("Location: dashboard.php");
            exit();
        } else {
            $feedback = "<div class='alert alert-danger text-center'>Registration failed: " . $stmt->error . "</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Register - Smart Soil Monitoring and Crop Prediction System</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body {
      background-color: #f4fff4;
      display: flex;
      flex-direction: column;
      min-height: 100vh;
      margin: 0;
    }
    .register-form {
      max-width: 720px;
      margin: 60px auto;
      background: white;
      padding: 40px;
      border-radius: 16px;
      box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
    }
    footer {
      background-color: #215c2f;
      color: white;
      padding: 30px 0;
      margin-top: auto;
    }
    .footer-links a {
      color: white;
      display: block;
      margin: 5px 0;
      text-decoration: none;
    }
  </style>
</head>
<body>


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

<!-- 🔁 Feedback -->
<div class="container mt-4">
  <?= $feedback ?>
</div>

<!-- Registration Form Section -->
<div class="register-form">
  <h3 class="text-center mb-4 text-success">Create Farmer Account</h3>
  <form action="register.php" method="POST">
    <div class="row g-3">
      <div class="col-md-6">
        <label for="fullname" class="form-label">Farmer Name</label>
        <input type="text" class="form-control" id="fullname" name="fullname" required>
      </div>
      <div class="col-md-6">
        <label for="location" class="form-label">Location</label>
        <input type="text" class="form-control" id="location" name="location" required>
      </div>
      <div class="col-md-6">
        <label for="phone" class="form-label">Phone</label>
        <input type="text" class="form-control" id="phone" name="phone" required>
      </div>
      <div class="col-md-6">
        <label for="device_id" class="form-label">Device ID</label>
        <input type="text" class="form-control" id="device_id" name="device_id" required placeholder="e.g. ZNZ-Farm01">
      </div>
      <div class="col-md-6">
        <label for="username" class="form-label">Username</label>
        <input type="text" class="form-control" id="username" name="username" required>
      </div>
      <div class="col-12">
        <label for="password" class="form-label">Password</label>
        <input type="password" class="form-control" id="password" name="password" required>
      </div>
    </div>
    <button type="submit" name="submit_farmer" class="btn btn-success w-100 mt-4">
      <i class="bi bi-person-plus-fill me-1"></i> Register
    </button>
    <p class="text-center mt-3">Already registered? <a href="login.php">Login here</a></p>
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
