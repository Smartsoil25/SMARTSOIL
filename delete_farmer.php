<?php
session_start();
include("config.php");

// Hakikisha ni admin tu anayeweza kufuta
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Angalia kama farmer_id imetumwa kupitia URL
if (isset($_GET['id'])) {
    $farmer_id = $_GET['id'];

    // Andaa query ya kufuta
    $stmt = $conn->prepare("DELETE FROM farmers WHERE farmer_id = ?");
    $stmt->bind_param("i", $farmer_id);

    if ($stmt->execute()) {
        // ✅ Futa vizuri, rudisha kwenye dashboard
        header("Location: admin_dashboard.php?deleted=1");
        exit();
    } else {
        // ⚠️ Kosa lililotokea
        echo "<div class='alert alert-danger text-center'>Failed to delete farmer: " . $stmt->error . "</div>";
    }
} else {
    // Kama hakuna ID iliyotumwa
    header("Location: admin_dashboard.php");
    exit();
}
?>
