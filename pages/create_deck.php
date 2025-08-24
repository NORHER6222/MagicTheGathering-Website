<?php
if (session_status() === PHP_SESSION_NONE) session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

$payload = json_decode(file_get_contents('php://input'), true);
$name = isset($payload['name']) ? trim($payload['name']) : '';
if ($name === '') { echo json_encode(['ok'=>false,'error'=>'invalid']); exit; }

$username = $_SESSION['username'];
$stmt = $conn->prepare('SELECT id FROM users WHERE username = ? LIMIT 1');
$stmt->bind_param('s', $username);
$stmt->execute();
$stmt->bind_result($user_id);
$stmt->fetch();
$stmt->close();
if (!$user_id) { echo json_encode(['ok'=>false,'error'=>'no-user']); exit; }

$stmt = $conn->prepare('INSERT INTO decks (user_id, name) VALUES (?, ?)');
$stmt->bind_param('is', $user_id, $name);
$ok = $stmt->execute();
$deck_id = $stmt->insert_id;
$stmt->close();

echo json_encode(['ok'=>$ok, 'id'=>$deck_id, 'name'=>$name]);
