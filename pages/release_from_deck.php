<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

$deck_id = isset($_POST['deck_id']) ? (int)$_POST['deck_id'] : 0;
$inventory_id = isset($_POST['inventory_id']) ? (int)$_POST['inventory_id'] : 0;
$qty = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 0;
if ($deck_id <= 0 || $inventory_id <= 0 || $qty <= 0) { header('Location: inventory.php'); exit; }

$username = $_SESSION['username'];
$stmt = $conn->prepare('SELECT id FROM users WHERE username=? LIMIT 1');
$stmt->bind_param('s', $username);
$stmt->execute();
$stmt->bind_result($user_id);
$stmt->fetch();
$stmt->close();

$stmt = $conn->prepare('SELECT 1 FROM decks WHERE id = ? AND user_id = ?');
$stmt->bind_param('ii', $deck_id, $user_id);
$stmt->execute();
$has_deck = $stmt->fetch();
$stmt->close();
if (!$has_deck) { header('Location: inventory.php'); exit; }

$conn->begin_transaction();
try {
  $stmt = $conn->prepare('UPDATE deck_cards SET quantity = quantity - ? WHERE deck_id = ? AND inventory_id = ? AND quantity >= ?');
  $stmt->bind_param('iiii', $qty, $deck_id, $inventory_id, $qty);
  $stmt->execute();
  if ($stmt->affected_rows !== 1) { throw new Exception('deck-update-failed'); }
  $stmt->close();

  $stmt = $conn->prepare('DELETE FROM deck_cards WHERE deck_id = ? AND inventory_id = ? AND quantity <= 0');
  $stmt->bind_param('ii', $deck_id, $inventory_id);
  $stmt->execute();
  $stmt->close();

  $stmt = $conn->prepare('UPDATE inventory SET quantity = quantity + ? WHERE id = ? AND user_id = ?');
  $stmt->bind_param('iii', $qty, $inventory_id, $user_id);
  $stmt->execute();
  if ($stmt->affected_rows !== 1) { throw new Exception('inv-update-failed'); }
  $stmt->close();

  $conn->commit();
} catch (Exception $e) {
  $conn->rollback();
}
header('Location: view_deck.php?id=' . $deck_id);
