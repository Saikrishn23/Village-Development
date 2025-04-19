<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Sanitize input
        $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
        $phone = filter_var($_POST['phone'], FILTER_SANITIZE_STRING);
        $address = filter_var($_POST['address'], FILTER_SANITIZE_STRING);
        $issue_description = filter_var($_POST['issue_description'], FILTER_SANITIZE_STRING);
        $image_path = null;

        // Handle image upload
        if(isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            $filename = $_FILES['image']['name'];
            $filetype = pathinfo($filename, PATHINFO_EXTENSION);

            if(in_array(strtolower($filetype), $allowed)) {
                $maxsize = 5 * 1024 * 1024; // 5MB
                if($_FILES['image']['size'] < $maxsize) {
                    // Create uploads directory if it doesn't exist
                    if (!file_exists('uploads')) {
                        mkdir('uploads', 0777, true);
                    }

                    $new_filename = uniqid() . '.' . $filetype;
                    $upload_path = 'uploads/' . $new_filename;

                    if(move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                        $image_path = $upload_path;
                    }
                }
            }
        }

        // Insert into database
        $sql = "INSERT INTO issues (name, phone, address, issue_description, image_path) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", $name, $phone, $address, $issue_description, $image_path);

        if ($stmt->execute()) {
            echo "success";
        } else {
            throw new Exception("Database error: " . $conn->error);
        }

    } catch (Exception $e) {
        error_log($e->getMessage());
        echo "Error: " . $e->getMessage();
    }
}
?>
