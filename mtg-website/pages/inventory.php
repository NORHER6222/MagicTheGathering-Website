<?php
require '../includes/auth.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Inventory</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .box-section { max-height:400px; overflow-y:auto; border:1px solid #ccc; padding:10px; margin-top:10px; border-radius:6px; }
        .card-entry { border:1px solid #e5e7eb; padding:8px; border-radius:6px; margin:6px 0; }
        .grid-head, .grid-row { display:grid; grid-template-columns: 1.2fr 0.6fr 0.6fr 0.8fr auto; gap:8px; align-items:center; }
        .grid-head { font-weight:700; margin:4px 0; }
        .hint { color:#6b7280; margin:4px 0 8px; }
        form.inline { display:inline; }
        button { cursor:pointer; }
        .section { margin-bottom:20px; }
    </style>
</head>
<body>
<?php include '../includes/nav.php'; ?>

<h2>Inventory</h2>

<div class="section">
  <h3>Build a Deck</h3>
  <form method="post" action="process_add_deck.php" class="inline">
      <label for="deckname">New Deck Name:</label>
      <input id="deckname" type="text" name="name" required>
      <button type="submit">Create Deck</button>
  </form>
  <div id="decks-list" class="box-section" style="margin-top:8px;"></div>
</div>

<div class="section">
  <h3>Your Inventory</h3>
  <p class="hint">On-hand = Inventory + In Decks. Hover the ⓘ to see which decks contain that card. Use the controls to move a specific quantity into a deck.</p>
  <div class="box-section">
    <div class="grid-head">
      <div>Card</div><div>On-hand</div><div>In Decks</div><div>Inventory</div><div>Move to Deck</div>
    </div>
    <div id="inventory-results"></div>
  </div>
</div>

<div class="section">
  <h2>Add Any MTG Card</h2>
  <p>Type a full or partial card name and click "Search Library" to fetch from Scryfall, then add to your inventory.</p>
  <input type="text" id="scryQuery" placeholder="e.g., Lightning Bolt">
  <button type="button" onclick="scrySearch()">Search Library</button>
  <div id="scry-results" class="box-section"></div>
</div>

<div class="section">
  <h3>Quick Add (manual)</h3>
  <form id="quickAddForm" onsubmit="quickAdd(event)">
    <input type="text" id="qa_name" placeholder="Card name" required>
    <input type="number" id="qa_qty" value="1" min="1" style="width:80px">
    <button type="submit">Add</button>
  </form>
</div>

<script>
function loadDecks(){
  return fetch('get_decks.php').then(r=>r.json()).then(decks => {
    const list = document.getElementById('decks-list');
    if(!list) return decks;
    list.innerHTML = (decks.length ? '' : '<p class="hint">No decks yet.</p>');
    const ul = document.createElement('ul');
    decks.forEach(d => {
      const li = document.createElement('li');
      li.innerHTML = `<a href="view_deck.php?id=${d.id}">${d.name}</a>`;
      ul.appendChild(li);
    });
    list.appendChild(ul);
    return decks;
  });
}

function renderInventory(){
  const container = document.getElementById('inventory-results');
  if(!container) return;
  container.innerHTML = '';

  Promise.all([
    fetch('get_inventory_totals.php').then(r=>r.json()),
    loadDecks()
  ]).then(([items, decks])=>{
    items.forEach(item => {
      const row = document.createElement('div');
      row.className = 'grid-row card-entry';

      const name = document.createElement('div');
      name.textContent = item.card_name;
      name.style.fontWeight='600';

      const total = document.createElement('div');
      total.textContent = item.on_hand_total;

      const indecks = document.createElement('div');
      const infoBtn = document.createElement('button');
      infoBtn.type='button';
      infoBtn.textContent = item.in_decks + ' ⓘ';
      infoBtn.title = (item.breakdown && item.breakdown.length)
        ? item.breakdown.map(b => `${b.deck} x${b.qty}`).join('\n')
        : 'Not in any deck';
      indecks.appendChild(infoBtn);

      const avail = document.createElement('div');
      avail.textContent = item.quantity;

      const controls = document.createElement('div');
      controls.style.textAlign = 'right';
      const sel = document.createElement('select');
      (decks || []).forEach(d => {
        const o = document.createElement('option');
        o.value = d.id; o.textContent = d.name;
        sel.appendChild(o);
      });
      const qty = document.createElement('input'); qty.type='number'; qty.min='1'; qty.value='1'; qty.style.width='60px';
      const btn = document.createElement('button'); btn.type='button'; btn.textContent='Add to Deck';

      btn.onclick = ()=>{
        const moveQty = parseInt(qty.value,10) || 1;
        if (moveQty > item.quantity) {
          alert('You only have ' + item.quantity + ' available in inventory.');
          return;
        }
        const params = new URLSearchParams({deck_id: sel.value, inventory_id: item.id, quantity: moveQty});
        fetch('assign_to_deck.php',{method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body: params.toString()})
          .then(r=>{ if(!r.ok) return r.text().then(t=>Promise.reject(t)); location.reload(); });
      };

      controls.appendChild(sel);
      controls.appendChild(qty);
      controls.appendChild(btn);

      row.appendChild(name);
      row.appendChild(total);
      row.appendChild(indecks);
      row.appendChild(avail);
      row.appendChild(controls);
      container.appendChild(row);
    });
  });
}

function scrySearch(){
  const q = document.getElementById('scryQuery').value.trim();
  if(!q){ return; }
  const url = 'https://api.scryfall.com/cards/search?q=' + encodeURIComponent(q);
  fetch(url).then(r=>r.json()).then(js=>{
    const c = document.getElementById('scry-results'); c.innerHTML='';
    if(!js.data){ c.textContent='No results.'; return; }
    js.data.slice(0,20).forEach(card=>{
      const row = document.createElement('div');
      row.className = 'card-entry';
      const name = card.name;
      row.innerHTML = `<strong>${name}</strong> <em>${(card.set_name||'')}</em>`;
      const addBtn = document.createElement('button'); addBtn.textContent='Add to My Inventory'; addBtn.style.marginLeft='10px';
      const qty = document.createElement('input'); qty.type='number'; qty.value='1'; qty.min='1'; qty.style.width='60px'; qty.style.marginLeft='8px';
      addBtn.onclick = ()=>{
        const params = new URLSearchParams({card_name: name, quantity: qty.value});
        fetch('process_add_card.php',{method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body: params.toString()})
          .then(r=>r.text()).then(t=>{ if(t==='incremented'){ alert('Card already existed — increased quantity.'); } else { alert('Added!'); } location.reload(); });
      };
      row.appendChild(addBtn); row.appendChild(qty);
      c.appendChild(row);
    });
  }).catch(()=>{ document.getElementById('scry-results').textContent='Error contacting Scryfall.'; });
}

function quickAdd(e){
  e.preventDefault();
  const name = document.getElementById('qa_name').value.trim();
  const qty = document.getElementById('qa_qty').value;
  if(!name) return;
  const params = new URLSearchParams({card_name: name, quantity: qty});
  fetch('process_add_card.php',{method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body: params.toString()})
    .then(r=>r.text()).then(t=>{ if(t==='incremented'){ alert('Card already existed — increased quantity.'); } else { alert('Added!'); } location.reload(); });
}

document.addEventListener('DOMContentLoaded', function(){
  loadDecks();
  renderInventory();
});
</script>
</body>
</html>
