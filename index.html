<?php
// Set Indian Timezone
date_default_timezone_set('Asia/Kolkata');

// Error reporting off for clean production execution
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(0);

include 'db.php';
session_start();

$botToken = "8903053949:AAEUDcR95kN1J2CD76ev0xGmWkHzHgPn7gg";
$admin_chat_id = "5959981401";

// Ensure database tables & columns exist automatically
$conn->query("CREATE TABLE IF NOT EXISTS access_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_name VARCHAR(255) NOT NULL,
    user_email VARCHAR(255) NOT NULL,
    status VARCHAR(50) DEFAULT 'pending',
    created_at DATETIME NULL,
    expires_at DATETIME NULL
)");

$conn->query("CREATE TABLE IF NOT EXISTS profile_data (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(255),
    email VARCHAR(255),
    phone VARCHAR(50),
    address TEXT,
    career_objective TEXT,
    education TEXT,
    skills TEXT,
    profile_pic VARCHAR(255)
)");

$conn->query("CREATE TABLE IF NOT EXISTS chat_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    sender_type ENUM('user', 'admin') NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

$check_column = $conn->query("SHOW COLUMNS FROM access_requests LIKE 'expires_at'");
if ($check_column && $check_column->num_rows == 0) {
    $conn->query("ALTER TABLE access_requests ADD COLUMN expires_at DATETIME NULL");
}

function handleSessionExpirationAndNotify($conn, $reason = "Timer expired.") {
    if (isset($_SESSION['user_request_id'])) {
        $req_id = (int)$_SESSION['user_request_id'];
        
        $res = $conn->query("SELECT user_name, user_email FROM access_requests WHERE id = $req_id");
        if ($res && $res->num_rows > 0) {
            $row = $res->fetch_assoc();
            $userName = $row['user_name'];
            $userCompany = $row['user_email'];

            global $botToken, $admin_chat_id;
            $text = "❌ *Account Deactivated & Access Expired!*\n\n" .
                    "🆔 *Request ID:* {$req_id}\n" .
                    "👤 *HR Name:* {$userName}\n" .
                    "🏢 *Company/Email:* {$userCompany}\n" .
                    "⏱ *Reason:* {$reason}";
            
            $payload = ['chat_id' => $admin_chat_id, 'text' => $text, 'parse_mode' => 'Markdown'];
            $tgUrl = 'https://api.telegram.org/bot' . $botToken . '/sendMessage';
            
            $ch = curl_init($tgUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_exec($ch);
            curl_close($ch);
        }
        $conn->query("DELETE FROM access_requests WHERE id = $req_id");
    }
    unset($_SESSION['user_request_id']);
    unset($_SESSION['user_company']);
}

// Handle Profile Update Request & Telegram Notification
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile_data'])) {
    header('Content-Type: application/json');
    
    $fullName = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $objective = trim($_POST['career_objective']);
    $education = trim($_POST['education']);
    $skills = trim($_POST['skills']);

    $stmt = $conn->prepare("UPDATE profile_data SET full_name = ?, email = ?, phone = ?, address = ?, career_objective = ?, education = ?, skills = ? LIMIT 1");
    if($stmt) {
        $stmt->bind_param("sssssss", $fullName, $email, $phone, $address, $objective, $education, $skills);
        if ($stmt->execute()) {
            $updateText = "✏️ *Profile Updated Successfully!*\n\n" .
                          "👤 *Name:* {$fullName}\n" .
                          "📧 *Email:* {$email}\n" .
                          "📞 *Phone:* {$phone}\n" .
                          "📍 *Address:* {$address}\n" .
                          "🕒 *Time:* " . date('d M Y, h:i:s A');
            
            $payload = ['chat_id' => $admin_chat_id, 'text' => $updateText, 'parse_mode' => 'Markdown'];
            $tgUrl = 'https://api.telegram.org/bot' . $botToken . '/sendMessage';
            
            $ch = curl_init($tgUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_exec($ch);
            curl_close($ch);

            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => $stmt->error]);
        }
    } else {
        echo json_encode(['success' => false, 'error' => $conn->error]);
    }
    exit();
}

// Handle Login Form via AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax_login'])) {
    header('Content-Type: application/json');
    $loginName = trim($_POST['login_name']);
    $loginEmail = trim($_POST['login_email']);

    $stmt = $conn->prepare("SELECT id, status, expires_at FROM access_requests WHERE TRIM(LOWER(user_name)) = TRIM(LOWER(?)) AND TRIM(LOWER(user_email)) = TRIM(LOWER(?)) ORDER BY id DESC LIMIT 1");
    if($stmt) {
        $stmt->bind_param("ss", $loginName, $loginEmail);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            
            if (strtolower($row['status']) !== 'approved') {
                echo json_encode(['success' => false, 'error' => 'Your request status is currently "' . $row['status'] . '". Please wait for admin approval.']);
                exit();
            }

            if (!empty($row['expires_at']) && strtotime($row['expires_at']) <= time()) {
                echo json_encode(['success' => false, 'error' => 'Your approved permission has expired. Please request access again.']);
                exit();
            }

            $_SESSION['user_request_id'] = $row['id'];
            $_SESSION['user_company'] = $loginEmail;
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'No active approved permission found for this Name & Email.']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => $conn->error]);
    }
    exit();
}

