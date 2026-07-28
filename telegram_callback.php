<?php
// एरर रिपोर्टिंग ऑन करें ताकि पता चले क्या दिक्कत है
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'db.php';

// टेलीग्राम से आने वाले डेटा को पढ़ना
$content = file_get_contents("php://input");
$update = json_decode($content, true);

// यह लाइन चेक करने के लिए है कि टेलीग्राम से डेटा आ रहा है या नहीं (इसे tg_log.txt में सेव करेगा)
file_put_contents('tg_log.txt', print_r($update, true), FILE_APPEND);

if (isset($update['message'])) {
    $message = $update['message'];
    $chatId = $message['chat']['id'] ?? '';
    $text = trim($message['text'] ?? '');

    if (!empty($chatId)) {
        // अगर यूजर /approve कमांड देता है
        if (stripos($text, '/approve') === 0) {
            $parts = explode(' ', $text);
            $requestId = isset($parts[1]) ? (int)$parts[1] : 0;
            
            if ($requestId > 0) {
                $stmt = $conn->prepare("UPDATE access_requests SET status = 'approved' WHERE id = ?");
                $stmt->bind_param("i", $requestId);
            } else {
                $stmt = $conn->prepare("UPDATE access_requests SET status = 'approved' WHERE status = 'pending' ORDER BY id DESC LIMIT 1");
            }
            
            if ($stmt->execute()) {
                sendTelegramMessage($chatId, "✅ Success: Access Approved! The resume is now unlocked for the HR.");
            } else {
                sendTelegramMessage($chatId, "❌ Database Error: Could not approve.");
            }
        }
        // अगर यूजर /reject कमांड देता है
        else if (stripos($text, '/reject') === 0) {
            $parts = explode(' ', $text);
            $requestId = isset($parts[1]) ? (int)$parts[1] : 0;
            
            if ($requestId > 0) {
                $stmt = $conn->prepare("UPDATE access_requests SET status = 'rejected' WHERE id = ?");
                $stmt->bind_param("i", $requestId);
            } else {
                $stmt->prepare("UPDATE access_requests SET status = 'rejected' WHERE status = 'pending' ORDER BY id DESC LIMIT 1");
            }
            
            if ($stmt->execute()) {
                sendTelegramMessage($chatId, "❌ Success: Access Rejected.");
            } else {
                sendTelegramMessage($chatId, "❌ Database Error: Could not reject.");
            }
        } else {
            // अगर कोई अन्य मैसेज भेजा तो बॉट बताएगा कि कमांड सही है या नहीं
            sendTelegramMessage($chatId, "ℹ️ Received your message. Use /approve <ID> or /reject <ID>");
        }
    }
}

// मैसेज भेजने का फिक्स फंक्शन (cURL का उपयोग ताकि यह होस्टिंग पर 100% काम करे)
function sendTelegramMessage($chatId, $messageText) {
    if (!defined('TELEGRAM_BOT_TOKEN')) return;
    
    $url = "https://api.telegram.org/bot" . TELEGRAM_BOT_TOKEN . "/sendMessage";
    $data = [
        'chat_id' => $chatId,
        'text' => $messageText,
        'parse_mode' => 'Markdown'
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $result = curl_exec($ch);
    curl_close($ch);
    
    // इसे भी लॉग में सेव करेंगे ताकि पता चले रिप्लाई गया या नहीं
    file_put_contents('tg_response_log.txt', $result, FILE_APPEND);
}
?>
