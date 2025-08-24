<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../includes/auth.php';
?>
<!doctype html>
<html>


<head>
<meta charset="utf-8">
<title>Inventory</title>
<link rel="stylesheet" href="../css/styles.css">
<link rel="stylesheet" href="../css/inventory.css">
</head>
<body>
<?php if (file_exists(__DIR__ . '/../includes/navbar.php')) { include __DIR__ . '/../includes/navbar.php'; } else { include __DIR__ . '/../includes/nav.php'; } ?>

<div>
    <a href="home.php"><img class="logo" src="../img/mtgmanager.png" alt=""></a>
</div>


<p><a href="search_cards.php">Add Any MTG Card (Scryfall)</a></p>

<h2>Your Inventory</h2>
<div id="inv-root">
  <table id="inv-table" border="1" cellpadding="4" cellspacing="0">
    <thead>
      <tr><th>Card</th><th>Quantity</th><th>Assign</th><th>Actions</th></tr>
    </thead>
    <tbody id="inv-tbody">
      <tr><td colspan="4">Loading...</td></tr>
    </tbody>
  </table>
</div>

<h2>Decks</h2>
<div id="decks-root">
  <div style="margin:6px 0;">
    <input id="newDeckName" placeholder="New deck name">
    <button id="createDeckBtn">Create</button>
  </div>
  <ul id="deckList"></ul>
</div>

<script>
  window.MTG_ENDPOINTS = {
    getInventory: 'get_inventory_json.php',
    updateQty: 'update_quantity.php',
    removeCard: 'remove_card.php',
    getDecks: 'get_decks_json.php',
    createDeck: 'create_deck.php',
    transferToDeck: 'transfer_to_deck.php',
    viewDeck: 'view_deck.php'
  };
</script>
<script src="../assets/js/inventory.js"></script>
</body>
</html>
