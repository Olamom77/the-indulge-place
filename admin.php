<?php require 'config.php';
session_set_cookie_params(['httponly'=>true,'samesite'=>'Lax','secure'=>!empty($_SERVER['HTTPS'])]);session_start();
$err='';
if(isset($_GET['out'])){session_destroy();header('Location: admin.php');exit;}
if(isset($_POST['login'])){
 if(($_SESSION['fails']??0)>=5&&time()-($_SESSION['last']??0)<300)$err='Too many attempts. Try again in a few minutes.';
 elseif(ADMIN_HASH&&hash_equals(ADMIN_USER,(string)($_POST['u']??''))&&password_verify((string)($_POST['p']??''),ADMIN_HASH)){session_regenerate_id(true);$_SESSION=['a'=>1,'t'=>bin2hex(random_bytes(16))];header('Location: admin.php');exit;}
 else{$_SESSION['fails']=($_SESSION['fails']??0)+1;$_SESSION['last']=time();$err='Invalid username or password.';}
}
$in=!empty($_SESSION['a']);$T=$_SESSION['t']??'';
if($in&&$_SERVER['REQUEST_METHOD']==='POST'){
 if(!hash_equals($T,(string)($_POST['t']??'')))die('Invalid session token. Reload and try again.');
 $db=db();$a=$_POST['act']??'';$id=(int)($_POST['id']??0);$tab='menu';
 if($a==='save'){
  $img=trim($_POST['image']??'');$ok=trim($_POST['name']??'')!==''&&trim($_POST['category']??'')!==''&&is_numeric($_POST['price']??'')&&$_POST['price']>=0&&$_POST['price']<100000;
  if(!$ok)$_SESSION['flash']='Name, category and a valid price are required.';
  elseif($img!==''&&!preg_match('~^(https?://|/|assets/)[^\s\'"()\\\\<>]*$~',$img))$_SESSION['flash']='Image must be a path like assets/img/x.jpg or an http(s) URL.';
  else{$v=[in_array($_POST['menu_type']??'',['Main Restaurant','Fine Dining'])?$_POST['menu_type']:'Main Restaurant',mb_substr(trim($_POST['category']),0,60),mb_substr(trim($_POST['name']),0,120),trim($_POST['description']??''),round((float)$_POST['price'],2),$img,isset($_POST['featured'])?1:0,isset($_POST['available'])?1:0];
   if($id){$v[]=$id;$db->prepare('UPDATE menu_items SET menu_type=?,category=?,name=?,description=?,price=?,image=?,featured=?,available=? WHERE id=?')->execute($v);}
   else $db->prepare('INSERT INTO menu_items(menu_type,category,name,description,price,image,featured,available) VALUES(?,?,?,?,?,?,?,?)')->execute($v);
   $_SESSION['flash']='Saved.';}}
 if($a==='del'&&$id){$db->prepare('DELETE FROM menu_items WHERE id=?')->execute([$id]);$_SESSION['flash']='Item deleted.';}
 if($a==='status'){$tab='orders';if(in_array($_POST['status']??'',['new','preparing','done','cancelled'])&&$id){$db->prepare('UPDATE orders SET status=? WHERE id=?')->execute([$_POST['status'],$id]);$_SESSION['flash']='Order #'.$id.' updated.';}}
 header('Location: admin.php?t='.$tab);exit;}
