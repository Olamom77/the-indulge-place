<?php require 'config.php'; header('Content-Type: application/json');
$a=$_GET['a']??''; $in=json_decode(file_get_contents('php://input'),true)??[];
function out($d,$c=200){http_response_code($c);echo json_encode($d);exit;}
try{
if($a==='menu')out(db()->query("SELECT id,menu_type,category,name,description,price,CASE WHEN (image IS NULL OR image='') AND name='Indulge Supreme Yam Bowl' THEN 'assets/img/indulge-supreme-yam.jpg' ELSE image END AS image,featured FROM menu_items WHERE available=1 ORDER BY id")->fetchAll());
if($_SERVER['REQUEST_METHOD']!=='POST')out(['error'=>'Bad request'],405);
if($a==='order'){
 $n=trim($in['name']??'');$ph=trim($in['phone']??'');$t=$in['type']??'';$ad=trim($in['address']??'');$no=trim($in['notes']??'');
 if(!$n||mb_strlen($n)>100||!preg_match('/^[+\\d\\s()-]{7,20}$/',$ph)||!in_array($t,['delivery','pickup'])||($t==='delivery'&&!$ad)||!is_array($in['items']??null)||!$in['items'])out(['error'=>'Please complete all required fields.'],422);
 $st=db()->prepare('SELECT id,name,price FROM menu_items WHERE id=? AND available=1');$lines=[];$tot=0;
 foreach($in['items'] as $it){$st->execute([(int)($it['id']??0)]);$r=$st->fetch();$q=max(1,min(50,(int)($it['qty']??0)));if(!$r)continue;$lines[]=['name'=>$r['name'],'qty'=>$q,'price'=>(float)$r['price']];$tot+=$q*$r['price'];}
 if(!$lines)out(['error'=>'Cart is empty.'],422);
 db()->prepare('INSERT INTO orders(customer,phone,type,address,notes,items_json,total) VALUES(?,?,?,?,?,?,?)')->execute([$n,$ph,$t,mb_substr($ad,0,255),mb_substr($no,0,500),json_encode($lines),$tot]);
 out(['id'=>db()->lastInsertId(),'items'=>$lines,'total'=>$tot]);
}
if($a==='contact'){
 $n=trim($in['name']??'');$m=trim($in['message']??'');$em=trim($in['email']??'');
 if(!$n||!$m||!filter_var($em,FILTER_VALIDATE_EMAIL)||mb_strlen($m)>2000)out(['error'=>'Please check your details.'],422);
 db()->prepare('INSERT INTO messages(name,email,message) VALUES(?,?,?)')->execute([$n,$em,$m]);out(['ok'=>1]);
}
out(['error'=>'Not found'],404);
}catch(Throwable $x){out(['error'=>'Server error'],500);}
