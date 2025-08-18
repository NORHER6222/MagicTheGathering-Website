<?php
require '../includes/auth.php';
require '../includes/db.php';

$username = $_SESSION['username'];
$user_id = $conn->query("SELECT id FROM users WHERE username='$username'")->fetch_assoc()['id'];
$res = $conn->query("SELECT friend_username FROM friends WHERE user_id=$user_id ORDER BY friend_username");
$count = 0;
while ($row = $res->fetch_assoc()):
    $f = htmlspecialchars($row['friend_username'], ENT_QUOTES);
    $style = $count < 10 ? '' : 'display:none;';
?>
    <div class="friend-entry" style="<?php echo $style; ?>">
        <span><a href="view_inventory.php?user=<?php echo urlencode($f); ?>"><?php echo $f; ?></a></span>
        <button onclick="removeFriend('<?php echo addslashes($f); ?>')">Remove</button>
    </div>
<?php
    $count++;
endwhile;
?>