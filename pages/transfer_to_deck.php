<?php
if (session_status() === PHP_SESSION_NONE) session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

$data = json_decode(file_get_contents('php://input'), true);
$inventory_id = isset($data['inventory_id']) ? (int)$data['inventory_id'] : 0;
$deck_id = isset($data['deck_id']) ? (int)$data['deck_id'] : 0;
$qty = isset($data['quantity']) ? (int)$data['quantity'] : 0;

if ($inventory_id <= 0 || $deck_id <= 0 || $qty <= 0) {
  echo json_encode(['ok'=>false,'error'=>'invalid']); exit;
}

$username = $_SESSION['username'];
$stmt = $conn->prepare('SELECT id FROM users WHERE username=? LIMIT 1');
$stmt->bind_param('s', $username);
$stmt->execute();
$stmt->bind_result($user_id);
$stmt->fetch();
$stmt->close();
if (!$user_id) { echo json_encode(['ok'=>false,'error'=>'no-user']); exit; }

$stmt = $conn->prepare('SELECT quantity FROM inventory WHERE id = ? AND user_id = ?');
$stmt->bind_param('ii', $inventory_id, $user_id);
$stmt->execute();
$stmt->bind_result($inv_qty);
$has_inv = $stmt->fetch();
$stmt->close();
if (!$has_inv) { echo json_encode(['ok'=>false,'error'=>'inventory-not-found']); exit; }
if ($inv_qty < $qty) { echo json_encode(['ok'=>false,'error'=>'not-enough-inventory']); exit; }

$stmt = $conn->prepare('SELECT 1 FROM decks WHERE id = ? AND user_id = ?');
$stmt->bind_param('ii', $deck_id, $user_id);
$stmt->execute();
$has_deck = $stmt->fetch();
$stmt->close();
if (!$has_deck) { echo json_encode(['ok'=>false,'error'=>'deck-not-found']); exit; }

$conn->begin_transaction();
try {
  $stmt = $conn->prepare('UPDATE inventory SET quantity = quantity - ? WHERE id = ? AND user_id = ? AND quantity >= ?');
  $stmt->bind_param('iiii', $qty, $inventory_id, $user_id, $qty);
  $stmt->execute();
  if ($stmt->affected_rows !== 1) { throw new Exception('inv-update-failed'); }
  $stmt->close();

  $stmt = $conn->prepare('INSERT INTO deck_cards (deck_id, inventory_id, quantity) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE quantity = quantity + VALUES(quantity)');
  $stmt->bind_param('iii', $deck_id, $inventory_id, $qty);
  if (!$stmt->execute()) { throw new Exception('deck-insert-failed'); }
  $stmt->close();

  $stmt = $conn->prepare('SELECT quantity FROM inventory WHERE id=?');
  $stmt->bind_param('i', $inventory_id);
  $stmt->execute();
  $stmt->bind_result($new_qty);
  $stmt->fetch();
  $stmt->close();

  $conn->commit();
  echo json_encode(['ok'=>true, 'new_qty'=>(int)$new_qty]);
} catch (Exception $e) {
  $conn->rollback();
  echo json_encode(['ok'=>false,'error'=>$e->getMessage()]);
}
