

<?php
$host = "localhost";
$user = "root"; // default for XAMPP
$password = ""; // blank default for XAMPP
$dbname = "smart_soil_system";

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
