<?php
require_once 'config.php';

try {
    // Hash the password "admin123"
    $email = "admin1@gmail.com";
    $password = "admin123";
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Update the password in the database
    $sql = "UPDATE administrators SET password = ? WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $hashed_password, $email);
    
    if ($stmt->execute()) {
        echo "Password updated successfully for " . $email;
        echo "<br>You can now login with:<br>";
        echo "Email: admin1@gmail.com<br>";
        echo "Password: admin123";
    } else {
        echo "Error updating password: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
