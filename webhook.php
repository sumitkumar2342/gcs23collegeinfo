<?php
include 'db.php';
$content = file_get_contents("php://input");
$update = json_decode($content, TRUE);

if (isset($update['message'])) {
    $chat_id = $update['message']['chat']['id'];
    $text = isset($update['message']['text']) ? $update['message']['text'] : '';
    
    // Yahan aap incoming message ko database mein save kar sakte hain taaki user ko reply dikhe
}
http_response_code(200);
echo "OK";
?>