// Handle AJAX Chat Message Submission & Telegram forwarding
if (isset($_POST['action']) && $_POST['action'] === 'send_chat_message') {
    header('Content-Type: application/json');
    $message = trim($_POST['message'] ?? '');
    
    if (empty($message)) {
        echo json_encode(['success' => false, 'error' => 'Message cannot be empty.']);
        exit();
    }

    $userName = "Anonymous Visitor";
    $userEmail = "Not Registered";
    $requestId = "N/A";
    $req_id = 0;

    if (isset($_SESSION['user_request_id'])) {
        $req_id = (int)$_SESSION['user_request_id'];
        $res = $conn->query("SELECT id, user_name, user_email FROM access_requests WHERE id = $req_id");
        if ($res && $res->num_rows > 0) {
            $row = $res->fetch_assoc();
            $requestId = $row['id'];
            $userName = $row['user_name'];
            $userEmail = $row['user_email'];
        }
    }

    if($req_id > 0) {
        $stmt_ins = $conn->prepare("INSERT INTO chat_messages (user_id, sender_type, message) VALUES (?, 'user', ?)");
        if($stmt_ins) {
            $stmt_ins->bind_param("is", $req_id, $message);
            $stmt_ins->execute();
        }
    }

    $chatText = "💬 *New Live Chat Message Received!*\n\n" .
                "🆔 *Request ID:* {$requestId}\n" .
                "👤 *Name:* {$userName}\n" .
                "🏢 *Company/Email:* {$userEmail}\n\n" .
                "💬 *Message:* \n" . $message;
    
    $payload = ['chat_id' => $admin_chat_id, 'text' => $chatText, 'parse_mode' => 'Markdown'];
    $tgUrl = 'https://api.telegram.org/bot' . $botToken . '/sendMessage';
    
    $ch = curl_init($tgUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_exec($ch);
    curl_close($ch);
    
    echo json_encode(['success' => true]);
    exit();
}

// Handle Message Editing via AJAX
if (isset($_POST['action']) && $_POST['action'] === 'edit_chat_msg' && isset($_SESSION['user_request_id'])) {
    header('Content-Type: application/json');
    $msg_id = intval($_POST['msg_id']);
    $req_id = intval($_SESSION['user_request_id']);
    $new_text = trim($_POST['new_text']);

    if(empty($new_text)) {
        echo json_encode(['success' => false, 'error' => 'Empty message.']);
        exit();
    }

    $chk_time = $conn->query("SELECT created_at FROM chat_messages WHERE id=$msg_id AND user_id=$req_id AND sender_type='user' LIMIT 1");
    if($chk_time && $chk_time->num_rows > 0) {
        $row = $chk_time->fetch_assoc();
        if(time() - strtotime($row['created_at']) <= 60) {
            $stmt_upd = $conn->prepare("UPDATE chat_messages SET message = ? WHERE id = ? AND user_id = ?");
            if($stmt_upd) {
                $stmt_upd->bind_param("sii", $new_text, $msg_id, $req_id);
                $stmt_upd->execute();
            }
            echo json_encode(['success' => true]);
            exit();
        } else {
            echo json_encode(['success' => false, 'error' => 'expired']);
            exit();
        }
    }
    echo json_encode(['success' => false, 'error' => 'not_found']);
    exit();
}

// Handle Message Deletion via AJAX
if (isset($_POST['action']) && $_POST['action'] === 'delete_chat_msg' && isset($_SESSION['user_request_id'])) {
    header('Content-Type: application/json');
    $msg_id = intval($_POST['msg_id']);
    $req_id = intval($_SESSION['user_request_id']);

    $conn->query("DELETE FROM chat_messages WHERE id=$msg_id AND user_id=$req_id AND sender_type='user'");
    echo json_encode(['success' => true]);
    exit();
}

if (isset($_GET['action']) && $_GET['action'] === 'expire_session') {
    header('Content-Type: application/json');
    handleSessionExpirationAndNotify($conn, "Access duration completed. Account revoked.");
    echo json_encode(['success' => true]);
    exit();
}

if (isset($_GET['reset'])) {
    unset($_SESSION['user_request_id']);
    unset($_SESSION['user_company']);
    header("Location: index.php");
    exit();
}

if (isset($_GET['action']) && $_GET['action'] === 'check_status') {
    header('Content-Type: application/json');
    $status = 'none';
    
    if (isset($_SESSION['user_request_id'])) {
        $req_id = (int)$_SESSION['user_request_id'];
        $check_query = $conn->query("SELECT status, expires_at FROM access_requests WHERE id = $req_id");
        if ($check_query && $check_query->num_rows > 0) {
            $row = $check_query->fetch_assoc();
            $status = $row['status'];
            
            if ($status === 'approved' && !empty($row['expires_at'])) {
                if (strtotime($row['expires_at']) <= time()) {
                    handleSessionExpirationAndNotify($conn, "Access duration completed.");
                    $status = 'none';
                }
            }
        } else {
            $status = 'none';
            unset($_SESSION['user_request_id']);
            unset($_SESSION['user_company']);
        }
    }
    echo json_encode(['status' => $status]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax_request'])) {
    header('Content-Type: application/json');
    
    $userName = trim($_POST['user_name']);
    $userCompany = trim($_POST['user_company']);
    $current_ist_time = date('Y-m-d H:i:s');

    $stmt = $conn->prepare("INSERT INTO access_requests (user_name, user_email, status, created_at) VALUES (?, ?, 'pending', ?)");
    if ($stmt) {
        $stmt->bind_param("sss", $userName, $userCompany, $current_ist_time);
        
        if ($stmt->execute()) {
            $requestId = $stmt->insert_id;
            $_SESSION['user_request_id'] = $requestId;
            $_SESSION['user_company'] = $userCompany;

            $formatted_time = date('d M Y, h:i:s A', strtotime($current_ist_time));

            echo json_encode([
                'success' => true,
                'request_id' => $requestId,
                'request_time' => $formatted_time,
                'bot_token' => $botToken,
                'chat_id' => $admin_chat_id
            ]);
        } else {
            echo json_encode(['success' => false, 'error' => $stmt->error]);
        }
    } else {
        echo json_encode(['success' => false, 'error' => $conn->error]);
    }
    exit();
}

$user_status = 'none';
$request_time = '';
$expires_at_timestamp = 0;

if (isset($_SESSION['user_request_id'])) {
    $req_id = (int)$_SESSION['user_request_id'];
    $check_query = $conn->query("SELECT status, created_at, expires_at FROM access_requests WHERE id = $req_id");
    if ($check_query && $check_query->num_rows > 0) {
        $row = $check_query->fetch_assoc();
        $user_status = $row['status'];
        $request_time = date('d M Y, h:i:s A', strtotime($row['created_at']));
        
        if ($user_status === 'approved' && !empty($row['expires_at'])) {
            $expires_at_timestamp = strtotime($row['expires_at']) * 1000;
        }
    } else {
        $user_status = 'none';
        unset($_SESSION['user_request_id']);
        unset($_SESSION['user_company']);
    }
}

$profile_res = $conn->query("SELECT * FROM profile_data LIMIT 1");
$profile = ($profile_res && $profile_res->num_rows > 0) ? $profile_res->fetch_assoc() : [];
$documents = $conn->query("SELECT * FROM documents ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($profile['full_name'] ?? 'Professional Resume'); ?> - Secure Access</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --navy-primary: #38bdf8;
            --navy-secondary: #0ea5e9;
            --bg-light: #000000;
            --card-bg: #111827;
            --text-dark: #f3f4f6;
            --border-color: #374151;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { 
            font-family: 'Inter', sans-serif; 
            background-color: var(--bg-light); 
            color: var(--text-dark); 
            padding-bottom: 90px; 
            -webkit-user-select: none;
            user-select: none;
        }

        .confidential-watermark {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            pointer-events: none; z-index: 9999; overflow: hidden;
            display: flex; align-items: center; justify-content: center;
            opacity: 0.05; font-size: 35px; font-weight: 700; color: #ffffff;
            transform: rotate(-30deg); white-space: nowrap;
        }

        .top-flag-bar { height: 6px; background: linear-gradient(to right, #ff9933 33%, #ffffff 33%, #ffffff 66%, #138808 66%); }
        .container { max-width: 950px; margin: 30px auto; padding: 0 15px; }
        .card { background: var(--card-bg); border-radius: 12px; border: 1px solid var(--border-color); border-top: 5px solid var(--navy-secondary); padding: 30px; box-shadow: 0 10px 25px rgba(0, 0, 0, 0.8); margin-bottom: 25px; }

        .lock-box { text-align: center; padding: 30px 15px; }
        .lock-icon { font-size: 50px; margin-bottom: 12px; }
        .lock-title { font-size: 22px; color: var(--navy-primary); font-weight: 700; margin-bottom: 8px; }
        .lock-desc { font-size: 14px; color: #9ca3af; margin-bottom: 25px; line-height: 1.6; }

        .form-group { margin-bottom: 18px; text-align: left; max-width: 420px; margin-left: auto; margin-right: auto; }
        label { display: block; font-size: 13px; font-weight: 600; color: var(--navy-primary); margin-bottom: 6px; }
        input[type="text"], input[type="email"], textarea { width: 100%; padding: 12px 14px; background: #1f2937; color: #ffffff; border: 1px solid var(--border-color); border-radius: 6px; font-size: 14px; outline: none; user-select: text; }
        input[type="text"]:focus, input[type="email"]:focus, textarea:focus { border-color: var(--navy-secondary); box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.2); }
        
        .btn-submit { background: var(--navy-secondary); color: #fff; border: none; padding: 12px 24px; border-radius: 6px; font-weight: 600; font-size: 15px; cursor: pointer; width: 100%; max-width: 420px; transition: 0.2s; margin-top: 10px; }
        .btn-submit:hover { background: var(--navy-primary); color: #000; }

        .toggle-text-btn { margin-top: 20px; font-size: 13px; color: var(--navy-primary); background: none; border: none; cursor: pointer; text-decoration: underline; font-weight: 600; display: inline-block; }
        .toggle-text-btn:hover { color: #ffffff; }

        .back-link-btn { display: inline-flex; align-items: center; gap: 6px; margin-top: 15px; font-size: 13px; color: #9ca3af; background: none; border: none; cursor: pointer; font-weight: 600; }
        .back-link-btn:hover { color: var(--navy-primary); }

        .info-card { background: #1f2937; border: 1px solid var(--border-color); border-left: 4px solid var(--navy-secondary); border-radius: 8px; padding: 20px; max-width: 550px; margin: 20px auto 0; text-align: left; }
        .info-badge { display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 700; margin-bottom: 12px; text-transform: uppercase; }
        .badge-pending { background: #451a03; color: #fbbf24; }
        .badge-rejected { background: #450a0a; color: #f87171; }
        .info-text { font-size: 14px; color: #d1d5db; line-height: 1.6; margin-bottom: 12px; }
        .info-time { font-size: 12px; color: #9ca3af; font-weight: 600; border-top: 1px dashed var(--border-color); padding-top: 10px; }

        .profile-header { display: flex; gap: 25px; align-items: center; border-bottom: 2px solid var(--border-color); padding-bottom: 20px; margin-bottom: 20px; }
        .photo-box { width: 110px; height: 130px; min-width: 110px; border: 2px solid var(--navy-secondary); background: #1f2937; border-radius: 8px; overflow: hidden; display: flex; align-items: center; justify-content: center; }
        .photo-box img { width: 100%; height: 100%; object-fit: contain; }
        .profile-info h1 { font-size: 24px; color: var(--navy-primary); font-weight: 700; }
        .profile-info p { font-size: 13.5px; color: #9ca3af; margin-top: 4px; }

        .sec-title-wrapper { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid var(--border-color); padding-bottom: 6px; margin-bottom: 12px; }
        .sec-title { font-size: 16px; color: var(--navy-primary); font-weight: 700; text-transform: uppercase; margin: 0; }
        
        .print-btn { background-color: var(--navy-secondary); color: #ffffff; border: none; padding: 5px 12px; font-size: 12px; font-weight: 600; cursor: pointer; border-radius: 4px; }
        .print-btn:hover { background-color: var(--navy-primary); color: #000000; }

        .timer-banner { background: rgba(239, 68, 68, 0.15); border: 1px solid #ef4444; color: #f87171; padding: 10px 15px; border-radius: 6px; font-size: 13px; font-weight: 600; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; }

        .sec-content { font-size: 14px; line-height: 1.6; color: #d1d5db; white-space: pre-line; margin-bottom: 20px; }
        .doc-list { list-style: none; }
        .doc-item { display: flex; justify-content: space-between; align-items: center; padding: 10px 14px; background: #1f2937; border: 1px solid var(--border-color); border-radius: 6px; margin-bottom: 10px; }
        .reset-link { margin-top: 20px; display: inline-block; font-size: 12px; color: #9ca3af; text-decoration: none; }
        .reset-link:hover { text-decoration: underline; color: #f87171; }
        .view-doc-btn { background: none; border: none; cursor: pointer; padding: 4px 8px; font-size: 18px; }

        #docModal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.95); justify-content: center; align-items: center; z-index: 10000; backdrop-filter: blur(15px); }
        .modal-content { background: #1f2937; color: #fff; padding: 20px; border-radius: 8px; max-width: 600px; width: 90%; text-align: center; position: relative; max-height: 85vh; overflow-y: auto; border: 1px solid var(--border-color); }
        .close-btn { position: absolute; top: 10px; right: 15px; font-size: 22px; cursor: pointer; color: #9ca3af; }
        .close-btn:hover { color: #fff; }
        .view-timer-badge { background: #ef4444; color: #fff; font-size: 12px; padding: 3px 10px; border-radius: 12px; display: inline-block; margin-bottom: 10px; font-weight: 600; }

        /* Chat Drawer */
        #whatsappChatDrawer { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: #0b0f19; z-index: 12000; flex-direction: column; }
        .wa-chat-header { background: #111827; color: #fff; padding: 18px 25px; display: flex; justify-content: space-between; align-items: center; font-weight: 700; font-size: 18px; border-bottom: 1px solid var(--border-color); }
        .bot-status-indicator { padding: 6px 12px; font-size: 12px; font-weight: 600; text-align: center; background: #064e3b; color: #34d399; }
        .wa-chat-body { flex: 1; padding: 20px; overflow-y: auto; font-size: 14.5px; color: #d1d5db; max-width: 800px; width: 100%; margin: 0 auto; display: flex; flex-direction: column; gap: 12px; }
        
        .chat-bubble { max-width: 75%; padding: 12px 16px; border-radius: 14px; line-height: 1.5; word-break: break-word; font-size: 14px; box-shadow: 0 2px 5px rgba(0,0,0,0.2); cursor: pointer; }
        .chat-bubble.user { align-self: flex-end; background: #1f2937; color: #f3f4f6; border: 1px solid var(--border-color); border-bottom-right-radius: 2px; }
        .chat-bubble.admin { align-self: flex-start; background: #064e3b; color: #34d399; border: 1px solid #059669; border-bottom-left-radius: 2px; }

        .wa-chat-footer { padding: 15px 20px; background: #111827; border-top: 1px solid var(--border-color); display: flex; gap: 12px; max-width: 800px; width: 100%; margin: 0 auto; }
        .wa-chat-input { flex: 1; background: #1f2937; color: #fff; border: 1px solid var(--border-color); border-radius: 8px; padding: 12px 16px; font-size: 14px; outline: none; user-select: text; }
        .wa-chat-input:focus { border-color: var(--navy-secondary); }
        .wa-send-btn { background: #0ea5e9; color: #000; border: none; padding: 12px 24px; border-radius: 8px; font-weight: 600; font-size: 14px; cursor: pointer; transition: 0.2s; }
        .wa-send-btn:hover { background: #38bdf8; }

        #msgActionModal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); z-index: 13000; justify-content: center; align-items: center; }
        .action-modal-box { background: #111827; border: 1px solid var(--border-color); padding: 20px; border-radius: 8px; width: 280px; text-align: center; }
        .action-btn { background: #1f2937; color: #fff; border: 1px solid var(--border-color); padding: 10px; width: 100%; border-radius: 6px; margin-bottom: 8px; cursor: pointer; font-weight: 600; font-size: 13px; }
        .action-btn:hover { background: #0ea5e9; color: #000; }
        .action-btn-del { color: #f87171; }

        .instagram-nav { position: fixed; bottom: 0; left: 0; width: 100%; background: #111827; border-top: 1px solid var(--border-color); display: flex; justify-content: space-around; align-items: center; padding: 10px 0; z-index: 1000; }
        .nav-item { display: flex; flex-direction: column; align-items: center; color: #9ca3af; text-decoration: none; font-size: 11px; font-weight: 500; cursor: pointer; background: none; border: none; }
        .nav-item span.icon { font-size: 20px; margin-bottom: 2px; }
        .nav-item:hover, .nav-item.active { color: var(--navy-primary); }
        .nav-plus-btn { background: linear-gradient(135deg, #0ea5e9, #38bdf8); color: #000; width: 44px; height: 44px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 24px; font-weight: bold; border: none; cursor: pointer; }

        @media (max-width: 600px) { .profile-header { flex-direction: column; text-align: center; } }
    </style>
</head>
<body oncontextmenu="return false;">

<div class="confidential-watermark">🔒 SECURE PROFILE - SCREENSHOT & RECORDING RESTRICTED 🔒</div>
<div class="top-flag-bar"></div>
<div class="container">

    <?php if ($user_status === 'approved' || $user_status === 'pending' || $user_status === 'none'): ?>
        <?php if ($user_status === 'approved'): ?>
            <div class="card">
                <div class="timer-banner">
                    <span>⏱️ Time Remaining: <span id="countdownTimer">--:--:--</span></span>
                    <span id="realTimeClock" style="font-size: 11px; font-weight: 700;">--:--:--</span>
                </div>
                
                <div style="margin-bottom: 20px; text-align: right;">
                    <button class="print-btn" onclick="toggleEditForm()" style="background:#064e3b; color:#34d399;">✏️ Edit Profile Data</button>
                </div>

                <div id="profileEditSection" style="display: none; background: #1f2937; padding: 20px; border-radius: 8px; margin-bottom: 25px; border: 1px solid var(--border-color);">
                    <h3 style="color: var(--navy-primary); margin-bottom: 15px; font-size: 16px;">Update Profile Details</h3>
                    <form id="updateProfileForm">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                            <div>
                                <label>Full Name:</label>
                                <input type="text" id="edit_full_name" value="<?php echo htmlspecialchars($profile['full_name'] ?? ''); ?>" required>
                            </div>
                            <div>
                                <label>Email:</label>
                                <input type="email" id="edit_email" value="<?php echo htmlspecialchars($profile['email'] ?? ''); ?>" required>
                            </div>
                            <div>
                                <label>Phone:</label>
                                <input type="text" id="edit_phone" value="<?php echo htmlspecialchars($profile['phone'] ?? ''); ?>">
                            </div>
                            <div>
                                <label>Address:</label>
                                <input type="text" id="edit_address" value="<?php echo htmlspecialchars($profile['address'] ?? ''); ?>">
                            </div>
                        </div>
                        <div style="margin-bottom: 15px;">
                            <label>Career Objective:</label>
                            <textarea id="edit_career_objective" rows="3"><?php echo htmlspecialchars($profile['career_objective'] ?? ''); ?></textarea>
                        </div>
                        <div style="margin-bottom: 15px;">
                            <label>Educational Qualification:</label>
                            <textarea id="edit_education" rows="3"><?php echo htmlspecialchars($profile['education'] ?? ''); ?></textarea>
                        </div>
                        <div style="margin-bottom: 15px;">
                            <label>Key Skills:</label>
                            <textarea id="edit_skills" rows="3"><?php echo htmlspecialchars($profile['skills'] ?? ''); ?></textarea>
                        </div>
                        <button type="submit" id="updateProfileBtn" class="btn-submit" style="background:#064e3b; color:#34d399; max-width: 200px;">Save Changes</button>
                        <button type="button" class="back-link-btn" onclick="toggleEditForm()" style="margin-left: 15px;">Cancel</button>
                    </form>
                </div>

                <div class="profile-header">
                    <div class="photo-box">
                        <img src="<?php echo !empty($profile['profile_pic']) ? htmlspecialchars($profile['profile_pic']) : 'photo.jpg'; ?>" alt="Profile Photo">
                    </div>
                    <div class="profile-info">
                        <h1><?php echo htmlspecialchars($profile['full_name'] ?? 'N/A'); ?></h1>
                        <p>📧 <?php echo htmlspecialchars($profile['email'] ?? 'N/A'); ?> | 📞 <?php echo htmlspecialchars($profile['phone'] ?? 'N/A'); ?></p>
                        <p>📍 <?php echo htmlspecialchars($profile['address'] ?? 'N/A'); ?></p>
                    </div>
                </div>
                <?php if(!empty($profile['career_objective'])): ?>
                    <div class="sec-title-wrapper"><div class="sec-title">Career Objective</div></div>
                    <div class="sec-content"><?php echo htmlspecialchars($profile['career_objective']); ?></div>
                <?php endif; ?>
                <?php if(!empty($profile['education'])): ?>
                    <div class="sec-title-wrapper"><div class="sec-title">Educational Qualification</div></div>
                    <div class="sec-content"><?php echo htmlspecialchars($profile['education']); ?></div>
                <?php endif; ?>
                <?php if(!empty($profile['skills'])): ?>
                    <div class="sec-title-wrapper"><div class="sec-title">Key Skills</div></div>
                    <div class="sec-content"><?php echo htmlspecialchars($profile['skills']); ?></div>
                <?php endif; ?>
                <div class="sec-title-wrapper">
                    <div class="sec-title">Attached Documents</div>
                    <button class="print-btn" onclick="window.print()">🖨️ Print / PDF</button>
                </div>
                <ul class="doc-list">
                    <?php if($documents && $documents->num_rows > 0): while($doc = $documents->fetch_assoc()): ?>
                        <li class="doc-item">
                            <span>📄 <strong><?php echo htmlspecialchars($doc['doc_name']); ?></strong></span>
                            <button class="view-doc-btn" onclick="openDocModal('<?php echo htmlspecialchars($doc['file_path']); ?>', '<?php echo htmlspecialchars($doc['doc_name']); ?>')">👁️</button>
                        </li>
                    <?php endwhile; else: ?>
                        <li class="doc-item" style="color:#9ca3af;">No documents available.</li>
                    <?php endif; ?>
                </ul>
            </div>
        <?php elseif ($user_status === 'pending'): ?>
            <div class="card lock-box">
                <div class="lock-icon">⏳</div>
                <div class="lock-title">Authorization Pending</div>
                <div class="info-card">
                    <span class="info-badge badge-pending">🕒 Status: Waiting for Approval</span>
                    <div class="info-text">Your request has been sent. Website will open automatically as soon as admin approves!</div>
                    <div class="info-time">⏱ Request Sent: <?php echo $request_time; ?></div>
                </div>
                <a href="index.php?reset=1" class="reset-link">🔄 Exit / Change Request</a>
            </div>
        <?php else: ?>
            <div class="card lock-box">
                <div id="requestFormWrapper">
                    <div class="lock-icon">🔒</div>
                    <div class="lock-title">Restricted Profile Access</div>
                    <div class="lock-desc">Please fill out your details below to request profile access.</div>
                    
                    <form id="accessForm">
                        <div class="form-group">
                            <label>HR Representative Name:</label>
                            <input type="text" id="user_name" required placeholder="Enter your full name">
                        </div>
                        <div class="form-group">
                            <label>Company Email:</label>
                            <input type="email" id="user_company" required placeholder="Enter your email address">
                        </div>
                        <button type="submit" id="submitBtn" class="btn-submit">Request Access ➔</button>
                    </form>

                    <button type="button" class="toggle-text-btn" onclick="showLoginForm()">Already have permission? Login here</button>
                </div>

                <div id="loginFormWrapper" style="display: none;">
                    <div class="lock-icon">🔑</div>
                    <div class="lock-title">Login with Permission</div>
                    <div class="lock-desc">Enter your approved Name and Email to view the profile.</div>

                    <form id="loginForm">
                        <div class="form-group">
                            <label>Registered Name:</label>
                            <input type="text" id="login_name" required placeholder="Enter your registered name">
                        </div>
                        <div class="form-group">
                            <label>Registered Email:</label>
                            <input type="email" id="login_email" required placeholder="Enter your registered email">
                        </div>
                        <button type="submit" id="loginBtn" class="btn-submit" style="background:#064e3b; color:#34d399;">Login Now ➔</button>
                    </form>

                    <button type="button" class="back-link-btn" onclick="showRequestForm()">← Back to Request</button>
                </div>

            </div>
        <?php endif; ?>

        <!-- Chat Drawer -->
        <div id="whatsappChatDrawer">
            <div class="wa-chat-header">
                <span>💬 Direct Live Chat Support (Request ID: <?php echo $_SESSION['user_request_id'] ?? 'New'; ?>)</span>
                <span onclick="toggleChatDrawer()" style="cursor: pointer; font-size: 24px; background: #1f2937; color: #fff; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; border-radius: 50%;">&times;</span>
            </div>
            <div class="bot-status-indicator">🟢 Database & Admin Panel Synced</div>
            <div class="wa-chat-body" id="chatMessagesContainer">
                <!-- Dynamically loaded via admin panel / database backend -->
            </div>
            <div class="wa-chat-footer">
                <input type="text" id="waChatInput" class="wa-chat-input" placeholder="Type a message..." onkeypress="handleChatKeyPress(event)">
                <button class="wa-send-btn" onclick="sendChatMessage()">Send</button>
            </div>
        </div>

        <!-- Message Action Modal -->
        <div id="msgActionModal">
            <div class="action-modal-box">
                <input type="hidden" id="selectedMsgId">
                <h4 style="color: #38bdf8; font-size: 14px; margin-bottom: 12px;">Message Options</h4>
                <button class="action-btn" id="editOptionBtn" onclick="triggerEditMode()">Edit Message (Within 1 min)</button>
                <button class="action-btn action-btn-del" onclick="deleteChatMessage()">Delete Message</button>
                <button class="action-btn" style="background:#374151; margin-top:5px;" onclick="closeMsgActions()">Cancel</button>
            </div>
        </div>

        <?php if ($user_status === 'approved'): ?>
        <div class="instagram-nav">
            <button class="nav-item active" onclick="window.scrollTo({top:0, behavior:'smooth'});"><span class="icon">🏠</span>Home</button>
            <button class="nav-item" onclick="toggleChatDrawer();"><span class="icon">💬</span>Chat</button>
            <button class="nav-plus-btn" onclick="alert('Secure action panel.');">+</button>
            <button class="nav-item" onclick="window.location.href='index.php?reset=1';"><span class="icon">🚪</span>Exit</button>
        </div>
        <?php endif; ?>

    <?php elseif ($user_status === 'rejected'): ?>
        <div class="card lock-box" style="border-top-color: #ef4444;">
            <div class="lock-icon">🚫</div>
            <div class="lock-title" style="color: #f87171;">Access Denied</div>
            <div class="info-card" style="border-left-color: #ef4444; background: #450a0a;">
                <span class="info-badge badge-rejected">🔴 Status: Declined</span>
                <div class="info-text">The access request was declined or expired.</div>
            </div>
            <a href="index.php?reset=1" class="reset-link">🔄 Try Again</a>
        </div>
    <?php endif; ?>

</div>

<!-- Document Modal -->
<div id="docModal" onclick="closeDocModal()">
    <div class="modal-content">
        <span class="close-btn" onclick="closeDocModal()">&times;</span>
        <span id="viewTimerBadge" class="view-timer-badge">Closing in 3s...</span>
        <h3 id="modalDocTitle" style="color: var(--navy-primary); margin-bottom: 10px; font-size: 16px;">Document Preview</h3>
        <img id="modalDocImage" src="" alt="Preview" style="max-width:100%; height:auto; border-radius: 6px;">
    </div>
</div>

<script>
    <?php if ($user_status === 'pending'): ?>
    setInterval(function() {
        fetch('index.php?action=check_status')
        .then(res => res.json())
        .then(data => {
            if (data.status === 'approved' || data.status === 'rejected' || data.status === 'none') {
                window.location.reload();
            }
        }).catch(err => {});
    }, 3000);
    <?php endif; ?>

    function toggleEditForm() {
        const section = document.getElementById('profileEditSection');
        section.style.display = section.style.display === 'none' ? 'block' : 'none';
    }

    function showLoginForm() {
        document.getElementById('requestFormWrapper').style.display = 'none';
        document.getElementById('loginFormWrapper').style.display = 'block';
    }

    function showRequestForm() {
        document.getElementById('loginFormWrapper').style.display = 'none';
        document.getElementById('requestFormWrapper').style.display = 'block';
    }

    const updateProfileForm = document.getElementById('updateProfileForm');
    if(updateProfileForm) {
        updateProfileForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = document.getElementById('updateProfileBtn');
            btn.disabled = true;
            btn.innerText = 'Saving...';

            const formData = new FormData();
            formData.append('update_profile_data', '1');
            formData.append('full_name', document.getElementById('edit_full_name').value);
            formData.append('email', document.getElementById('edit_email').value);
            formData.append('phone', document.getElementById('edit_phone').value);
            formData.append('address', document.getElementById('edit_address').value);
            formData.append('career_objective', document.getElementById('edit_career_objective').value);
            formData.append('education', document.getElementById('edit_education').value);
            formData.append('skills', document.getElementById('edit_skills').value);

            fetch('index.php', { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    alert('Profile updated successfully!');
                    window.location.reload();
                } else {
                    alert('Error updating profile: ' + data.error);
                    btn.disabled = false;
                    btn.innerText = 'Save Changes';
                }
            });
        });
    }

    <?php if ($user_status === 'approved' && $expires_at_timestamp > 0): ?>
    let targetTime = <?php echo $expires_at_timestamp; ?>;
    
    function updateCountdown() {
        let now = new Date().getTime();
        let distance = targetTime - now;

        if (distance < 0) {
            document.getElementById("countdownTimer").innerText = "EXPIRED";
            fetch('index.php?action=expire_session').then(() => {
                window.location.reload();
            });
            return;
        }

        let days = Math.floor(distance / (1000 * 60 * 60 * 24));
        let hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        let minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        let seconds = Math.floor((distance % (1000 * 60)) / 1000);

        let timeString = "";
        if (days > 0) {
            timeString = days + "d " + (hours < 10 ? "0" + hours : hours) + ":" + (minutes < 10 ? "0" + minutes : minutes) + ":" + (seconds < 10 ? "0" + seconds : seconds);
        } else {
            timeString = (hours < 10 ? "0" + hours : hours) + ":" + (minutes < 10 ? "0" + minutes : minutes) + ":" + (seconds < 10 ? "0" + seconds : seconds);
        }

        document.getElementById("countdownTimer").innerText = timeString;
    }
    setInterval(updateCountdown, 1000);
    updateCountdown();
    <?php endif; ?>

    setInterval(() => {
        const now = new Date();
        const clockEl = document.getElementById("realTimeClock");
        if(clockEl) {
            clockEl.innerText = now.toLocaleTimeString();
        }
    }, 1000);

    function toggleChatDrawer() {
        const drawer = document.getElementById('whatsappChatDrawer');
        drawer.style.display = drawer.style.display === 'flex' ? 'none' : 'flex';
        if(drawer.style.display === 'flex') {
            document.getElementById('waChatInput').focus();
            fetchChatMessages();
        }
    }

    // Updated Chat Function connected with admin_chat_backend.php / admin panel database
    function fetchChatMessages() {
        <?php if (isset($_SESSION['user_request_id'])): ?>
        let activeUserId = <?php echo (int)$_SESSION['user_request_id']; ?>;
        
        fetch(`admin_chat_backend.php?action=fetch&user_id=${activeUserId}`)
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                const container = document.getElementById('chatMessagesContainer');
                let html = '';
                if(data.messages.length > 0) {
                    data.messages.forEach(cm => {
                        const isUser = (cm.sender_type === 'user');
                        const bubbleClass = isUser ? 'user' : 'admin';
                        const timeDiff = cm.created_at ? (Math.floor(Date.now() / 1000) - Math.floor(new Date(cm.created_at).getTime() / 1000)) : 0;
                        const canEdit = (isUser && timeDiff <= 60) ? 'true' : 'false';
                        const safeMsg = cm.message.replace(/'/g, "\\'").replace(/"/g, '&quot;');
                        
                        html += `<div class="chat-bubble ${bubbleClass}" ${isUser ? `onclick="openMsgActions(${cm.id}, '${safeMsg}', ${canEdit})"` : ''}>${escapeHtml(cm.message)}</div>`;
                    });
                } else {
                    html = `<div class="chat-bubble admin">Hello! Send your message or query, it will be delivered to the admin panel.</div>`;
                }
                
                const isScrolledToBottom = container.scrollHeight - container.scrollTop <= container.clientHeight + 50;
                container.innerHTML = html;
                if(isScrolledToBottom) {
                    container.scrollTop = container.scrollHeight;
                }
            }
        }).catch(err => {});
        <?php endif; ?>
    }

    function escapeHtml(text) {
        return text.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
    }

    document.addEventListener("DOMContentLoaded", function() {
        <?php if (isset($_SESSION['user_request_id'])): ?>
        fetchChatMessages();
        setInterval(fetchChatMessages, 3000); 
        <?php endif; ?>
    });

    function sendChatMessage() {
        const input = document.getElementById('waChatInput');
        const message = input.value.trim();
        if (!message) return;

        input.value = '';

        const formData = new FormData();
        formData.append('action', 'send_chat_message');
        formData.append('message', message);

        fetch('index.php', { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                fetchChatMessages();
            }
        }).catch(() => { fetchChatMessages(); });
    }

    function handleChatKeyPress(e) {
        if (e.key === 'Enter') {
            sendChatMessage();
        }
    }

    let activeEditMsgText = "";

    function openMsgActions(msgId, msgText, canEdit) {
        document.getElementById('selectedMsgId').value = msgId;
        activeEditMsgText = msgText;
        
        const editBtn = document.getElementById('editOptionBtn');
        if(canEdit) {
            editBtn.style.display = 'block';
        } else {
            editBtn.style.display = 'none';
        }
        document.getElementById('msgActionModal').style.display = 'flex';
    }

    function closeMsgActions() {
        document.getElementById('msgActionModal').style.display = 'none';
    }

    function triggerEditMode() {
        let msgId = document.getElementById('selectedMsgId').value;
        closeMsgActions();

        let newText = prompt("Edit message:", activeEditMsgText);
        if(newText !== null && newText.trim() !== "") {
            const formData = new FormData();
            formData.append('action', 'edit_chat_msg');
            formData.append('msg_id', msgId);
            formData.append('new_text', newText.trim());

            fetch('index.php', { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    fetchChatMessages();
                } else if(data.error === 'expired') {
                    alert("1 minute limit has expired.");
                } else {
                    alert("Error editing message.");
                }
            });
        }
    }

    function deleteChatMessage() {
        let msgId = document.getElementById('selectedMsgId').value;
        closeMsgActions();

        if(confirm("Are you sure you want to delete this message?")) {
            const formData = new FormData();
            formData.append('action', 'delete_chat_msg');
            formData.append('msg_id', msgId);

            fetch('index.php', { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => {
                if(data.success) { fetchChatMessages(); }
            });
        }
    }

    let docCloseTimeout;
    function openDocModal(filePath, docName) {
        document.getElementById('modalDocTitle').innerText = docName;
        document.getElementById('modalDocImage').src = filePath;
        document.getElementById('docModal').style.display = 'flex';
        clearTimeout(docCloseTimeout);
        docCloseTimeout = setTimeout(() => { closeDocModal(); }, 3000);
    }
    function closeDocModal() {
        clearTimeout(docCloseTimeout);
        document.getElementById('docModal').style.display = 'none';
    }

    const loginForm = document.getElementById('loginForm');
    if(loginForm) {
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const name = document.getElementById('login_name').value;
            const email = document.getElementById('login_email').value;
            const btn = document.getElementById('loginBtn');
            btn.disabled = true;
            btn.innerText = 'Verifying...';

            const formData = new FormData();
            formData.append('ajax_login', '1');
            formData.append('login_name', name);
            formData.append('login_email', email);

            fetch('index.php', { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    window.location.reload();
                } else {
                    alert(data.error);
                    btn.disabled = false;
                    btn.innerText = 'Login Now ➔';
                }
            });
        });
    }

    const accessForm = document.getElementById('accessForm');
    if(accessForm) {
        accessForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const name = document.getElementById('user_name_input') || document.getElementById('user_name').value;
            const company = document.getElementById('user_company').value;
            
            const btn = document.getElementById('submitBtn');
            btn.disabled = true;
            btn.innerText = 'Submitting...';

            const formData = new FormData();
            formData.append('ajax_request', '1');
            formData.append('user_name', name);
            formData.append('user_company', company);

            fetch('index.php', { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    const text = `🔔 *New HR Access Request!*\n\n` +
                                 `🆔 *Request ID:* ${data.request_id}\n` +
                                 `👤 *HR Name:* ${name}\n` +
                                 `🏢 *Email:* ${company}\n` +
                                 `⏰ *Time:* ${data.request_time}\n\n` +
                                 `*Select Access Duration:*`;
                    
                    const payload = {
                        chat_id: data.chat_id,
                        text: text,
                        parse_mode: 'Markdown',
                        reply_markup: {
                            inline_keyboard: [
                                [
                                    { text: "⏱️ 5 Mins", url: `https://jobnetwork.great-site.net/action.php?id=${data.request_id}&status=approved&time=5&unit=MINUTE` },
                                    { text: "⏱️ 10 Mins", url: `https://jobnetwork.great-site.net/action.php?id=${data.request_id}&status=approved&time=10&unit=MINUTE` }
                                ],
                                [
                                    { text: "⏰ 1 Hour", url: `https://jobnetwork.great-site.net/action.php?id=${data.request_id}&status=approved&time=1&unit=HOUR` },
                                    { text: "📅 1 Day", url: `https://jobnetwork.great-site.net/action.php?id=${data.request_id}&status=approved&time=1&unit=DAY` }
                                ],
                                [
                                    { text: "📅 7 Days", url: `https://jobnetwork.great-site.net/action.php?id=${data.request_id}&status=approved&time=7&unit=DAY` },
                                    { text: "📅 1 Month", url: `https://jobnetwork.great-site.net/action.php?id=${data.request_id}&status=approved&time=1&unit=MONTH` }
                                ],
                                [
                                    { text: "❌ Reject", url: `https://jobnetwork.great-site.net/action.php?id=${data.request_id}&status=rejected` }
                                ]
                            ]
                        }
                    };
                    
                    fetch(`https://api.telegram.org/bot${data.bot_token}/sendMessage`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(payload)
                    }).finally(() => { window.location.reload(); });
                } else {
                    alert('Error submitting request form: ' + (data.error || 'Unknown error'));
                    btn.disabled = false;
                    btn.innerText = 'Request Access ➔';
                }
            });
        });
    }
</script>
</body>
</html>
