const $=s=>document.querySelector(s);
let campaigns=[];
const esc=s=>String(s).replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
async function api(url,opt){const r=await fetch(url,opt);const d=await r.json();if(!r.ok)throw new Error(d.error||'Request failed');return d}
function pct(a,b){const t=a+b;return t?Math.round(a/t*100):0}
function card(c){
 const a=Number(c.a_votes),b=Number(c.b_votes),ap=pct(a,b),bp=100-ap, voted=!!c.my_vote;
 return `<article class="poll-card" data-id="${esc(c.id)}">
 <div class="poll-head"><span class="category">${esc(c.category)}</span><span class="live-tag">${voted?'✓ VOTED':'● LIVE'}</span></div>
 <div class="question">${esc(c.question)}</div><h3>${esc(c.title)}</h3><p class="subtitle">${esc(c.subtitle)}</p>
 <div class="faces">
  <div class="face face-a"><img class="person-img" data-wiki="${esc(c.a_wiki)}" alt="${esc(c.a_name)}"><div class="face-bottom"><b>${esc(c.a_name)}</b><strong>${ap}%</strong></div></div>
  <div class="vs-badge">VS</div>
  <div class="face face-b"><img class="person-img" data-wiki="${esc(c.b_wiki)}" alt="${esc(c.b_name)}"><div class="face-bottom"><b>${esc(c.b_name)}</b><strong>${bp}%</strong></div></div>
 </div>
 <div class="vote-numbers"><span>${a.toLocaleString()} Votes</span><span>${b.toLocaleString()} Votes</span></div>
 <div class="split-bar"><span style="width:${ap}%"></span></div>
 <div class="total-row"><span>${(a+b).toLocaleString()} Total Votes</span><span>Live result</span></div>
 <div class="vote-actions"><button class="vote-btn blue" data-side="a" ${voted?'disabled':''}>♡ &nbsp; VOTE FOR ${esc(c.a_name).toUpperCase()}</button><button class="vote-btn red" data-side="b" ${voted?'disabled':''}>♡ &nbsp; VOTE FOR ${esc(c.b_name).toUpperCase()}</button></div>
 <div class="bottom-actions"><span class="op-count">◯ ${Number(c.opinions).toLocaleString()} Opinions</span><div><button class="small-btn result-btn">▥ &nbsp; Results</button><button class="small-btn opinion-btn">Comments</button><button class="small-btn share-btn">Share</button></div></div>
 </article>`
}
async function load(){
 const params=new URLSearchParams(location.search),cat=params.get('category');
 const d=await api('api/campaigns.php'+(cat?'?category='+encodeURIComponent(cat):''));campaigns=d.campaigns;
 $('#sectionTitle').textContent=cat?cat+' polls':'Choose your side';
 $('#pollGrid').innerHTML=campaigns.map(card).join('')||'<div class="empty">No campaigns found.</div>';
 let tv=0,to=0;campaigns.forEach(c=>{tv+=Number(c.a_votes)+Number(c.b_votes);to+=Number(c.opinions)});
 $('#totalVotes').textContent=tv.toLocaleString();$('#totalPolls').textContent=campaigns.length;$('#totalOpinions').textContent=to.toLocaleString();
 hydrate();bind();
}
async function hydrate(){await Promise.all([...document.querySelectorAll('.person-img')].map(async im=>{try{const r=await fetch('https://en.wikipedia.org/api/rest_v1/page/summary/'+encodeURIComponent(im.dataset.wiki));const d=await r.json();if(d.thumbnail?.source)im.src=d.thumbnail.source;else im.parentElement.classList.add('no-image')}catch{im.parentElement.classList.add('no-image')}}))}
function bind(){
 document.querySelectorAll('.poll-card').forEach(el=>{
  const c=campaigns.find(x=>x.id===el.dataset.id);
  el.querySelectorAll('.vote-btn').forEach(btn=>btn.onclick=async()=>{
   try{
    el.querySelectorAll('.vote-btn').forEach(x=>x.disabled=true);
    const d=await api('api/vote.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({campaign_id:c.id,side:btn.dataset.side})});
    if(d.already_voted)toast(d.message||'Already voted.');
    else toast('✓ Your vote was counted on the server.');
    await load();
   }catch(e){el.querySelectorAll('.vote-btn').forEach(x=>x.disabled=false);toast(e.message)}
  });
  el.querySelector('.result-btn').onclick=()=>results(c);
  el.querySelector('.opinion-btn').onclick=()=>opinions(c);
  el.querySelector('.share-btn').onclick=()=>share(c);
 });
}
function results(c){const a=Number(c.a_votes),b=Number(c.b_votes),ap=pct(a,b);open(`<span class="kicker">LIVE RESULT</span><h2>${esc(c.title)}</h2><p class="modal-muted">${(a+b).toLocaleString()} votes stored in the VOTIVA database.</p><div class="result-big"><div><strong>${ap}%</strong><span>${esc(c.a_name)}</span><small>${a.toLocaleString()} votes</small></div><div><strong>${100-ap}%</strong><span>${esc(c.b_name)}</span><small>${b.toLocaleString()} votes</small></div></div><div class="split-bar big"><span style="width:${ap}%"></span></div>`)}
async function opinions(c){
 const d=await api('api/opinions.php?campaign_id='+encodeURIComponent(c.id));
 open(`<span class="kicker">PUBLIC OPINIONS</span><h2>${esc(c.title)}</h2><form id="opForm"><label>Share your opinion</label><textarea id="opText" maxlength="500" required placeholder="Write something respectful..."></textarea><button class="modal-submit">Post opinion</button></form><div class="opinion-list">${d.opinions.length?d.opinions.map(o=>`<div class="opinion"><p>${esc(o.text)}</p><small>${new Date(o.created_at+'Z').toLocaleString()}</small></div>`).join(''):'<p class="modal-muted">No opinions yet. Be the first.</p>'}</div>`);
 $('#opForm').onsubmit=async e=>{e.preventDefault();try{await api('api/opinions.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({campaign_id:c.id,text:$('#opText').value})});close();toast('Opinion posted.');await load()}catch(err){toast(err.message)}}
}
async function share(c){const url=location.origin+location.pathname+'?category='+encodeURIComponent(c.category);try{if(navigator.share)await navigator.share({title:'VOTIVA — '+c.title,text:'Vote on VOTIVA',url});else{await navigator.clipboard.writeText(url);toast('Poll link copied.')}}catch{}}
function open(html){$('#modalContent').innerHTML=html;$('#modal').classList.add('open');$('#modal').setAttribute('aria-hidden','false')}
function close(){$('#modal').classList.remove('open');$('#modal').setAttribute('aria-hidden','true')}
function toast(s){const t=$('#toast');t.textContent=s;t.classList.add('show');clearTimeout(window.__t);window.__t=setTimeout(()=>t.classList.remove('show'),2600)}
$('#closeModal').onclick=close;$('#modal').onclick=e=>{if(e.target.id==='modal')close()};$('#refreshBtn').onclick=load;
$('#howBtn').onclick=()=>open(`<span class="kicker">HOW IT WORKS</span><h2>Real votes. Real totals.</h2><p class="modal-muted">Choose one side and press its vote button. The browser sends the vote to the PHP API. The server inserts it into SQLite using a unique campaign + voter-token rule. A second vote from the same voter token is rejected.</p><p class="modal-muted">The percentages shown in the cards are calculated from the stored vote rows. There are no random result animations or fake counters.</p>`);
$('#menuBtn').onclick=()=>$('#mobileMenu').classList.toggle('open');
load().catch(e=>toast(e.message));
