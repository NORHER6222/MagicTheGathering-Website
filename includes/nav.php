<?php
if(session_status() == PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/db.php';

$nav_username = $_SESSION['username'] ?? '';
$nav_avatar = '';
if ($nav_username) {
    if ($stmt = $conn->prepare("SELECT COALESCE(avatar,'') FROM users WHERE username = ?")) {
        $stmt->bind_param("s", $nav_username);
        $stmt->execute();
        $stmt->bind_result($nav_avatar);
        $stmt->fetch();
        $stmt->close();
    }
}
if (!$nav_avatar) { $nav_avatar = "/mtg-website/img/avatars/avatar1.svg"; }
?>
<nav>
  <a href="/mtg-website/pages/home.php">Home</a> |
  <a href="/mtg-website/pages/inventory.php">Inventory</a> |
  <a href="/mtg-website/pages/friends.php">Friends</a> |  <a href="/mtg-website/pages/profile.php">Profile</a> |
  <a href="/mtg-website/pages/help.php">Help</a> |
  <span style="float:right; display:inline-flex; align-items:center; gap:8px;">
    <img src="<?= htmlspecialchars($nav_avatar) ?>" alt="avatar" style="width:24px; height:24px; border-radius:50%; vertical-align:middle;">
    <a href="/mtg-website/logout.php">Logout (<?= htmlspecialchars($nav_username) ?>)</a>
  </span>
</nav>
<hr>
