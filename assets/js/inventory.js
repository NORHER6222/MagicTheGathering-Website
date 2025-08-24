(function(){
  const EP = window.MTG_ENDPOINTS || {};
  const $ = (sel, root=document)=> root.querySelector(sel);

  function jsonFetch(url, opts={}){
    return fetch(url, Object.assign({headers:{'Content-Type':'application/json'}}, opts))
      .then(r=>r.json());
  }
  function formFetch(url, data){
    return fetch(url, {
      method:'POST',
      headers:{'Content-Type':'application/x-www-form-urlencoded'},
      body: new URLSearchParams(data).toString()
    }).then(r=>r.json().catch(()=>({ok:r.ok})));
  }
  const debounce=(fn,ms)=>{let t;return (...a)=>{clearTimeout(t);t=setTimeout(()=>fn(...a),ms);}};

  let decks = [];
  let onhand = new Map(); // inventory_id -> total

  function loadDecks(){
    return fetch(EP.getDecks).then(r=>r.json()).then(data=>{
      decks = Array.isArray(data) ? data : (data.decks || []);
      const list = $("#deckList");
      if (list){
        list.innerHTML = "";
        decks.forEach(d=>{
          const li = document.createElement('li');
          const a = document.createElement('a');
          a.href = `${EP.viewDeck}?id=${encodeURIComponent(d.id)}`;
          a.textContent = `${d.name} (${d.count||0})`;
          li.appendChild(a);
          list.appendChild(li);
        });
      }
    });
  }

  function loadOnHandTotals(){
    return fetch('get_onhand_totals.php').then(r=>r.json()).then(rows=>{
      onhand.clear();
      (rows || []).forEach(r=>{ onhand.set(r.inventory_id, r.total); });
    });
  }

  function loadInventory(){
    return fetch(EP.getInventory).then(r=>r.json()).then(data=>{
      const rows = Array.isArray(data) ? data : (data.rows || data.data || []);
      renderInventory(rows);
    });
  }

  // Simple in-memory image cache for Scryfall lookups
  const imgCache = new Map();
  function resolveImage(cardName){
    if(!cardName) return Promise.resolve(null);
    if(imgCache.has(cardName)) return Promise.resolve(imgCache.get(cardName));
    const e = encodeURIComponent(cardName);
    const urlExact = `https://api.scryfall.com/cards/named?exact=${e}`;
    return fetch(urlExact).then(r=>r.json()).then(j=>{
      let src = (j.image_uris && (j.image_uris.small || j.image_uris.normal)) || null;
      if(!src && j.card_faces && j.card_faces.length){
        const f = j.card_faces[0];
        src = f.image_uris && (f.image_uris.small || f.image_uris.normal);
      }
      imgCache.set(cardName, src || null);
      return src || null;
    }).catch(()=>null);
  }

  function makeModal(){
    if($('#assignModal')) return $('#assignModal');
    const overlay = document.createElement('div');
    overlay.id='assignModal';
    overlay.style.cssText='position:fixed;inset:0;background:rgba(0,0,0,.6);display:none;align-items:center;justify-content:center;z-index:9999;';
    const panel = document.createElement('div');
    panel.style.cssText='background:#fff;color:#000;padding:16px;min-width:320px;max-width:90%;border:1px solid #444;';
    panel.innerHTML = '<h3 id="am-title" style="margin-top:0">Assign to deck</h3>\
      <div style="display:flex;gap:8px;align-items:center;margin:8px 0">\
        <img id="am-img" alt="" style="width:64px;height:auto;display:none;border:1px solid #ccc">\
        <div>\
          <div id="am-name" style="font-weight:600"></div>\
          <div id="am-onhand" style="font-size:12px;opacity:.8"></div>\
        </div>\
      </div>\
      <div style="margin:6px 0">\
        <label>Deck:&nbsp;<select id="am-deck"></select></label>\
      </div>\
      <div style="margin:6px 0">\
        <label>Quantity:&nbsp;<input id="am-qty" type="number" min="1" value="1" style="width:80px"></label>\
      </div>\
      <div style="display:flex;gap:8px;justify-content:flex-end;margin-top:12px">\
        <button id="am-cancel">Cancel</button>\
        <button id="am-confirm">Assign</button>\
      </div>';
    overlay.appendChild(panel);
    document.body.appendChild(overlay);
    overlay.addEventListener('click', (e)=>{ if(e.target===overlay) overlay.style.display='none'; });
    $('#am-cancel', panel)?.addEventListener('click', ()=> overlay.style.display='none');
    return overlay;
  }

  function openAssignModal(rowData, currentQtyInput){
    const modal = makeModal();
    const nameEl = modal.querySelector('#am-name');
    const imgEl = modal.querySelector('#am-img');
    const onhandEl = modal.querySelector('#am-onhand');
    const deckSel = modal.querySelector('#am-deck');
    const qtyEl = modal.querySelector('#am-qty');
    const confirmBtn = modal.querySelector('#am-confirm');

    nameEl.textContent = rowData.name;
    const total = onhand.get(rowData.id);
    onhandEl.textContent = (typeof total==='number') ? `Total on-hand: ${total}` : '';
    qtyEl.value = 1;

    // decks
    deckSel.innerHTML = '';
    const opt0 = document.createElement('option'); opt0.value=''; opt0.textContent='Select deck'; deckSel.appendChild(opt0);
    decks.forEach(d=>{ const o=document.createElement('option'); o.value=d.id; o.textContent=d.name; deckSel.appendChild(o); });

    // image
    imgEl.style.display='none';
    resolveImage(rowData.name).then(src=>{
      if(src){ imgEl.src=src; imgEl.style.display='block'; }
    });

    confirmBtn.onclick = function(){
      const deckId = deckSel.value;
      const mvQty = parseInt(qtyEl.value,10)||1;
      if(!deckId){ alert('Please choose a deck'); return; }
      jsonFetch(EP.transferToDeck, {method:'POST', body: JSON.stringify({inventory_id:rowData.id, deck_id:deckId, quantity:mvQty})})
        .then(res=>{
          if(res && res.ok){
            if(typeof res.new_qty==='number' && currentQtyInput){ currentQtyInput.value = res.new_qty; }
            loadDecks();
            loadOnHandTotals().then(()=>{
              // update on-hand display in the row
              const span = document.querySelector(`[data-onhand="${rowData.id}"]`);
              if(span && onhand.has(rowData.id)) span.textContent = ` (Total: ${onhand.get(rowData.id)})`;
            });
            modal.style.display='none';
          } else {
            alert(res && res.error ? res.error : 'Move failed');
          }
        })
        .catch(()=>alert('Network error'));
    };

    modal.style.display='flex';
  }

  function renderInventory(rows){
    const tb = $("#inv-tbody");
    tb.innerHTML = "";
    rows.forEach(row=>{
      const id = row.id ?? row.inventory_id ?? row.inventoryId;
      const name = row.card_name ?? row.name ?? row.card;
      const qty = row.quantity ?? row.qty ?? 0;

      const tr = document.createElement('tr');

      // Card cell with image + name + total
      const cName = document.createElement('td');
      const wrap = document.createElement('div');
      wrap.style.display='flex'; wrap.style.alignItems='center'; wrap.style.gap='8px';
      const img = document.createElement('img'); img.alt=''; img.style.width='40px'; img.style.height='auto'; img.style.border='1px solid #333'; img.style.display='none';
      resolveImage(name).then(src=>{ if(src){ img.src=src; img.style.display='block'; }});
      const title = document.createElement('span'); title.textContent = name;
      const totalSpan = document.createElement('span'); totalSpan.setAttribute('data-onhand', String(id));
      if(onhand.has(id)) totalSpan.textContent = ` (Total: ${onhand.get(id)})`;
      wrap.appendChild(img); wrap.appendChild(title); wrap.appendChild(totalSpan);
      cName.appendChild(wrap);
      tr.appendChild(cName);

      // Quantity edit
      const cQty = document.createElement('td');
      const inp = document.createElement('input');
      inp.type = 'number'; inp.min='0'; inp.value = qty; inp.style.width='80px';
      inp.addEventListener('input', debounce(function(){
        let val = parseInt(inp.value,10);
        if(isNaN(val)||val<0) val = 0;
        jsonFetch(EP.updateQty, {method:'POST', body: JSON.stringify({inventory_id:id, quantity:val})})
          .then(res=>{
            if(!res || res.ok!==true){ console.warn('qty update failed', res); }
            else {
              // refresh on-hand total
              loadOnHandTotals().then(()=>{
                const span = document.querySelector(`[data-onhand="${id}"]`);
                if(span && onhand.has(id)) span.textContent = ` (Total: ${onhand.get(id)})`;
              });
            }
          });
      }, 350));
      cQty.appendChild(inp);
      tr.appendChild(cQty);

      // Assign button (opens modal)
      const cAssign = document.createElement('td');
      const btn = document.createElement('button'); btn.textContent='Assign to deck';
      btn.addEventListener('click', ()=> openAssignModal({id, name}, inp));
      cAssign.appendChild(btn);
      tr.appendChild(cAssign);

      // Delete
      const cAct = document.createElement('td');
      const del = document.createElement('button'); del.textContent='Delete';
      del.addEventListener('click', function(){
        if(!confirm('Delete this card from your inventory?')) return;
        formFetch(EP.removeCard, {card_id:id}).then(res=>{
          if(res && res.ok !== false){ tr.remove(); }
          else { alert('Delete failed'); }
        }).catch(()=>alert('Network error'));
      });
      cAct.appendChild(del);
      tr.appendChild(cAct);

      tb.appendChild(tr);
    });
  }

  function init(){
    $("#createDeckBtn")?.addEventListener('click', function(){
      const name = ($("#newDeckName").value || "").trim();
      if(!name){ alert('Enter a deck name'); return; }
      jsonFetch(EP.createDeck, {method:'POST', body: JSON.stringify({name})}).then(res=>{
        if(res && res.ok){ $("#newDeckName").value=''; loadDecks(); }
        else { alert(res && res.error ? res.error : 'Create deck failed'); }
      });
    });
    // Load all data, then render
    loadDecks()
      .then(loadOnHandTotals)
      .then(loadInventory);
  }

  if(document.readyState==='loading') document.addEventListener('DOMContentLoaded', init); else init();
})();