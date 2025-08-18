
<?php
require '../includes/db.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
    $stmt->bind_param("ss", $username, $password);
    if ($stmt->execute()) {
        header("Location: ../index.php");
    } else {
        $error = "Username already exists.";
    }
}
?>
<!DOCTYPE html>
<html>
    <video autoplay loop muted playsinline class="back-video">
                <source src="../backgroundvid.mp4" type="video/mp4">
                 Browser does not support video
            </video>
   <link rel="stylesheet" href="css/register.css"> 

<head><title>Register</title><link rel="stylesheet" href="../css/register.css"></head>
<body>
<h2>CREATE ACCOUNT</h2>
<?php if (isset($error)) echo "<p>$error</p>"; ?>
<form method="post">
    Username: <input name="username"><br>
    Password: <input name="password" type="password"><br>
    <button type="submit">Register</button>
</form>
</body>
</html>
