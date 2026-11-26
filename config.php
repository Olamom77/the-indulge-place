<?php
// Railway MySQL variables are supported, with DB_* overrides for other hosts.
define('DB_HOST', getenv('DB_HOST') ?: getenv('MYSQLHOST') ?: 'localhost');
define('DB_PORT', getenv('DB_PORT') ?: getenv('MYSQLPORT') ?: '3306');
define('DB_NAME', getenv('DB_NAME') ?: getenv('MYSQLDATABASE') ?: 'indulge');
define('DB_USER', getenv('DB_USER') ?: getenv('MYSQLUSER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: getenv('MYSQLPASSWORD') ?: '');
define('ADMIN_USER', getenv('ADMIN_USER') ?: 'admin');
define('ADMIN_HASH', getenv('ADMIN_HASH') ?: '');

function db(): PDO {
    static $p;
    if ($p instanceof PDO) return $p;
    $dsn = 'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=utf8mb4';
    $p = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    ]);
    return $p;
}

function e($s) {
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}

// Public business details.
$S = [
    'whatsapp' => '2347075203145',
    'phone' => '+234 707 520 3145 / +234 802 645 2722',
    'address' => 'Lagos, Nigeria',
    'email' => 'theindulgeplace@gmail.com',
    'currency' => '₦',
    'instagram' => 'https://www.instagram.com/theindulgeplace/',
    'hours' => ['Monday - Sunday' => 'Opens 8:00 AM'],
    'map' => ''
];
