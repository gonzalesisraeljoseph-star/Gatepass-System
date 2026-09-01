<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<style>
  body{font-family:system-ui,sans-serif;background:#12161C;color:#E7EAF0;margin:0}
  header{padding:16px 24px;border-bottom:1px solid #2A3140;display:flex;gap:16px;align-items:center;flex-wrap:wrap}
  header input,header select{background:#1B212B;color:#E7EAF0;border:1px solid #2A3140;border-radius:6px;padding:6px 10px}
  .layout{display:grid;grid-template-columns:260px 1fr;gap:0}
  @media (max-width:900px){.layout{grid-template-columns:1fr}}
  aside{padding:16px;border-right:1px solid #2A3140;min-height:520px}
  aside h3{font-size:12px;text-transform:uppercase;letter-spacing:.08em;color:#8792A3;margin:0 0 10px}
  .tpl-item{background:#1B212B;border:1px solid #2A3140;border-radius:10px;padding:10px 12px;margin-bottom:8px;cursor:pointer}
  .tpl-item:hover{border-color:#3A4356}
  .tpl-item.active{border-color:#3FBF8F}
  .tpl-item .name{font-size:13px;font-weight:600}
  .tpl-item .meta{font-size:11px;color:#8792A3;margin-top:3px}
  .tpl-item .row{display:flex;justify-content:space-between;align-items:center;margin-top:8px}
  .tpl-item button{background:transparent;border:none;color:#E8654F;font-size:11px;cursor:pointer;padding:0}
  .tpl-item .badge-type{font-family:'JetBrains Mono',monospace;font-size:10px;background:#232B38;padding:2px 6px;border-radius:5px;color:#8792A3}
  .new-btn{width:100%;background:#232B38;color:#E7EAF0;border:1px solid #2A3140;border-radius:8px;padding:8px;cursor:pointer;font-size:12.5px;margin-bottom:14px}
  main{padding:0}
  #canvas{width:calc(100% - 48px);margin:0 24px;height:480px;background:radial-gradient(circle,#232B38 1px,transparent 1px) 0 0/24px 24px,#161B22;position:relative;border-radius:10px;border:1px solid #2A3140;overflow:hidden}
  .toolbar{padding:10px 24px;display:flex;gap:8px;align-items:center;flex-wrap:wrap}
  .toolbar button{background:#232B38;color:#E7EAF0;border:1px solid #2A3140;border-radius:6px;padding:6px 12px;cursor:pointer}
  .toolbar button.primary{background:#3FBF8F;color:#0C1116;border-color:#3FBF8F}
  .toolbar button:disabled{opacity:.4;cursor:not-allowed}
  .node{position:absolute;min-width:130px;padding:8px 12px;border-radius:20px;background:#1B212B;border:2px solid #3A4250;
    cursor:grab;text-align:center;font-size:13px;user-select:none;transform:translate(-50%,-50%)}
  .node.start{border-color:#35B58C}
  .node.approver{border-color:#E8A33D}
  .node.end{border-color:#8B7FE8}
  .node select{width:100%;margin-top:4px;font-size:11px}
  .node .rm{position:absolute;top:-8px;right:-8px;width:18px;height:18px;border-radius:50%;background:#E85D4E;color:#241210;border:none;font-size:11px;font-weight:700;line-height:1;cursor:pointer}
  .node .handle{position:absolute;right:-9px;top:50%;transform:translateY(-50%);width:16px;height:16px;border-radius:50%;
    background:#8792A3;border:2px solid #12161C;cursor:crosshair}
  .node .handle:hover{background:#E7EAF0}
  .node .handle::after{content:'';position:absolute;inset:-8px}
  svg#lines{position:absolute;top:0;left:0;width:100%;height:100%;pointer-events:none}
  svg#lines line.edge{pointer-events:stroke;cursor:pointer}
  svg#lines line.edge:hover{stroke:#E8654F !important}
  .hint{padding:0 24px;color:#8792A3;font-size:13px;margin-top:6px}
  .problems{margin:10px 24px;padding:12px 16px;border-radius:8px;background:#E85D4E1a;border:1px solid #E85D4E55;display:none}
  .problems.show{display:block}
  .problems li{font-size:12.5px;color:#E8654F;margin-bottom:4px}
  .ok-msg{margin:10px 24px;color:#3FBF8F;font-size:13px;display:none}
  .ok-msg.show{display:block}
  .editing-flag{font-size:12px;color:#8792A3}
  .editing-flag b{color:#E7EAF0}
  .editing-flag.dirty b{color:#E8A33D}
</style>

<header>
  <strong>Connect-the-dot Workflow Builder</strong>
  <input id="tplName" placeholder="Template name" value="New Workflow">
  <input id="tplType" placeholder="Request type (e.g. leave)" value="gatepass">
  <span class="editing-flag" id="editingFlag"></span>
</header>

<div class="layout">
  <aside>
    <h3>Saved routes</h3>
    <button class="new-btn" id="newTplBtn">+ Start a new route</button>
    <div id="tplList"></div>
  </aside>

  <main>
    <div class="toolbar">
      <button data-add="start">+ Start dot</button>
      <button data-add="approver">+ Approver dot</button>
      <button data-add="end">+ End dot</button>
      <button id="saveBtn" class="primary" disabled>Save workflow</button>
    </div>
    <div class="hint">Drag a dot by its body to reposition it. To connect two dots, drag from the small
      <b style="color:#E7EAF0">grey handle on the right edge</b> of a Start/Approver dot onto the dot it should lead to.
      Click a line to delete just that connection. A route can't be saved while any dot is disconnected.</div>

    <div id="canvas"><svg id="lines"></svg></div>

    <ul class="problems" id="problems"></ul>
    <div class="ok-msg" id="okMsg">This route is fully connected and safe to save.</div>
  </main>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
const roles = <?= json_encode($roles) ?>;
const users = <?= json_encode($users) ?>;
// server-rendered list, used for the sidebar; kept in sync locally after save/delete
let savedTemplates = <?= json_encode($templates) ?>;

let nodes = []; // {tmp_id, node_type, role_id, user_id, label, pos_x, pos_y} - pos_x/pos_y are the CENTER of the dot
let edges = []; // {from_tmp_id, to_tmp_id}
let seq = 1;
let editingId = null;   // real DB id of the template currently loaded, or null = new/unsaved
let dirty = false;      // true once the canvas differs from what's loaded/saved

function markDirty(){
  dirty = true;
  const flag = document.getElementById('editingFlag');
  flag.classList.toggle('dirty', dirty);
  if(editingId) flag.innerHTML = 'Editing <b>#'+editingId+'</b> — unsaved changes';
}
function clearDirty(){
  dirty = false;
  const flag = document.getElementById('editingFlag');
  flag.classList.remove('dirty');
  flag.innerHTML = editingId ? 'Editing <b>#'+editingId+'</b>' : '';
}
function confirmDiscardIfDirty(){
  if(!dirty) return true;
  return confirm('You have unsaved changes on this route. Discard them and continue?');
}
window.addEventListener('beforeunload', e=>{ if(dirty){ e.preventDefault(); e.returnValue=''; } });

function roleOptions(sel){
  return '<option value="">- pick a role -</option>' + roles.map(r=>`<option value="${r.id}" ${sel==r.id?'selected':''}>${r.name}</option>`).join('');
}
function userOptions(sel, roleFilter){
  const list = roleFilter ? users.filter(u=>u.role_id==roleFilter) : users;
  return '<option value="">- or a specific person -</option>' + list.map(u=>`<option value="${u.id}" ${sel==u.id?'selected':''}>${u.name}</option>`).join('');
}
document.getElementById('tplName').addEventListener('input', markDirty);
document.getElementById('tplType').addEventListener('input', markDirty);

/* ---------------- sidebar: saved routes list ---------------- */
function renderTplList(){
  const wrap = document.getElementById('tplList');
  if(!savedTemplates.length){ wrap.innerHTML = '<div class="hint" style="padding:0">No routes saved yet.</div>'; return; }
  wrap.innerHTML = savedTemplates.map(t => `
    <div class="tpl-item ${editingId==t.id?'active':''}" data-id="${t.id}">
      <div class="name">${t.name}</div>
      <div class="meta"><span class="badge-type">${t.request_type}</span> · applies to ${t.applies_to} · ${t.node_count} dots</div>
      <div class="row">
        <span class="hint" style="padding:0">click to edit</span>
        <button data-del="${t.id}">Delete</button>
      </div>
    </div>
  `).join('');
  wrap.querySelectorAll('.tpl-item').forEach(el=>{
    el.addEventListener('click', ev=>{
      if(ev.target.closest('[data-del]')) return;
      if(!confirmDiscardIfDirty()) return;
      loadTemplate(el.dataset.id);
    });
  });
  wrap.querySelectorAll('[data-del]').forEach(btn=>{
    btn.addEventListener('click', ev=>{
      ev.stopPropagation();
      if(!confirm('Delete this route? This cannot be undone.')) return;
      fetch('<?= site_url('approving-sequence/delete') ?>/'+btn.dataset.del, {method:'POST'})
        .then(r=>r.json()).then(()=>{
          savedTemplates = savedTemplates.filter(t=>t.id!=btn.dataset.del);
          if(editingId==btn.dataset.del) newTemplate(true);
          renderTplList();
        });
    });
  });
}

/* ---------------- load an existing route from the server into the canvas ---------------- */
function loadTemplate(id){
  fetch('<?= site_url('approving-sequence') ?>/'+id).then(r=>r.json()).then(res=>{
    editingId = res.template.id;
    document.getElementById('tplName').value = res.template.name;
    document.getElementById('tplType').value = res.template.request_type;

    // real DB ids become tmp_ids directly (prefixed to avoid clashing with freshly-added dots)
    nodes = res.nodes.map(n => ({
      tmp_id: 'db'+n.id, node_type: n.node_type,
      role_id: n.role_id || '', user_id: n.user_id || '',
      label: n.label, pos_x: Number(n.pos_x), pos_y: Number(n.pos_y),
    }));
    edges = res.edges.map(e => ({ from_tmp_id: 'db'+e.from_node_id, to_tmp_id: 'db'+e.to_node_id }));

    render();
    clearDirty();
    renderTplList();
  });
}

function newTemplate(skipConfirm){
  if(!skipConfirm && !confirmDiscardIfDirty()) return;
  editingId = null;
  nodes = []; edges = [];
  document.getElementById('tplName').value = 'New Workflow';
  document.getElementById('tplType').value = 'leave';
  render();
  clearDirty();
  renderTplList();
}
document.getElementById('newTplBtn').addEventListener('click', ()=>newTemplate(false));

document.querySelectorAll('[data-add]').forEach(btn=>{
  btn.addEventListener('click', ()=>{
    const type = btn.dataset.add;
    const n = {tmp_id:'n'+(seq++), node_type:type, role_id:'', user_id:'',
      label:type==='start'?'Requester':(type==='end'?'Approved':'Approver'),
      pos_x: 100 + (nodes.length%5)*140, pos_y: 90 + Math.floor(nodes.length/5)*140};
    nodes.push(n);
    markDirty();
    render();
  });
});

/* ---------------- canvas rendering ---------------- */
function render(){
  const canvas = document.getElementById('canvas');
  canvas.querySelectorAll('.node').forEach(e=>e.remove());

  nodes.forEach(n=>{
    const div = document.createElement('div');
    div.className = 'node ' + n.node_type;
    div.style.left = n.pos_x+'px';
    div.style.top = n.pos_y+'px';
    div.dataset.id = n.tmp_id;

    let inner = `<div>${n.label}</div>`;
    if(n.node_type==='approver' || n.node_type==='start'){
      inner += `<select data-role>${roleOptions(n.role_id)}</select>`;
      inner += `<select data-user>${userOptions(n.user_id, n.role_id)}</select>`;
      if(n.node_type==='start') inner += `<div style="font-size:10px;color:#8792A3;margin-top:3px">who this route applies to</div>`;
    }
    inner += `<button class="rm" title="remove">×</button>`;
    if(n.node_type!=='end'){
      inner += `<div class="handle" title="drag to connect to another dot"></div>`;
    }
    div.innerHTML = inner;

    div.querySelector('.rm').addEventListener('click', ev=>{
      ev.stopPropagation();
      nodes = nodes.filter(x=>x.tmp_id!==n.tmp_id);
      edges = edges.filter(e=>e.from_tmp_id!==n.tmp_id && e.to_tmp_id!==n.tmp_id);
      markDirty(); render();
    });
    const roleSel = div.querySelector('[data-role]');
    const userSel = div.querySelector('[data-user]');
    if(roleSel){
      roleSel.addEventListener('mousedown', ev=>ev.stopPropagation());
      roleSel.addEventListener('change', ev=>{ n.role_id = ev.target.value; n.user_id=''; markDirty(); render(); });
    }
    if(userSel){
      userSel.addEventListener('mousedown', ev=>ev.stopPropagation());
      userSel.addEventListener('change', ev=>{ n.user_id = ev.target.value; markDirty(); render(); });
    }

    div.addEventListener('mousedown', startDrag(n));

    const handle = div.querySelector('.handle');
    if(handle){
      handle.addEventListener('mousedown', startConnect(n));
    }

    canvas.appendChild(div);
  });

  drawLines();
  validateLive();
}

/* dragging a dot's body repositions it */
function startDrag(n){
  return function(ev){
    if(ev.target.closest('select') || ev.target.closest('button') || ev.target.closest('.handle')) return;
    ev.preventDefault();
    const startX = ev.clientX, startY = ev.clientY;
    const origX = n.pos_x, origY = n.pos_y;
    let moved = false;
    function move(e){
      moved = true;
      n.pos_x = origX + (e.clientX-startX);
      n.pos_y = origY + (e.clientY-startY);
      render();
    }
    function up(){
      document.removeEventListener('mousemove',move);
      document.removeEventListener('mouseup',up);
      if(moved) markDirty();
    }
    document.addEventListener('mousemove',move);
    document.addEventListener('mouseup',up);
  };
}

/* dragging FROM a dot's connector handle draws a live line to the cursor,
   and drops onto whichever dot is under the mouse on release. */
function startConnect(n){
  return function(ev){
    ev.stopPropagation();
    ev.preventDefault();
    const canvas = document.getElementById('canvas');
    const svg = document.getElementById('lines');
    const temp = document.createElementNS('http://www.w3.org/2000/svg','line');
    temp.setAttribute('stroke', '#E8A33D');
    temp.setAttribute('stroke-width', '3');
    temp.setAttribute('stroke-dasharray', '6,4');
    svg.appendChild(temp);

    function pos(e){
      const rect = canvas.getBoundingClientRect();
      return { x: e.clientX - rect.left, y: e.clientY - rect.top };
    }
    function move(e){
      const p = pos(e);
      temp.setAttribute('x1', n.pos_x); temp.setAttribute('y1', n.pos_y);
      temp.setAttribute('x2', p.x); temp.setAttribute('y2', p.y);
    }
    function up(e){
      document.removeEventListener('mousemove', move);
      document.removeEventListener('mouseup', up);
      temp.remove();
      const dropEl = document.elementFromPoint(e.clientX, e.clientY);
      const targetDiv = dropEl && dropEl.closest('.node');
      if(targetDiv && targetDiv.dataset.id !== n.tmp_id){
        const already = edges.some(ed=>ed.from_tmp_id===n.tmp_id && ed.to_tmp_id===targetDiv.dataset.id);
        if(!already){
          edges.push({from_tmp_id:n.tmp_id, to_tmp_id:targetDiv.dataset.id});
          markDirty();
        }
      }
      render();
    }
    document.addEventListener('mousemove', move);
    document.addEventListener('mouseup', up);
    move(ev);
  };
}

function drawLines(){
  const svg = document.getElementById('lines');
  svg.innerHTML = '';
  edges.forEach((e, idx)=>{
    const a = nodes.find(n=>n.tmp_id===e.from_tmp_id);
    const b = nodes.find(n=>n.tmp_id===e.to_tmp_id);
    if(!a||!b) return;
    const line = document.createElementNS('http://www.w3.org/2000/svg','line');
    line.setAttribute('class','edge');
    line.setAttribute('x1', a.pos_x); line.setAttribute('y1', a.pos_y);
    line.setAttribute('x2', b.pos_x); line.setAttribute('y2', b.pos_y);
    line.setAttribute('stroke', '#5B6478'); line.setAttribute('stroke-width','4'); line.setAttribute('stroke-linecap','round');
    line.addEventListener('click', ()=>{
      edges = edges.filter((_, i)=>i!==idx);
      markDirty(); render();
    });
    svg.appendChild(line);

    // small arrowhead at the midpoint so direction is visible
    const mx = (a.pos_x+b.pos_x)/2, my=(a.pos_y+b.pos_y)/2;
    const ang = Math.atan2(b.pos_y-a.pos_y, b.pos_x-a.pos_x);
    const ah = document.createElementNS('http://www.w3.org/2000/svg','circle');
    ah.setAttribute('cx', mx); ah.setAttribute('cy', my); ah.setAttribute('r','4');
    ah.setAttribute('fill', '#8792A3'); ah.setAttribute('pointer-events','none');
    svg.appendChild(ah);
  });
}

// ---- live connectivity validation: same rules as WorkflowBuilder::findGraphProblems() ----
let validateTimer;
function validateLive(){
  clearTimeout(validateTimer);
  validateTimer = setTimeout(doValidate, 200);
}
function doValidate(){
  fetch('<?= site_url('approving-sequence/validate') ?>', {
    method:'POST', headers:{'Content-Type':'application/json'},
    body: JSON.stringify({nodes, edges})
  }).then(r=>r.json()).then(res=>{
    const list = document.getElementById('problems');
    const ok = document.getElementById('okMsg');
    const saveBtn = document.getElementById('saveBtn');
    if(res.ok){
      list.classList.remove('show'); list.innerHTML='';
      ok.classList.add('show');
      saveBtn.disabled = false;
    } else {
      ok.classList.remove('show');
      list.innerHTML = res.problems.map(p=>`<li>${p}</li>`).join('');
      list.classList.add('show');
      saveBtn.disabled = true; // cannot select "Save" while dots are unconnected
    }
  });
}

function save(){
  const body = {
    template: {
      id: editingId,
      name: document.getElementById('tplName').value,
      request_type: document.getElementById('tplType').value,
    },
    nodes: nodes,
    edges: edges,
    assignments: nodes.filter(n=>n.node_type==='start').map(n=>({
      applies_to_type: n.user_id ? 'user' : 'role',
      applies_to_id: n.user_id || n.role_id
    }))
  };
  fetch('<?= site_url('approving-sequence/save') ?>', {
    method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify(body)
  }).then(async r=>{
    const res = await r.json();
    if(!r.ok){
      const list = document.getElementById('problems');
      list.innerHTML = res.problems.map(p=>`<li>${p}</li>`).join('');
      list.classList.add('show');
      return;
    }
    document.getElementById('okMsg').textContent = 'Saved as template #' + res.template_id;
    document.getElementById('okMsg').classList.add('show');
    // reload straight into the saved version so the sidebar, canvas ids and
    // "editing #n" flag all reflect exactly what's now in the database.
    loadTemplate(res.template_id);
    refreshTplListFromServer(res.template_id);
  });
}
function refreshTplListFromServer(id){
  fetch('<?= site_url('approving-sequence') ?>/' + id).then(r=>r.json()).then(res=>{
    const idx = savedTemplates.findIndex(t=>t.id==id);
    const startNode = res.nodes.find(n=>n.node_type==='start');
    const appliesTo = startNode
      ? (startNode.user_id ? (users.find(u=>u.id==startNode.user_id)||{}).name+' only' : (roles.find(r=>r.id==startNode.role_id)||{}).name)
      : '—';
    const entry = { id: res.template.id, name: res.template.name, request_type: res.template.request_type, node_count: res.nodes.length, applies_to: appliesTo || '—' };
    if(idx>=0) savedTemplates[idx] = entry; else savedTemplates.push(entry);
    renderTplList();
  });
}
document.getElementById('saveBtn').addEventListener('click', save);

renderTplList();
render();
</script>
<?= $this->endSection() ?>