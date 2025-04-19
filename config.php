<?php
$host = 'localhost';
$user = 'root';
$password = 'sai@123';  // Your MySQL password
$database = 'village_development';

try {
    $conn = new mysqli($host, $user, $password, $database);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
} catch (Exception $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
