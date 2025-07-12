<?php
session_start();
include("config.php");

// Check if admin is logged in
if (!isset($_SESSION['admin'])) {
  header("Location: admin_login.php");
  exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $crop = $conn->real_escape_string($_POST['crop']);
  $conditions = $conn->real_escape_string($_POST['conditions']);

  $sql = "INSERT INTO crop_rules (crop, conditions, created_at) VALUES (?, ?, NOW())";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("ss", $crop, $conditions);

  if ($stmt->execute()) {
    echo '<script>alert("✅ Crop rule added successfully!"); window.location.href="admin_dashboard.php";</script>';
  } else {
    echo '<script>alert("❌ Failed to add crop rule."); window.location.href="admin_dashboard.php";</script>';
  }
  $stmt->close();
}
?>
