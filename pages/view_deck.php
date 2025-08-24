<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

$deck_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$username = $_SESSION['username'];
$stmt = $conn->prepare('SELECT id FROM users WHERE username=? LIMIT 1');
$stmt->bind_param('s', $username);
$stmt->execute();
$stmt->bind_result($user_id);
$stmt->fetch();
$stmt->close();

// Ensure deck belongs to user
$stmt = $conn->prepare('SELECT name FROM decks WHERE id=? AND user_id=?');
$stmt->bind_param('ii', $deck_id, $user_id);
$stmt->execute();
$stmt->bind_result($deck_name);
$ok = $stmt->fetch();
$stmt->close();
if(!$ok){ header('Location: inventory.php'); exit; }
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>View Deck</title>
<link rel="stylesheet" href="../css/styles.css">
</head>
<body>
<?php if (file_exists(__DIR__ . '/../includes/navbar.php')) { include __DIR__ . '/../includes/navbar.php'; } else { include __DIR__ . '/../includes/nav.php'; } ?>

<h2>Deck: <?php echo htmlspecialchars($deck_name); ?></h2>
<p><a href="inventory.php">← Back to Inventory</a></p>

<!-- Rename deck -->
<form method="post" action="update_deck_name.php" style="margin:8px 0;">
  <input type="hidden" name="deck_id" value="<?php echo $deck_id; ?>">
  <label>Rename deck:&nbsp;
    <input type="text" name="name" value="<?php echo htmlspecialchars($deck_name); ?>" required>
  </label>
  <button type="submit">Save Name</button>
</form>

<!-- Delete deck (returns all cards to inventory, then deletes) -->
<form method="post" action="delete_deck.php" onsubmit="return confirm('Delete this deck and return all cards to inventory?');" style="margin:8px 0;">
  <input type="hidden" name="deck_id" value="<?php echo $deck_id; ?>">
  <button type="submit" style="color:#a00;">Delete Deck</button>
</form>

<table border="1" cellpadding="4" cellspacing="0">
  <thead><tr><th>Card</th><th>Quantity</th><th>Return</th></tr></thead>
  <tbody>
    <?php
      $sql = 'SELECT dc.inventory_id, i.card_name, dc.quantity
              FROM deck_cards dc JOIN inventory i ON i.id = dc.inventory_id
              WHERE dc.deck_id = ?';
      $stmt = $conn->prepare($sql);
      $stmt->bind_param('i', $deck_id);
      $stmt->execute();
      $res = $stmt->get_result();
      while ($r = $res->fetch_assoc()):
    ?>
      <tr>
        <td><?php echo htmlspecialchars($r['card_name']); ?></td>
        <td><?php echo (int)$r['quantity']; ?></td>
        <td>
          <form method="post" action="release_from_deck.php" style="display:inline;">
            <input type="hidden" name="deck_id" value="<?php echo $deck_id; ?>">
            <input type="hidden" name="inventory_id" value="<?php echo (int)$r['inventory_id']; ?>">
            <input type="number" name="quantity" min="1" max="<?php echo (int)$r['quantity']; ?>" value="1" style="width:60px">
            <button type="submit">Return</button>
          </form>
        </td>
      </tr>
    <?php endwhile; $stmt->close(); ?>
  </tbody>
</table>
</body>
</html>
