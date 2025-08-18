<?php
require '../includes/auth.php';
require '../includes/db.php';
$username = $_SESSION['username'];
$user_id = $conn->query("SELECT id FROM users WHERE username='$username'")->fetch_assoc()['id'];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Home</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .box-section { margin:20px 0; padding:10px; background:#f9f9f9; border:1px solid #ccc; }
        .entry { padding:6px; border-bottom:1px solid #ddd; }
        .entry a { text-decoration:none; color:#333; }
    </style>
</head>
<body>
<?php include '../includes/nav.php'; ?>
<h2>Home Dashboard</h2>
<h3>Your Friends</h3>
<div class="box-section">
<?php
$fRes = $conn->query("SELECT friend_username FROM friends WHERE user_id=$user_id ORDER BY friend_username LIMIT 5");
while ($f = $fRes->fetch_assoc()):
    $name = htmlspecialchars($f['friend_username'], ENT_QUOTES);
?>
    <div class="entry"><a href="view_inventory.php?user=<?php echo urlencode($name); ?>"><?php echo $name; ?></a></div>
<?php endwhile; ?>
</div>
<h3>Your Inventory</h3>
<div class="box-section">
<?php
$iRes = $conn->query("SELECT card_name, quantity FROM inventory WHERE user_id=$user_id ORDER BY card_name LIMIT 5");
while ($i = $iRes->fetch_assoc()):
?>
    <div class="entry"><?php echo htmlspecialchars($i['card_name']); ?> (x<?php echo intval($i['quantity']); ?>)</div>
<?php endwhile; ?>
</div>
</body>
</html>