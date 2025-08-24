<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

$deck_id = isset($_POST['deck_id']) ? (int)$_POST['deck_id'] : 0;
$name = isset($_POST['name']) ? trim($_POST['name']) : '';
if ($deck_id <= 0 || $name === '') { header('Location: inventory.php'); exit; }

$username = $_SESSION['username'];
$stmt = $conn->prepare('SELECT id FROM users WHERE username = ? LIMIT 1');
$stmt->bind_param('s', $username);
$stmt->execute();
$stmt->bind_result($user_id);
$stmt->fetch();
$stmt->close();

$stmt = $conn->prepare('UPDATE decks SET name = ? WHERE id = ? AND user_id = ?');
$stmt->bind_param('sii', $name, $deck_id, $user_id);
$stmt->execute();
$stmt->close();

header('Location: view_deck.php?id=' . $deck_id);
