
<?php
session_start();
require 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $stmt = $conn->prepare("SELECT * FROM users WHERE username=?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($user = $res->fetch_assoc()) {
        if ($password === $user['password']) {
            $_SESSION['username'] = $username;
            header("Location: pages/home.php");
            exit();
        }
    }
    $error = "Invalid credentials.";
}
?>
<!DOCTYPE html>
<html>
            <video autoplay loop muted playsinline class="back-video">
                <source src="videos/backgroundvid.mp4" type="video/mp4">
                 Browser does not support video
            </video>
            
            <!-- 
                            Work In Progress
          
            <audio source src="audio/indexaudio.mp3" autoplay controls>
            </audio>
            
            -->
            <link rel="stylesheet" href="css/index.css"> 
<head><title>MTG Login</title></head>
<body>
<h1>Welcome To<h2>
<h2>MTG MANAGER</h2>

<style>
    h2 {
    text-align: center;
    color: rgb(181, 182, 183);
    font-size: 700%;

}

    #C{
    width: 152em;
    color: rgba(185, 181, 181, 1);
}

</style>

<?php if (isset($error)) echo "<p>$error</p>"; ?>
<form method="post">
    USERNAME: <input name="username"><br>
    PASSWORD: <input name="password" type="password"><br>
    <button id="LoginBtn" type="submit">LOGIN</button>
</form>

<div class="create-account-container">
    <a href="pages/register.php">Create An Account</a>
</div>
</body>
</html>
