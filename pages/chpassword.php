<?php
require '../includes/auth.php';
require_once '../includes/db.php';

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $c_password = $_POST['c-password'];
    $n_password = $_POST['n-password'];
    $conf_password = $_POST['conf-password'];

    if ($n_password !== $conf_password) {
        $error = "Passwords do not match, Please Ensure Passwords Match!";
    } elseif (strlen($n_password) < 8) {
        $error = "New password must be at least 8 characters!";
    } else {
      
        $stmt = $conn->prepare("SELECT password FROM users WHERE username = ?");
        if ($stmt ===false) {
          $error = "Failed to prepare statement: " .$conn->error;
        } else {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
                $user = $result->fetch_assoc();
                if ($c_password === $user['password']) { 
                    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE username = ?");
                    if ($stmt === false) {
                        $error = "Failed to prepare update statement: " . $conn->error;
                    } else {
                        $stmt->bind_param("ss", $n_password, $username);
                        if ($stmt->execute()) {
                            $message = "Password has been changed successfully!";
                            header("Location: profile.php");
                            exit();
                        } else {
                            $error = "Failed to update password: " . $conn->error;
                        }
                        $stmt->close();
                    }
                } else {
                    $error = "Username or Password is Invalid, Please Try Again!";
                }
           
              }
        }
    }
}
?>
