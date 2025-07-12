<?php
$crop = $_GET['crop'] ?? "No crop found";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Recommended Crop</title>
    <link href="css/bootstrap.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="alert alert-success text-center">
            <h3>🌾 Recommended Crop: <strong><?= htmlspecialchars($crop) ?></strong></h3>
        </div>
    </div>
</body>
</html>
