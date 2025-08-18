
<?php if (isset($_SESSION['username'])): ?>
<nav>
    <a href="/mtg-website/pages/inventory.php">Inventory</a> |
    <a href="/mtg-website/pages/friends.php">Friends</a> |
    <a href="/mtg-website/pages/help.php">Help</a> |
    <a href="/mtg-website/logout.php">Logout (<?php echo $_SESSION['username']; ?>)</a>
</nav>
<hr>
<?php endif; ?>
