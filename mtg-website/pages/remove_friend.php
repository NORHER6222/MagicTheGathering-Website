<?php
require '../includes/auth.php';
require '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['friend'])) {
    $username = $_SESSION['username'];
    $user_id = $conn->query("SELECT id FROM users WHERE username='$username'")->fetch_assoc()['id'];
    $friend = $_POST['friend'];
    $stmt = $conn->prepare("DELETE FROM friends WHERE user_id = ? AND friend_username = ?");
    $stmt->bind_param("is", $user_id, $friend);
    $stmt->execute();
}
?>