$flash=$_SESSION['flash']??'';unset($_SESSION['flash']);$tab=$_GET['t']??'dashboard';
?><!doctype html><html lang="en"><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="robots" content="noindex"><title>Admin - The Indulge Place</title>
<style>body{font:15px system-ui;background:#f4eee6;color:#1b1410;max-width:1000px;margin:auto;padding:16px}input,select,textarea,button{font:inherit;padding:8px;border:1px solid #cbbfae;border-radius:8px;max-width:100%}button{background:#1b1410;color:#fff;cursor:pointer}.g{display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:8px;background:#fff;padding:12px;border-radius:12px;margin:8px 0;align-items:center}.d{background:#9b2c1d}a{color:#9b2c1d}.f{background:#e7f3e8;padding:10px;border-radius:8px}.f.e{background:#fbe5e1}nav a{margin-right:12px}</style>
<?php if(!$in): ?><h1>Admin login</h1><?php if($err)echo '<p class="f e">'.e($err).'</p>';if(!ADMIN_HASH)echo '<p class="f e">ADMIN_HASH is empty in config.php - set it first (see README).</p>';?>
<form method="post"><input name="u" placeholder="Username" autocomplete="username"> <input name="p" type="password" placeholder="Password" autocomplete="current-password"> <button name="login" value="1">Log in</button></form><?php exit; endif;
try{$db=db();}catch(Throwable $e){http_response_code(503);echo '<h1>Database not connected</h1><p>Connect the Railway MySQL service, add MYSQLHOST, MYSQLPORT, MYSQLDATABASE, MYSQLUSER and MYSQLPASSWORD to this web service, then import schema.sql.</p><p><a href="admin.php?out=1">Log out</a></p>';exit;}
function f($r,$T){$r+=['id'=>0,'menu_type'=>'Main Restaurant','category'=>'','name'=>'','description'=>'','price'=>'','image'=>'','featured'=>0,'available'=>1];?>
<form method="post" class="g"><input type="hidden" name="t" value="<?=e($T)?>"><input type="hidden" name="id" value="<?=(int)$r['id']?>">
<select name="menu_type"><?php foreach(['Main Restaurant','Fine Dining']as $mt)echo '<option'.($mt===$r['menu_type']?' selected':'').'>'.$mt.'</option>';?></select><input name="name" placeholder="Name" value="<?=e($r['name'])?>" required maxlength="120"><input name="category" placeholder="Category" value="<?=e($r['category'])?>" required maxlength="60">
<input name="price" type="number" step="0.01" min="0" placeholder="Price" value="<?=e($r['price'])?>" required><input name="image" placeholder="Image path/URL" value="<?=e($r['image'])?>">
<input name="description" placeholder="Description" value="<?=e($r['description'])?>"><label><input type="checkbox" name="featured" <?=$r['featured']?'checked':''?>> Featured</label><label><input type="checkbox" name="available" <?=$r['available']?'checked':''?>> Available</label>
<button name="act" value="save"><?=$r['id']?'Save':'Add item'?></button><?php if($r['id']):?><button class="d" name="act" value="del" onclick="return confirm('Delete this item?')">Delete</button><?php endif?></form><?php }?>
<h1>Admin</h1><nav><a href="?t=dashboard">Dashboard</a><a href="?t=menu">Menu</a><a href="?t=orders">Orders</a><a href="?t=messages">Messages</a><a href="./">View site</a><a href="?out=1">Log out</a></nav>
<?php if($flash)echo '<p class="f'.(preg_match('/required|must/',$flash)?' e':'').'">'.e($flash).'</p>';
if($tab==='dashboard'):$counts=['menu'=>(int)$db->query('SELECT COUNT(*) FROM menu_items')->fetchColumn(),'orders'=>(int)$db->query('SELECT COUNT(*) FROM orders')->fetchColumn(),'new'=>(int)$db->query("SELECT COUNT(*) FROM orders WHERE status='new'")->fetchColumn(),'messages'=>(int)$db->query('SELECT COUNT(*) FROM messages')->fetchColumn()];?><div class="g"><?php foreach(['menu'=>'Menu items','orders'=>'Total orders','new'=>'New orders','messages'=>'Messages'] as $k=>$label):?><div><small><?=e($label)?></small><h2><?=(int)$counts[$k]?></h2></div><?php endforeach;?></div><p>Welcome to your restaurant dashboard. Use the menu to manage dishes, update order status and read customer messages.</p><?php elseif($tab==='orders'): $rows=$db->query('SELECT * FROM orders ORDER BY id DESC LIMIT 100')->fetchAll(); if(!$rows)echo '<p>No orders yet.</p>'; foreach($rows as $o):?>
<form method="post" class="g"><input type="hidden" name="t" value="<?=e($T)?>"><input type="hidden" name="id" value="<?=(int)$o['id']?>"><input type="hidden" name="act" value="status">
<div><b>#<?=(int)$o['id']?></b> <?=e($o['created_at'])?><br><?=e($o['customer'])?> - <?=e($o['phone'])?><br><?=e($o['type'])?> <?=e($o['address'])?></div>
<div><?php foreach(json_decode($o['items_json'],true)?:[] as $l)echo e($l['qty'].' x '.$l['name']).'<br>';?><b>Total: <?=e(number_format((float)$o['total'],2))?></b><br><i><?=e($o['notes'])?></i></div>
<select name="status"><?php foreach(['new','preparing','done','cancelled']as $s)echo '<option'.($s===$o['status']?' selected':'').'>'.$s.'</option>';?></select><button>Update</button></form>
<?php endforeach; elseif($tab==='messages'): $rows=$db->query('SELECT * FROM messages ORDER BY id DESC LIMIT 100')->fetchAll(); if(!$rows)echo '<p>No messages yet.</p>'; foreach($rows as $m):?>
<div class="g"><div><b><?=e($m['name'])?></b> &lt;<a href="mailto:<?=e($m['email'])?>"><?=e($m['email'])?></a>&gt;<br><small><?=e($m['created_at'])?></small></div><div style="grid-column:span 2"><?=nl2br(e($m['message']))?></div></div>
<?php endforeach; else: ?><h2>Add item</h2><?php f([],$T);?><h2>Items</h2><?php foreach($db->query('SELECT * FROM menu_items ORDER BY id')as $r)f($r,$T);endif;?>
