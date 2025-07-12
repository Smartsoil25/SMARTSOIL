<?php
session_start();
include("config.php");

// Hakikisha ni Admin pekee
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Fetch all messages from farmers
$result = $conn->query("
    SELECT m.*, f.FarmerName 
    FROM farmer_messages m 
    JOIN farmers f ON m.farmer_id = f.farmer_id 
    ORDER BY m.timestamp DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Farmer Messages</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #0b470d;">
  <div class="container">
    <a class="navbar-brand fw-bold" href="admin_dashboard.php">
      <i class="bi bi-chat-dots me-2"></i> Admin - Farmer Messages
    </a>
    <a href="logout.php" class="btn btn-danger btn-sm ms-auto">
      <i class="bi bi-box-arrow-right me-1"></i> Logout
    </a>
  </div>
</nav>

<div class="container mt-5 mb-5">
    <h3 class="text-success mb-4">📬 Messages from Farmers</h3>
    <?php if ($result->num_rows > 0): ?>
        <table class="table table-bordered">
            <thead class="table-success">
                <tr>
                    <th>#</th>
                    <th>Farmer Name</th>
                    <th>Message</th>
                    <th>Sent At</th>
                </tr>
            </thead>
            <tbody>
                <?php $i = 1; while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $i++ ?></td>
                        <td><?= htmlspecialchars($row['FarmerName']) ?></td>
                        <td><?= nl2br(htmlspecialchars($row['message'])) ?></td>
                        <td><?= $row['timestamp'] ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-info">No messages found from farmers.</div>
    <?php endif; ?>
</div>

<footer class="footer text-center bg-dark text-white p-3">
  &copy; <?= date('Y') ?> Smart Soil Monitoring System - Admin Panel
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
