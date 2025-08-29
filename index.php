
<?php
session_start();
require 'includes/db.php';
@include_once 'config.php';
require_once 'includes/recaptcha.php';
$SITE_KEY = defined('RECAPTCHA_SITE_KEY') ? RECAPTCHA_SITE_KEY : '6LeuPLUrAAAAAJkHBswa6WYRiwmQHYCyU9chh_2b';
$SECRET_KEY = defined('RECAPTCHA_SECRET_KEY') ? RECAPTCHA_SECRET_KEY : '6LeuPLUrAAAAAL1bd-VWT6OUCuApeMDGuSPUJtyU';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_recaptcha($SECRET_KEY)) { $error = 'reCAPTCHA failed. Please try again.'; }
    else 
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
<head><title>MTG Login</title><script src="https://www.google.com/recaptcha/enterprise.js" async defer></script>
</head>
<body>
<?php
if ((isset($_GET['captcha']) && $_GET['captcha']==='0') || (!empty($error))) {
    $msg = !empty($error) ? $error : 'reCAPTCHA failed. Please try again.';
    $safe = htmlspecialchars($msg, ENT_QUOTES, 'UTF-8');
    echo "<script>alert('".$safe."');</script>";
}
?>
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
<form method="post" action="index.php">
    USERNAME: <input name="username"><br>
    PASSWORD: <input name="password" type="password"><br>
    
    <div class="g-recaptcha" data-sitekey="<?php echo htmlspecialchars($SITE_KEY, ENT_QUOTES, 'UTF-8'); ?>" data-action="LOGIN"></div>
    <button id="LoginBtn" type="submit">LOGIN</button>
</form>

<div class="create-account-container">
    <a href="pages/register.php">Create An Account</a>
</div>
</body>
</html>
