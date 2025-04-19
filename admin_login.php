<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    try {
        // Log login attempt
        error_log("Login attempt for email: " . $email);

        $sql = "SELECT * FROM administrators WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $admin = $result->fetch_assoc();
            error_log("Stored password hash: " . $admin['password']);
            error_log("Attempting to verify password...");
            
            if (password_verify($password, $admin['password'])) {
                error_log("Password verification successful");
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_name'] = $admin['fullname'];
                echo "success";
            } else {
                error_log("Password verification failed");
                error_log("Provided password: " . $password);
                echo "Invalid password!";
            }
        } else {
            error_log("No admin found with email: " . $email);
            echo "Email not found!";
        }
        
    } catch (Exception $e) {
        error_log("Error in admin login: " . $e->getMessage());
        echo "Error: " . $e->getMessage();
    } finally {
        if (isset($stmt)) $stmt->close();
        $conn->close();
    }
}
?>
