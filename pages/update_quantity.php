<?php
if (session_status() === PHP_SESSION_NONE) session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php'; 

$input = json_decode(file_get_contents('php://input'), true);
$inventory_id = isset($input['inventory_id']) ? (int)$input['inventory_id'] : 0;
$quantity = isset($input['quantity']) ? (int)$input['quantity'] : -1;
if ($inventory_id <= 0 || $quantity < 0) { echo json_encode(['ok'=>false,'error'=>'invalid']); exit; }

$username = $_SESSION['username'];
$stmt = $conn->prepare('SELECT id FROM users WHERE username = ? LIMIT 1');
$stmt->bind_param('s', $username);
$stmt->execute();
$stmt->bind_result($user_id);
$stmt->fetch();
$stmt->close();
if (!$user_id) { echo json_encode(['ok'=>false,'error'=>'no-user']); exit; }

$stmt = $conn->prepare('UPDATE inventory SET quantity = ? WHERE id = ? AND user_id = ?');
$stmt->bind_param('iii', $quantity, $inventory_id, $user_id);
$stmt->execute();
$ok = $stmt->affected_rows >= 0;
$stmt->close();

echo json_encode(['ok'=>$ok]);
