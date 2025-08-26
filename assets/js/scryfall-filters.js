
(function(){
  function h(tag, attrs={}, ...children){
    const el = document.createElement(tag);
    Object.entries(attrs).forEach(([k,v])=>{
      if(k === 'class') el.className = v;
      else if(k === 'for') el.htmlFor = v;
      else el.setAttribute(k, v);
    });
    children.forEach(c=>{
      if(c==null) return;
      if(typeof c === 'string') el.appendChild(document.createTextNode(c));
      else el.appendChild(c);
    });
    return el;
  }
  function buildPanel(){
    const wrap = h('div', { id:'scryfall-filter-panel' });
    const row1 = h('div', { style:'display:flex;flex-wrap:wrap;gap:8px;align-items:center' });

    const typeSel = h('select', { id:'sfType' },
      h('option', { value:'' }, 'Any Type'),
      ...['creature','instant','sorcery','artifact','enchantment','planeswalker','land']
        .map(o=>h('option', { value:o }, o))
    );
    const colorsSel = h('select', { id:'sfColors', multiple:'' },
      h('option', { value:'w' }, 'White'),
      h('option', { value:'u' }, 'Blue'),
      h('option', { value:'b' }, 'Black'),
      h('option', { value:'r' }, 'Red'),
      h('option', { value:'g' }, 'Green'),
      h('option', { value:'c' }, 'Colorless')
    );
    const raritySel = h('select', { id:'sfRarity' },
      h('option', { value:'' }, 'Any Rarity'),
      ...['common','uncommon','rare','mythic'].map(o=>h('option', { value:o }, o))
    );
    const yearInput = h('input', { id:'sfYear', type:'number', min:'1993', max:'2099', placeholder:'Year' });
    const freeText  = h('input', { id:'sfFree', type:'text', placeholder:'Free-text (name, set, etc.)', style:'flex:1 1 200px' });
    const btn = h('button', { id:'sfSearchBtn', type:'button' }, 'Search');

    row1.append(
      h('label', {}, 'Type'), typeSel,
      h('label', {}, 'Mana/Colors'), colorsSel,
      h('label', {}, 'Rarity'), raritySel,
      h('label', {}, 'Year'), yearInput,
      freeText, btn
    );
    wrap.append(row1);
    return wrap;
  }
  function pick(sel){ return Array.from(sel.selectedOptions).map(o=>o.value); }
  function buildQuery(){
    const type = document.getElementById('sfType').value.trim();
    const rarity = document.getElementById('sfRarity').value.trim();
    const year = document.getElementById('sfYear').value.trim();
    const free = document.getElementById('sfFree').value.trim();
    const colors = pick(document.getElementById('sfColors'));
    let q = [];
    if (free) q.push(encodeURIComponent(free));
    if (type) q.push('t%3A' + encodeURIComponent(type));
    if (rarity) q.push('r%3A' + encodeURIComponent(rarity));
    if (colors.length){
      if (colors.includes('c') && colors.length === 1){
        q.push('is%3Acolorless');
      } else {
        q.push('c%3E%3D' + encodeURIComponent(colors.filter(c=>c!=='c').join('')));
      }
    }
    if (year){ q.push('year%3A' + encodeURIComponent(year)); }
    return q.join('+');
  }
  function ensureResultsContainer(){
    let box = document.getElementById('scryfall-results');
    if(!box){
      box = h('div', { id:'scryfall-results' });
      document.body.appendChild(box);
    }
    return box;
  }
  function render(cards){
    const box = ensureResultsContainer();
    box.innerHTML = '';
    cards.forEach(card=>{
      const img = (card.image_uris && card.image_uris.small) ? h('img', { src:card.image_uris.small, alt:'' }) : h('div');
      const qty = h('input', { type:'number', min:'1', value:'1', style:'width:60px;margin:0 8px' });
      const add = h('button', { type:'button' }, 'Add');
      const row = document.createElement('div');
      row.className = 'result-row';
      row.append(img, 
        (()=>{ const m=document.createElement('div'); m.style.flex='1'; 
                const n=document.createElement('div'); n.style.fontWeight='600'; n.textContent=card.name||'Unknown';
                const s=document.createElement('div'); s.style.fontSize='12px'; s.style.opacity='0.8'; s.textContent=(card.set_name||'').toUpperCase();
                m.append(n,s); return m; })(),
        qty, add);
      add.addEventListener('click', function(){
        const q = parseInt(qty.value,10) || 1;
        fetch('./process_add_card.php', {
          method:'POST',
          headers:{'Content-Type':'application/x-www-form-urlencoded'},
          body: new URLSearchParams({ card_name: card.name, quantity: q }).toString()
        }).then(r=>{ if(r.ok){ add.textContent='Added'; add.disabled=true; } else { alert('Add failed'); }})
        .catch(()=>alert('Network error'));
      });
      box.appendChild(row);
    });
  }
  function runSearch(){
    const q = buildQuery();
    if(!q){ alert('Enter a search or pick filters.'); return; }
    const url = `https://api.scryfall.com/cards/search?q=${q}`;
    const box = ensureResultsContainer();
    box.textContent = 'Searching...';
    fetch(url).then(r=>r.json()).then(data=>{
      if(!data || !Array.isArray(data.data)){ box.textContent='No results'; return; }
      render(data.data);
    }).catch(()=>{ box.textContent = 'Error querying Scryfall'; });
  }
  function init(){
    const host = document.getElementById('scryfall-filters') || document.querySelector('main, .container, body');
    host.prepend(buildPanel());
    document.getElementById('sfSearchBtn').addEventListener('click', runSearch);
  }
  if(document.readyState==='loading'){ document.addEventListener('DOMContentLoaded', init); }
  else { init(); }
})();