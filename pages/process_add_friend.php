
<?php
require '../includes/auth.php';
require '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['friend_username'])) {
    $username = $_SESSION['username'];
    $friend = $_POST['friend_username'];

    if ($username === $friend) {
        echo "<p>You can't add yourself as a friend.</p>";
        exit;
    }

    $user_id = $conn->query("SELECT id FROM users WHERE username='$username'")->fetch_assoc()['id'];
    $exists = $conn->query("SELECT * FROM users WHERE username='$friend'");

    if ($exists->num_rows > 0) {
        $stmt = $conn->prepare("INSERT IGNORE INTO friends (user_id, friend_username) VALUES (?, ?)");
        $stmt->bind_param("is", $user_id, $friend);
        if ($stmt->execute()) {
            echo "<p>Friend '$friend' added!</p>";
        } else {
            echo "<p>Could not add friend.</p>";
        }
    } else {
        echo "<p>User not found.</p>";
    }
}
?>
