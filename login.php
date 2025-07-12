<?php
session_start();
include("config.php");

if (isset($_POST['login'])) {
    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password'];

    $query = "SELECT * FROM farmers WHERE username = '$username'";
    $result = $conn->query($query);

    if ($result && $result->num_rows == 1) {
        $row = $result->fetch_assoc();

        if (password_verify($password, $row['password'])) {
            // ✅ Save user info to session
            $_SESSION['user_id'] = $row['farmer_id'];
            $_SESSION['role'] = $row['role'];
            $_SESSION['user'] = $row['FarmerName'];

            // ✅ Redirect to Dashboard
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Invalid password!";
        }
    } else {
        $error = "User not found!";
    }
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login - Smart Soil System</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap CSS + Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

  <style>
    body {
      background:  #e0e0e0;
      font-family: 'Segoe UI', sans-serif;
      min-height: 100vh;
      margin: 0;
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
    .login-container {
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 90vh;
    }
    .login-card {
      background-color: #fff;
      border-radius: 15px;
      padding: 40px;
      box-shadow: 0 8px 16px rgba(0,0,0,0.1);
      width: 100%;
      max-width: 450px;
    }
    .login-card h3 {
      color: #2e7d32;
      font-weight: 700;
    }
    .btn-success {
      background-color: #2e7d32;
      border: none;
    }
    .btn-success:hover {
      background-color: #1b5e20;
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


<div class="login-container">
  <div class="login-card">
    <h3 class="text-center mb-4">Smart Soil System Login</h3>
    <?php if (!empty($error)): ?>
      <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    <form method="post" action="">
      <div class="mb-3">
        <label class="form-label">Username</label>
        <input type="text" name="username" class="form-control" required />
      </div>
      <div class="mb-3">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" required />
      </div>
      <button type="submit" name="login" class="btn btn-success w-100">Login</button>
    </form>
    <div class="text-center mt-3">
      <small>Don't have an account? <a href="register.php">Register here</a></small>
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
