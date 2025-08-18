<?php
require '../includes/auth.php';
require_once '../includes/db.php';

$deck_id = intval($_GET['id'] ?? 0);
if ($deck_id<=0){ echo "Deck not found"; exit; }

$user = $_SESSION['username'] ?? null;
$stmt = $conn->prepare("SELECT id FROM users WHERE username=?");
$stmt->bind_param("s",$user);
$stmt->execute();
$uid = $stmt->get_result()->fetch_assoc()['id'];

$chk = $conn->prepare("SELECT id, name, created_at FROM decks WHERE id=? AND user_id=?");
$chk->bind_param("ii",$deck_id,$uid);
$chk->execute();
$deck = $chk->get_result()->fetch_assoc();
if(!$deck){ echo "Deck not found"; exit; }

$q = $conn->prepare("SELECT dc.quantity, i.card_name FROM deck_cards dc JOIN inventory i ON dc.inventory_id=i.id WHERE dc.deck_id=? ORDER BY i.card_name");
$q->bind_param("i",$deck_id);
$q->execute();
$res = $q->get_result();
$rows = [];
while($row=$res->fetch_assoc()) $rows[]=$row;
?>
<!DOCTYPE html>
<html>
<head>
    <title>Deck: <?= htmlspecialchars($deck['name']) ?></title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include '../includes/nav.php'; ?>
<script>
function renameDeck(id){
  const input = document.getElementById('deckNameInput');
  const name = input.value.trim();
  if(!name){ alert('Name cannot be empty'); return; }
  const params = new URLSearchParams({deck_id:id, name});
  fetch('update_deck_name.php',{method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body: params.toString()})
    .then(r=>{ if(!r.ok) return r.text().then(t=>Promise.reject(t)); return r.text(); })
    .then(()=>{ document.getElementById('deckNameDisplay').textContent = name; })
    .catch(err=>alert('Error: '+err));
}
</script>
<h2>Deck: <span id="deckNameDisplay"><?= htmlspecialchars($deck['name']) ?></span>
<input id="deckNameInput" type="text" value="<?= htmlspecialchars($deck['name']) ?>" style="margin-left:10px;">
<button onclick="renameDeck(<?= (int)$deck['id'] ?>)">Save Name</button></h2>
<p>Created: <?= htmlspecialchars($deck['created_at']) ?></p>
<table border="1" cellpadding="6" cellspacing="0">
<tr><th>Card</th><th>In Deck</th><th>Return Qty</th><th>Action</th></tr>
<?php foreach($rows as $r): ?>
<tr>
  <td><?= htmlspecialchars($r['card_name']) ?></td>
  <td><?= (int)$r['quantity'] ?></td>
  <td>
    <form method="post" action="release_from_deck.php" style="display:inline">
      <input type="hidden" name="deck_id" value="<?= (int)$deck['id'] ?>">
      <?php
        // Look up the inventory_id for this card name for the current user
        $lookup = $conn->prepare("SELECT id FROM inventory WHERE user_id=? AND card_name=? LIMIT 1");
        $lookup->bind_param("is", $uid, $r['card_name']);
        $lookup->execute();
        $invRow = $lookup->get_result()->fetch_assoc();
        $invId = $invRow ? (int)$invRow['id'] : 0;
      ?>
      <input type="hidden" name="inventory_id" value="<?= $invId ?>">
      <input type="number" name="quantity" min="1" max="<?= (int)$r['quantity'] ?>" value="1" style="width:70px">
      <button type="submit">Return to Inventory</button>
    </form>
  </td>
  <td></td>
</tr>
<?php endforeach; ?>
</table>
</body>
</html>