<?php
session_start();
require '../includes/db.php';
if(!isset($_SESSION['username']) || !isset($_POST['card_id'])){ http_response_code(400); exit; }
$username = $_SESSION['username'];
$user_id = $conn->query("SELECT id FROM users WHERE username='$username'")->fetch_assoc()['id'];
$id = (int)$_POST['card_id'];
$stmt = $conn->prepare("DELETE FROM inventory WHERE id=? AND user_id=?");
$stmt->bind_param("ii", $id, $user_id);
$stmt->execute();
?>