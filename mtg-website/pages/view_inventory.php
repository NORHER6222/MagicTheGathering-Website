<?php
require '../includes/auth.php';
require '../includes/db.php';

if (!isset($_GET['user'])) {
    header('Location: friends.php');
    exit;
}
$viewUser = $_GET['user'];
$viewUserEsc = $conn->real_escape_string($viewUser);
$idRes = $conn->query("SELECT id FROM users WHERE username='$viewUserEsc'");
if ($idRes->num_rows === 0) {
    echo "User not found.";
    exit;
}
$viewId = $idRes->fetch_assoc()['id'];
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo htmlspecialchars($viewUser); ?>'s Inventory</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .box-section { margin:20px 0; padding:10px; background:#f9f9f9; border:1px solid #ccc; }
        .card-entry { padding:6px; border-bottom:1px solid #ddd; }
    </style>
</head>
<body>
<?php include '../includes/nav.php'; ?>
<h2><?php echo htmlspecialchars($viewUser); ?>'s Inventory</h2>
<div class="box-section">
<?php
$inv = $conn->query("SELECT card_name, quantity FROM inventory WHERE user_id=$viewId ORDER BY card_name");
while ($row = $inv->fetch_assoc()) {
    echo "<div class='card-entry'>" . htmlspecialchars($row['card_name']) . " (x" . intval($row['quantity']) . ")</div>";
}
?>
</div>
</body>
</html>
