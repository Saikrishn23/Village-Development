<?php
require_once 'config.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // 1. First check current admin details
    $email = "admin1@gmail.com";
    $sql = "SELECT * FROM administrators WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $admin = $result->fetch_assoc();
        echo "Current admin found:<br>";
        echo "Email: " . $admin['email'] . "<br>";
        echo "Current password hash: " . $admin['password'] . "<br><br>";
    }

    // 2. Update with new password hash
    $new_password = "admin123";
    $new_hash = password_hash($new_password, PASSWORD_DEFAULT);
    
    $update_sql = "UPDATE administrators SET password = ? WHERE email = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("ss", $new_hash, $email);
    
    if ($update_stmt->execute()) {
        echo "Password updated successfully!<br>";
        echo "New password hash: " . $new_hash . "<br><br>";
        
        // 3. Verify the update
        $verify_sql = "SELECT password FROM administrators WHERE email = ?";
        $verify_stmt = $conn->prepare($verify_sql);
        $verify_stmt->bind_param("s", $email);
        $verify_stmt->execute();
        $verify_result = $verify_stmt->get_result();
        $updated_admin = $verify_result->fetch_assoc();
        
        echo "Testing password verification:<br>";
        if (password_verify($new_password, $updated_admin['password'])) {
            echo "✅ Password verification successful!<br>";
            echo "<br>You can now login with:<br>";
            echo "Email: admin1@gmail.com<br>";
            echo "Password: admin123<br>";
        } else {
            echo "❌ Password verification failed!<br>";
        }
    } else {
        echo "Error updating password: " . $update_stmt->error;
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
} finally {
    if (isset($stmt)) $stmt->close();
    if (isset($update_stmt)) $update_stmt->close();
    if (isset($verify_stmt)) $verify_stmt->close();
    $conn->close();
}
?>
