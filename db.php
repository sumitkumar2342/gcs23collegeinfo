<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// InfinityFree Database Configurations
$host = "sql300.infinityfree.com";
$user = "if0_42285264"; 
$pass = "xYFQvMtcOuiTgd";     
$dbname = "if0_42285264_education";

// Telegram Configurations
define('TELEGRAM_BOT_TOKEN', '8903053949:AAEUDcR95kN1J2CD76ev0xGmWkHzHgPn7gg'); 
define('TELEGRAM_CHAT_ID', '5959981401'); 

// Database Connection
$conn = new mysqli($host, $user, $pass, $dbname);

// Connection Check
if ($conn->connect_error) {
    die("Database Connection failed: " . $conn->connect_error);
}

// Set UTF-8 encoding
$conn->set_charset("utf8mb4");

// Ensure bot_status table or columns exist
$conn->query("CREATE TABLE IF NOT EXISTS bot_status (
    id INT AUTO_INCREMENT PRIMARY KEY,
    status VARCHAR(20) DEFAULT 'offline',
    last_offline_time DATETIME NULL
)");

// Insert default row if empty
$res = $conn->query("SELECT id FROM bot_status LIMIT 1");
if ($res && $res->num_rows == 0) {
    $conn->query("INSERT INTO bot_status (status, last_offline_time) VALUES ('offline', NOW())");
}
?>
