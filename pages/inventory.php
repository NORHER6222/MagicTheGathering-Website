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

<footer>
    <section class="footer">
       <h4 class="sec2"></h4>
       <p class="footnotes">Wizards of the Coast, Magic: The Gathering, and their logos are trademarks of Wizards of the Coast LLC in the United States and other countries. © 1993-2025 Wizards. All Rights Reserved.<br><br>MTG MANAGER is not affiliated with, endorsed, sponsored, or specifically approved by Wizards of the Coast LLC. MTG MANAGER may use the trademarks and other intellectual property of Wizards of the Coast LLC, which is permitted under Wizards' Fan Site Policy. MAGIC: THE GATHERING® is a trademark of Wizards of the Coast. For more information about Wizards of the Coast or any of Wizards' trademarks or other intellectual property, please visit their website at https://company.wizards.com/.<br><br>© 2025 MTG MANAGER·Terms of Service·Privacy Policy·Affiliate Disclosures·Version 2025.v01</p>
    </section>
</footer>

</html>
