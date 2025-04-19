<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    $sql = "SELECT id, password, fullname FROM volunteers WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $volunteer = $result->fetch_assoc();
        if (password_verify($password, $volunteer['password'])) {
            $_SESSION['volunteer_id'] = $volunteer['id'];
            $_SESSION['volunteer_name'] = $volunteer['fullname'];
            echo "success";
        } else {
            echo "Invalid password";
        }
    } else {
        echo "Email not found";
    }
    $conn->close();
}
?>
