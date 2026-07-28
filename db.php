<?php
// Telegram Configurations
define('TELEGRAM_BOT_TOKEN', '8903053949:AAEUDCR95KN1J2CD76evOXGmWkHZghPnX4U');
define('TELEGRAM_CHAT_ID', '5959981401');

// Supabase Database Connection
$host = 'db.jumuvobxufwxxejjngbd.supabase.co';
$db = 'postgres';
$user = 'postgres';
$port = '5432';
$pass = 'Roshan@58123$'; // यहाँ अपना नया पासवर्ड डालें

$dsn = "pgsql:host=$host;port=$port;dbname=$db;";

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    echo "डेटाबेस से कनेक्शन सफल रहा!";
} catch (PDOException $e) {
    throw new PDOException($e->getMessage(), (int)$e->getCode());
}
?>
