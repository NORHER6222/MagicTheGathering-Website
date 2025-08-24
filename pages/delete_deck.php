<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

$deck_id = isset($_POST['deck_id']) ? (int)$_POST['deck_id'] : 0;
if ($deck_id <= 0) { header('Location: inventory.php'); exit; }

$username = $_SESSION['username'];
$stmt = $conn->prepare('SELECT id FROM users WHERE username = ? LIMIT 1');
$stmt->bind_param('s', $username);
$stmt->execute();
$stmt->bind_result($user_id);
$stmt->fetch();
$stmt->close();

// Verify deck ownership
$stmt = $conn->prepare('SELECT 1 FROM decks WHERE id = ? AND user_id = ?');
$stmt->bind_param('ii', $deck_id, $user_id);
$stmt->execute();
$owns = $stmt->fetch();
$stmt->close();
if (!$owns) { header('Location: inventory.php'); exit; }

$conn->begin_transaction();
try {
  // Return all cards to inventory
  $stmt = $conn->prepare('SELECT inventory_id, quantity FROM deck_cards WHERE deck_id = ?');
  $stmt->bind_param('i', $deck_id);
  $stmt->execute();
  $res = $stmt->get_result();
  while ($r = $res->fetch_assoc()) {
    $inv_id = (int)$r['inventory_id'];
    $qty = (int)$r['quantity'];
    $u = $conn->prepare('UPDATE inventory SET quantity = quantity + ? WHERE id = ? AND user_id = ?');
    $u->bind_param('iii', $qty, $inv_id, $user_id);
    $u->execute();
    $u->close();
  }
  $stmt->close();

  // Delete deck contents and the deck itself
  $stmt = $conn->prepare('DELETE FROM deck_cards WHERE deck_id = ?');
  $stmt->bind_param('i', $deck_id);
  $stmt->execute();
  $stmt->close();

  $stmt = $conn->prepare('DELETE FROM decks WHERE id = ? AND user_id = ?');
  $stmt->bind_param('ii', $deck_id, $user_id);
  $stmt->execute();
  $stmt->close();

  $conn->commit();
} catch (Exception $e) {
  $conn->rollback();
}
header('Location: inventory.php');
