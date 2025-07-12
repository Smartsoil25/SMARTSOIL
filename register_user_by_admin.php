<?php
session_start();
include("config.php");

// Hakikisha ni admin pekee
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Angalia kama form imetumwa
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['FarmerName'];
    $username = $_POST['username'];
    $phone = $_POST['phone'];
    $location = $_POST['location'];
    $device_id = $_POST['device_id'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = 'user'; // 🔹 Admin anasajili wakulima wa kawaida tu

    // Prepare statement ya kuingiza data
    $stmt = $conn->prepare("INSERT INTO farmers (FarmerName, Username, Phone, Location, device_id, Password, role) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $name, $username, $phone, $location, $device_id, $password, $role);

    if ($stmt->execute()) {
        header("Location: admin_dashboard.php?added=1");
        exit();
    } else {
        echo "<h4 style='color:red;'>Error: " . $stmt->error . "</h4>"; // ✅ Kuonyesha error halisi
        // header("Location: admin_dashboard.php?added=0");
        exit();
    }
} else {
    header("Location: admin_dashboard.php");
    exit();
}
?>
