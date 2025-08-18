<?php
require '../includes/auth.php';
require '../includes/db.php';

header('Content-Type: text/plain');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['card_name']) && isset($_POST['quantity'])) {
    $username = $_SESSION['username'];
    $stmtU = $conn->prepare("SELECT id FROM users WHERE username=?");
    $stmtU->bind_param("s", $username);
    $stmtU->execute();
    $user_id = $stmtU->get_result()->fetch_assoc()['id'];

    $card = trim($_POST['card_name']);
    $qty = max(1, (int)$_POST['quantity']);

    // Insert or increment if duplicate name exists for the user
    $stmt = $conn->prepare("INSERT INTO inventory (user_id, card_name, quantity)
                            VALUES (?, ?, ?)
                            ON DUPLICATE KEY UPDATE quantity = quantity + VALUES(quantity)");
    $stmt->bind_param("isi", $user_id, $card, $qty);
    if ($stmt->execute()) {
        // affected_rows: 1 => inserted, 2 => updated existing
        if ($stmt->affected_rows == 1) {
            echo "inserted";
        } else {
            echo "incremented";
        }
    } else {
        http_response_code(500);
        echo "error";
    }
} else {
    http_response_code(400);
    echo "invalid";
}
?>