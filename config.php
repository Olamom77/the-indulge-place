<?php
const DB_HOST='localhost',DB_NAME='indulge',DB_USER='root',DB_PASS='';
const ADMIN_USER='admin';
// Generate: php -r "echo password_hash('YOUR_PASSWORD',PASSWORD_DEFAULT);"
const ADMIN_HASH='';
function db():PDO{static $p;return $p??=new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8mb4',DB_USER,DB_PASS,[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,PDO::ATTR_EMULATE_PREPARES=>false]);}
function e($s){return htmlspecialchars((string)$s,ENT_QUOTES,'UTF-8');}
// Editable site details (placeholders - replace with real info)
$S=['whatsapp'=>'2347075203145','phone'=>'+234 707 520 3145 / +234 802 645 2722','address'=>'Lagos, Nigeria','email'=>'theindulgeplace@gmail.com','currency'=>'₦',
'instagram'=>'https://www.instagram.com/theindulgeplace/','hours'=>['Monday - Friday'=>'[Opening hours]','Saturday - Sunday'=>'[Opening hours]'],'map'=>''];
