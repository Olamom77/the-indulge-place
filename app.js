const $=s=>document.querySelector(s),C=window.SITE;let items=[],cat='All',mt='',cart=JSON.parse(localStorage.getItem('cart')||'{}');
const money=n=>{n=Number(n);return C.currency+n.toLocaleString('en-US',{minimumFractionDigits:Number.isInteger(n)?0:2,maximumFractionDigits:2})},esc=s=>String(s).replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
const card=i=>`<article class="card reveal"><div class="ph"><div class="pic" ${i.image?`style="background-image:url('${esc(i.image)}'),var(--g)"`:''}></div><span class="pr">${money(i.price)}</span><button class="add" data-add="${i.id}">Add +</button></div><h3>${esc(i.name)}</h3><p>${esc(i.description||'')}</p></article>`;
function reveal(){const o=new IntersectionObserver(e=>e.forEach(x=>x.isIntersecting&&(x.target.classList.add('in'),o.unobserve(x.target))),{threshold:.1});document.querySelectorAll('.reveal:not(.in)').forEach(el=>o.observe(el))}
const row=i=>`<div class="mi reveal">${i.image?`<span class="th" style="background-image:url('${esc(i.image)}')"></span>`:''}<div class="mt"><h4>${esc(i.name)}</h4>${i.description?`<p>${esc(i.description)}</p>`:''}</div><b>${money(i.price)}</b><button class="add2" data-add="${i.id}" aria-label="Add ${esc(i.name)} to cart">+</button></div>`;
function render(){const q=$('#q').value.toLowerCase(),types=[...new Set(items.map(i=>i.menu_type))];if(!types.includes(mt))mt=types[0];
$('#mtabs').innerHTML=types.length>1?types.map(t=>`<button class="${t===mt?'on':''}">${esc(t)}</button>`).join(''):'';
const pool=items.filter(i=>i.menu_type===mt),cs=['All',...new Set(pool.map(i=>i.category))];if(!cs.includes(cat))cat='All';
$('#cats').innerHTML=cs.map(c=>`<button class="${c===cat?'on':''}">${esc(c)}</button>`).join('');
const g=new Map();pool.filter(i=>(cat==='All'||i.category===cat)&&(i.name+' '+(i.description||'')).toLowerCase().includes(q)).forEach(i=>{if(!g.has(i.category))g.set(i.category,[]);g.get(i.category).push(i)});
let h='';g.forEach((l,c)=>{h+=`<h3 class="ch">${esc(c)}</h3>`+l.map(row).join('')});$('#items').innerHTML=h||'<p>No dishes found.</p>';
const f=items.filter(i=>+i.featured).slice(0,3);$('#featured').innerHTML=(f.length?f:items.slice(0,3)).map(card).join('');reveal()}
function save(){localStorage.setItem('cart',JSON.stringify(cart));drawer()}
function drawer(){const l=Object.entries(cart).map(([id,q])=>[items.find(i=>i.id==id),q]).filter(x=>x[0]);let t=0;
$('#lines').innerHTML=l.map(([i,q])=>{t+=i.price*q;return `<div class="ln"><span>${esc(i.name)}<br><small>${money(i.price)}</small></span><span><button data-q="${i.id}:-1">-</button> ${q} <button data-q="${i.id}:1">+</button> <button data-q="${i.id}:x">&times;</button></span></div>`}).join('')||'<p>Your cart is empty.</p>';
$('#tot').textContent=money(t);$('#cc').textContent=l.reduce((s,x)=>s+x[1],0)}
document.body.insertAdjacentHTML('beforeend','<div id="toast" role="status"></div>');let tt;const toast=s=>{const e=$('#toast');e.textContent=s;e.classList.add('on');$('#cartBtn').classList.remove('bump');void $('#cartBtn').offsetWidth;$('#cartBtn').classList.add('bump');clearTimeout(tt);tt=setTimeout(()=>e.classList.remove('on'),1800)};
const toggle=o=>{$('#drawer').classList.toggle('open',o);$('#shade').classList.toggle('on',o)};
document.addEventListener('click',e=>{const t=e.target;
if(t.dataset.add){cart[t.dataset.add]=(cart[t.dataset.add]||0)+1;save();const it=items.find(i=>i.id==t.dataset.add);toast((it?it.name:'Item')+' added to cart')}
if(t.dataset.q){const[id,a]=t.dataset.q.split(':');cart[id]=a==='x'?0:cart[id]+ +a;if(cart[id]<=0)delete cart[id];save()}
if(t.closest('#cats')&&t.tagName==='BUTTON'){cat=t.textContent;render()}
if(t.closest('#mtabs')&&t.tagName==='BUTTON'){mt=t.textContent;cat='All';render()}
const gt=t.closest('.gal [data-src]');if(gt){const im=new Image();im.onload=()=>{$('#lb').style.backgroundImage=`url(${gt.dataset.src})`;$('#lb').classList.add('on')};im.src=gt.dataset.src}
if(t.id==='lb')t.classList.remove('on');if(t.closest('#links'))menu(false)});
$('#q').oninput=render;$('#cartBtn').onclick=()=>toggle(true);$('#close').onclick=()=>toggle(false);const menu=o=>{$('#links').classList.toggle('open',o);$('#burger').classList.toggle('on',o);document.body.style.overflow=o?'hidden':''};$('#burger').onclick=()=>menu(!$('#links').classList.contains('open'));
addEventListener('scroll',()=>$('#nav').classList.toggle('solid',scrollY>60),{passive:true});
document.addEventListener('keydown',e=>{if(e.key==='Escape'){menu(false);$('#lb').classList.remove('on');toggle(false)}});
document.querySelectorAll('[name=type]').forEach(r=>r.onchange=()=>$('#ad').hidden=r.value!=='delivery'||!r.checked);
async function post(a,d){const r=await fetch('api.php?a='+a,{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify(d)});const j=await r.json();if(!r.ok)throw Error(j.error||'Error');return j}
$('#of').onsubmit=async e=>{e.preventDefault();const f=e.target,m=$('#om');m.className='msg';m.textContent='';
const d=Object.fromEntries(new FormData(f));d.items=Object.entries(cart).map(([id,qty])=>({id:+id,qty}));
if(!d.items.length)return m.textContent='Your cart is empty.';if(!d.name.trim())return m.textContent='Please enter your name.';
if(!/^[+\d\s()-]{7,20}$/.test(d.phone))return m.textContent='Please enter a valid phone number.';if(d.type==='delivery'&&!d.address.trim())return m.textContent='Please enter a delivery address.';
const wa=(C.whatsapp||'').replace(/\D/g,'');if(!wa)return m.textContent='WhatsApp number not configured yet (set it in config.php).';
const w=window.open('','_blank'),b=f.querySelector('.btn');b.disabled=true;
try{const o=await post('order',d);const txt=`New order #${o.id} - The Indulge Place\n`+o.items.map(i=>`${i.qty} x ${i.name} (${money(i.price*i.qty)})`).join('\n')+`\nTotal: ${money(o.total)}\n${d.type==='delivery'?'Delivery: '+d.address:'Pickup'}\nName: ${d.name}\nPhone: ${d.phone}`+(d.notes?`\nNotes: ${d.notes}`:'');
const u=`https://wa.me/${wa}?text=${encodeURIComponent(txt)}`;w?w.location.href=u:location.href=u;cart={};save();f.reset();$('#ad').hidden=true;m.className='msg ok';m.textContent='Order saved. Complete it in WhatsApp.'}catch(x){w&&w.close();m.textContent=x.message}finally{b.disabled=false}};
$('#cf').onsubmit=async e=>{e.preventDefault();const f=e.target,m=$('#cm'),d=Object.fromEntries(new FormData(f));m.className='msg';
if(!d.name.trim()||!/^\S+@\S+\.\S+$/.test(d.email)||!d.message.trim())return m.textContent='Please fill in all fields correctly.';
try{await post('contact',d);f.reset();m.className='msg ok';m.textContent='Thank you - message sent.'}catch(x){m.textContent=x.message}};
$('#shade').onclick=()=>toggle(false);
fetch('api.php?a=menu').then(r=>r.json()).then(d=>{items=d.map(i=>({...i,price:+i.price}));Object.keys(cart).forEach(k=>{if(!items.some(i=>i.id==k))delete cart[k]});localStorage.setItem('cart',JSON.stringify(cart));render();drawer()}).catch(()=>{$('#items').innerHTML='<p>Menu unavailable. Check database setup.</p>'});
reveal();document.querySelectorAll('.gal div').forEach(d=>d.onkeydown=e=>e.key==='Enter'&&d.click());
(()=>{const s=[...document.querySelectorAll('.slide')],d=[...document.querySelectorAll('.dots button')];if(s.length<2)return;let i=0,t;
const go=n=>{i=(n+s.length)%s.length;s.forEach((e,k)=>e.classList.toggle('on',k===i));d.forEach((e,k)=>e.classList.toggle('on',k===i))};
const run=()=>{clearInterval(t);if(!matchMedia('(prefers-reduced-motion:reduce)').matches)t=setInterval(()=>go(i+1),6500)};
d.forEach((b,k)=>b.onclick=()=>{go(k);run()});document.addEventListener('visibilitychange',()=>document.hidden?clearInterval(t):run());run()})();
document.querySelectorAll('.vt').forEach(b=>{const v=b.querySelector('video');b.onclick=()=>{if(v.paused){document.querySelectorAll('.vt').forEach(o=>{if(o!==b){o.querySelector('video').pause();o.classList.remove('on')}});v.play().then(()=>b.classList.add('on')).catch(()=>{})}else{v.pause();b.classList.remove('on')}};v.onpause=()=>b.classList.remove('on')});
