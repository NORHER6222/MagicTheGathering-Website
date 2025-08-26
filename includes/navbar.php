<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$logged_in = isset($_SESSION['username']);
$base = (strpos($_SERVER['REQUEST_URI'], '/pages/') !== false) ? '..' : '.';
?>
<nav style="margin:8px 0; color: white !important;">
  <a href="<?php echo $base; ?>/pages/home.php">Home</a> |
  <a href="<?php echo $base; ?>/pages/profile.php">Your Profile</a> |
  <a href="<?php echo $base; ?>/pages/inventory.php">Inventory</a> |
  <a href="<?php echo $base; ?>/pages/friends.php">Friends</a> |
  <a href="<?php echo $base; ?>/pages/help.php">Help</a> |
  <?php if ($logged_in): ?>
    | Logged in: <?php echo htmlspecialchars($_SESSION['username']); ?> | <a href="<?php echo $base; ?>/logout.php">Logout</a>
  <?php else: ?>
    | <a href="<?php echo $base; ?>/index.php">Login</a>
  <?php endif; ?>
</nav>
<hr>
