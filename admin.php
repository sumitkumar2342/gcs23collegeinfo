<?php
include 'db.php';

$msg = $_GET['msg'] ?? '';

// १. एक्सेस रिक्वेस्ट को अप्रूव करना
if (isset($_GET['approve_id'])) {
    $app_id = intval($_GET['approve_id']);
    $conn->query("UPDATE access_requests SET status='approved' WHERE id=$app_id");
    header("Location: admin.php?msg=" . urlencode("यूजर को एक्सेस दे दिया गया है!"));
    exit();
}

// कस्टम टाइम लिमिट सेट करने के लिए (Calendar/Date-Time Update)
if (isset($_POST['update_expiry'])) {
    $req_id = intval($_POST['req_id']);
    $new_expiry = mysqli_real_escape_string($conn, $_POST['new_expiry']);
    
    if (!empty($new_expiry)) {
        $conn->query("UPDATE access_requests SET status='approved', expires_at='$new_expiry' WHERE id=$req_id");
        header("Location: admin.php?msg=" . urlencode("एक्सेस टाइम लिमिट सफलतापूर्वक अपडेट कर दी गई है!"));
        exit();
    }
}

// २. एडमिन द्वारा यूजर को रिप्लाई भेजने का कोड (Admin Reply Handler)
if (isset($_POST['send_admin_reply'])) {
    $req_id = intval($_POST['req_id']);
    $admin_reply = mysqli_real_escape_string($conn, $_POST['admin_reply']);
    
    if (!empty($admin_reply)) {
        // डेटाबेस में एडमिन का रिप्लाई सेव करने के लिए (सुनिश्चित करें कि टेबल में admin_reply कॉलम मौजूद हो)
        $conn->query("UPDATE access_requests SET admin_reply='$admin_reply' WHERE id=$req_id");
        
        // यदि आप चाहें तो यहाँ Telegram API के ज़रिए भी यूजर को मैसेज भेज सकते हैं।
        header("Location: admin.php?msg=" . urlencode("रिप्लाई सफलतापूर्वक भेज दिया गया है!"));
        exit();
    }
}

// ३. प्रोफाइल डेटा अपडेट या इंसर्ट करना
if (isset($_POST['save_profile'])) {
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $career_objective = mysqli_real_escape_string($conn, $_POST['career_objective']);
    $education = mysqli_real_escape_string($conn, $_POST['education']);
    $skills = mysqli_real_escape_string($conn, $_POST['skills']);
    $certifications = mysqli_real_escape_string($conn, $_POST['certifications']);
    $internships = mysqli_real_escape_string($conn, $_POST['internships']);
    $languages = mysqli_real_escape_string($conn, $_POST['languages']);
    $hobbies = mysqli_real_escape_string($conn, $_POST['hobbies']);

    $profile_pic = $_POST['old_profile_pic'] ?? '';
    if (isset($_FILES["profile_pic"]["name"]) && $_FILES["profile_pic"]["name"] != "") {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) { mkdir($target_dir, 0777, true); }
        $target_file = $target_dir . time() . "_" . basename($_FILES["profile_pic"]["name"]);
        if (move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $target_file)) {
            $profile_pic = $target_file;
        }
    }

    $check = $conn->query("SELECT id FROM profile_data LIMIT 1");
    if ($check && $check->num_rows > 0) {
        $row = $check->fetch_assoc();
        $id = $row['id'];
        $sql = "UPDATE profile_data SET full_name='$full_name', email='$email', phone='$phone', address='$address', 
                career_objective='$career_objective', profile_pic='$profile_pic', education='$education', skills='$skills', 
                certifications='$certifications', internships='$internships', languages='$languages', hobbies='$hobbies' WHERE id=$id";
    } else {
        $sql = "INSERT INTO profile_data (full_name, email, phone, address, career_objective, profile_pic, education, skills, certifications, internships, languages, hobbies) 
                VALUES ('$full_name', '$email', '$phone', '$address', '$career_objective', '$profile_pic', '$education', '$skills', '$certifications', '$internships', '$languages', '$hobbies')";
    }
    
    if($conn->query($sql)) {
        header("Location: admin.php?msg=" . urlencode("प्रोफाइल सफलतापूर्वक अपडेट हो गई है!"));
        exit();
    }
}

// ४. दस्तावेज़ (Document) अपलोड करना
if (isset($_POST['upload_doc'])) {
    $doc_name = mysqli_real_escape_string($conn, $_POST['doc_name']);
    if (isset($_FILES["doc_file"]["name"]) && $_FILES["doc_file"]["name"] != "") {
        $target_dir = "uploads/docs/";
        if (!is_dir($target_dir)) { mkdir($target_dir, 0777, true); }
        $target_file = $target_dir . time() . "_" . basename($_FILES["doc_file"]["name"]);
        if (move_uploaded_file($_FILES["doc_file"]["tmp_name"], $target_file)) {
            $conn->query("INSERT INTO documents (doc_name, file_path) VALUES ('$doc_name', '$target_file')");
            header("Location: admin.php?msg=" . urlencode("डॉक्यूमेंट सफलतापूर्वक अपलोड हो गया!"));
            exit();
        }
    }
}

// ५. दस्तावेज़ डिलीट करना
if (isset($_GET['delete_doc'])) {
    $doc_id = intval($_GET['delete_doc']);
    $res = $conn->query("SELECT file_path FROM documents WHERE id=$doc_id");
    if ($row = $res->fetch_assoc()) {
        if (file_exists($row['file_path'])) { unlink($row['file_path']); }
        $conn->query("DELETE FROM documents WHERE id=$doc_id");
    }
    header("Location: admin.php?msg=" . urlencode("डॉक्यूमेंट हटा दिया गया है!"));
    exit();
}

$profile_query = $conn->query("SELECT * FROM profile_data LIMIT 1");
$prof = $profile_query ? $profile_query->fetch_assoc() : null;

// Stats for Dashboard Summary
$total_reqs = $conn->query("SELECT COUNT(*) as cnt FROM access_requests")->fetch_assoc()['cnt'];
$pending_reqs = $conn->query("SELECT COUNT(*) as cnt FROM access_requests WHERE status='pending'")->fetch_assoc()['cnt'];
$total_docs = $conn->query("SELECT COUNT(*) as cnt FROM documents")->fetch_assoc()['cnt'];
?>

<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Secure Control Center</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-navy: #0b3b60;
            --secondary-blue: #135280;
            --accent-orange: #e65100;
            --bg-matte: #000000;
            --card-soft: #111827;
            --text-dark: #f3f4f6;
            --border-soft: #374151;
            --shadow-3d: 0 10px 25px rgba(0, 0, 0, 0.8);
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; background-color: var(--bg-matte); color: var(--text-dark); padding-bottom: 60px; }

        .header-top-bar { height: 6px; background: linear-gradient(to right, #ff9933 33%, #ffffff 33%, #ffffff 66%, #138808 66%); }
        .admin-header { background-color: var(--card-soft); color: #ffffff; padding: 14px 20px; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid var(--border-soft); }
        .admin-header h1 { font-size: 18px; font-weight: 700; text-transform: uppercase; color: #38bdf8; }
        
        .header-right-actions { display: flex; align-items: center; gap: 12px; }
        .btn-preview { background-color: var(--secondary-blue); color: #fff; padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 12.5px; font-weight: 600; }
        .btn-preview:hover { background-color: #0ea5e9; color: #000; }

        .hamburger-btn { background: #1f2937; border: 1px solid var(--border-soft); color: #38bdf8; padding: 6px 10px; border-radius: 6px; font-size: 18px; cursor: pointer; display: flex; align-items: center; justify-content: center; }
        .hamburger-btn:hover { background: #0ea5e9; color: #000; }

        #sidebarOverlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); z-index: 9998; backdrop-filter: blur(4px); }
        
        .sidebar-drawer { position: fixed; top: 0; left: -280px; width: 280px; height: 100%; background: #111827; border-right: 1px solid var(--border-soft); z-index: 9999; transition: 0.3s ease; padding: 20px 15px; display: flex; flex-direction: column; box-shadow: 5px 0 25px rgba(0,0,0,0.9); }
        .sidebar-drawer.open { left: 0; }

        .sidebar-header { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-soft); padding-bottom: 12px; margin-bottom: 15px; }
        .sidebar-title { font-size: 15px; font-weight: 700; color: #38bdf8; text-transform: uppercase; }
        .close-sidebar-btn { background: none; border: none; color: #f87171; font-size: 24px; cursor: pointer; font-weight: bold; line-height: 1; }
        .close-sidebar-btn:hover { color: #ffffff; }

        .sidebar-nav-list { display: flex; flex-direction: column; gap: 8px; overflow-y: auto; }
        .nav-tab { background: #1f2937; color: #9ca3af; border: 1px solid var(--border-soft); padding: 11px 14px; border-radius: 6px; font-size: 13.5px; font-weight: 600; cursor: pointer; text-align: left; transition: 0.2s; width: 100%; }
        .nav-tab:hover, .nav-tab.active { background: #0ea5e9; color: #000; border-color: #0ea5e9; }

        .dashboard-container { max-width: 1100px; margin: 25px auto; padding: 0 15px; }
        .alert-msg { background: rgba(5, 150, 105, 0.2); color: #34d399; border: 1px solid #059669; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; font-weight: 600; }

        .admin-section { display: none; }
        .admin-section.active-section { display: block; }

        .admin-card { background: var(--card-soft); border-radius: 12px; border: 1px solid var(--border-soft); border-top: 4px solid #0ea5e9; padding: 22px; box-shadow: var(--shadow-3d); margin-bottom: 24px; }
        .card-title { font-size: 16px; font-weight: 700; color: #38bdf8; text-transform: uppercase; border-bottom: 2px solid var(--border-soft); padding-bottom: 8px; margin-bottom: 16px; }

        .stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; margin-bottom: 25px; }
        .stat-box { background: #1f2937; border: 1px solid var(--border-soft); padding: 18px; border-radius: 8px; text-align: center; }
        .stat-num { font-size: 24px; font-weight: 700; color: #38bdf8; margin-top: 5px; }

        .form-group { margin-bottom: 14px; }
        label { display: block; font-size: 13px; font-weight: 600; color: #38bdf8; margin-bottom: 5px; }
        input[type="text"], input[type="email"], input[type="datetime-local"], textarea, input[type="file"] { width: 100%; padding: 10px 12px; border: 1px solid var(--border-soft); border-radius: 6px; font-size: 13.5px; font-family: inherit; background: #1f2937; color: #ffffff; }
        input:focus, textarea:focus { outline: none; border-color: #0ea5e9; box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.2); }
        textarea { resize: vertical; min-height: 70px; }

        .btn-primary { background-color: #0ea5e9; color: #000; border: none; padding: 10px 20px; border-radius: 6px; font-size: 14px; font-weight: 600; cursor: pointer; width: 100%; transition: background 0.2s; }
        .btn-primary:hover { background-color: #38bdf8; }

        .doc-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .doc-table th { background-color: #1f2937; color: #38bdf8; text-align: left; padding: 10px; font-size: 12px; font-weight: 700; }
        .doc-table td { padding: 10px; border-bottom: 1px solid var(--border-soft); font-size: 13px; color: #d1d5db; }
        .btn-del { color: #f87171; text-decoration: none; font-weight: 600; font-size: 12px; }
        .btn-del:hover { text-decoration: underline; }
        .img-preview { width: 60px; height: 70px; object-fit: cover; border-radius: 4px; border: 1px solid var(--border-soft); margin-top: 5px; }

        .status-pill { display: inline-block; padding: 2px 8px; border-radius: 12px; font-size: 11px; font-weight: 700; }
        .pill-active { background: #064e3b; color: #34d399; }
        .pill-offline { background: #450a0a; color: #f87171; }

        /* Chat Logs & Reply UI Styling */
        .chat-log-box { background: #1f2937; border: 1px solid var(--border-soft); border-radius: 8px; padding: 16px; margin-bottom: 18px; }
        .chat-meta { display: flex; justify-content: space-between; font-size: 12.5px; color: #9ca3af; margin-bottom: 10px; border-bottom: 1px dashed var(--border-soft); padding-bottom: 8px; }
        .user-message-bubble { background: #111827; border-left: 3px solid #38bdf8; padding: 10px 12px; border-radius: 4px; font-size: 13.5px; color: #e5e7eb; margin-bottom: 12px; }
        .admin-reply-box { margin-top: 10px; }
        .admin-reply-display { background: rgba(14, 165, 233, 0.1); border: 1px solid #0ea5e9; padding: 8px 12px; border-radius: 6px; font-size: 13px; color: #38bdf8; margin-bottom: 10px; }

        #expiryModal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.85); justify-content: center; align-items: center; z-index: 10000; backdrop-filter: blur(10px); }
        .modal-box { background: #111827; border: 1px solid var(--border-soft); border-top: 4px solid #0ea5e9; padding: 25px; border-radius: 10px; width: 90%; max-width: 400px; color: #fff; position: relative; }
        .close-modal { position: absolute; top: 12px; right: 15px; font-size: 20px; cursor: pointer; color: #9ca3af; }
        .close-modal:hover { color: #fff; }

        @media (max-width: 850px) { .stats-grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>

<div class="header-top-bar"></div>

<div class="admin-header">
    <div style="display: flex; align-items: center; gap: 12px;">
        <button class="hamburger-btn" onclick="toggleSidebar()">☰</button>
        <h1>Control Center</h1>
    </div>
    <div class="header-right-actions">
        <a href="index.php" target="_blank" class="btn-preview">View Website ↗</a>
    </div>
</div>

<div id="sidebarOverlay" onclick="toggleSidebar()"></div>
<div id="sidebarDrawer" class="sidebar-drawer">
    <div class="sidebar-header">
        <span class="sidebar-title">Admin Menu</span>
        <button class="close-sidebar-btn" onclick="toggleSidebar()">✕</button>
    </div>
    <div class="sidebar-nav-list">
        <button class="nav-tab active" onclick="switchSection('dashboard-sec', this)">📊 Dashboard Summary</button>
        <button class="nav-tab" onclick="switchSection('profile-sec', this)">👤 Edit Profile & Resume</button>
        <button class="nav-tab" onclick="switchSection('requests-sec', this)">🔒 Access Verification & Requests</button>
        <button class="nav-tab" onclick="switchSection('documents-sec', this)">📁 Documents Management</button>
        <button class="nav-tab" onclick="switchSection('chatlogs-sec', this)">💬 Telegram & Live Chat Logs</button>
    </div>
</div>

<div class="dashboard-container">

    <?php if($msg): ?>
        <div class="alert-msg">✓ <?php echo htmlspecialchars($msg); ?></div>
    <?php endif; ?>

    <!-- SECTION 1: Dashboard Summary -->
    <div id="dashboard-sec" class="admin-section active-section">
        <div class="stats-grid">
            <div class="stat-box">
                <div>Total Access Requests</div>
                <div class="stat-num"><?php echo $total_reqs; ?></div>
            </div>
            <div class="stat-box">
                <div>Pending Approvals</div>
                <div class="stat-num" style="color: #fbbf24;"><?php echo $pending_reqs; ?></div>
            </div>
            <div class="stat-box">
                <div>Uploaded Documents</div>
                <div class="stat-num"><?php echo $total_docs; ?></div>
            </div>
        </div>
        <div class="admin-card">
            <div class="card-title">Welcome to Secure Admin Dashboard</div>
            <p style="color: #9ca3af; font-size: 14px; line-height: 1.6;">
                Upar left corner mein diye gaye three-line (☰) menu button par click karke aap alag-alag sections mein navigate kar sakte hain.
            </p>
        </div>
    </div>

    <!-- SECTION 2: Edit Profile & Resume -->
    <div id="profile-sec" class="admin-section">
        <div class="admin-card">
            <div class="card-title">प्रोफाइल डिटेल्स एवं रिज्यूमे अपडेट करें</div>
            <form action="" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="old_profile_pic" value="<?php echo htmlspecialchars($prof['profile_pic'] ?? ''); ?>">

                <div class="form-group">
                    <label>पूरा नाम:</label>
                    <input type="text" name="full_name" value="<?php echo htmlspecialchars($prof['full_name'] ?? ''); ?>" required>
                </div>

                <div class="form-group">
                    <label>प्रोफाइल फोटो:</label>
                    <input type="file" name="profile_pic">
                    <?php if(!empty($prof['profile_pic'])): ?>
                        <img src="<?php echo htmlspecialchars($prof['profile_pic']); ?>" class="img-preview" alt="Photo">
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label>ईमेल:</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($prof['email'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label>फोन नंबर:</label>
                    <input type="text" name="phone" value="<?php echo htmlspecialchars($prof['phone'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label>पता (Address):</label>
                    <textarea name="address"><?php echo htmlspecialchars($prof['address'] ?? ''); ?></textarea>
                </div>

                <div class="form-group">
                    <label>Profile Objective:</label>
                    <textarea name="career_objective"><?php echo htmlspecialchars($prof['career_objective'] ?? ''); ?></textarea>
                </div>

                <div class="form-group">
                    <label>Educational Qualifications:</label>
                    <textarea name="education"><?php echo htmlspecialchars($prof['education'] ?? ''); ?></textarea>
                </div>

                <div class="form-group">
                    <label>Practical Experience & Internships:</label>
                    <textarea name="internships"><?php echo htmlspecialchars($prof['internships'] ?? ''); ?></textarea>
                </div>

                <div class="form-group">
                    <label>Key Technical & Soft Skills:</label>
                    <textarea name="skills"><?php echo htmlspecialchars($prof['skills'] ?? ''); ?></textarea>
                </div>

                <div class="form-group">
                    <label>Certifications:</label>
                    <textarea name="certifications"><?php echo htmlspecialchars($prof['certifications'] ?? ''); ?></textarea>
                </div>

                <div class="form-group">
                    <label>Languages Known:</label>
                    <input type="text" name="languages" value="<?php echo htmlspecialchars($prof['languages'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label>Interests / Hobbies:</label>
                    <textarea name="hobbies"><?php echo htmlspecialchars($prof['hobbies'] ?? ''); ?></textarea>
                </div>

                <button type="submit" name="save_profile" class="btn-primary">प्रोफाइल अपडेट करें</button>
            </form>
        </div>
    </div>

    <!-- SECTION 3: Access Verification & Requests -->
    <div id="requests-sec" class="admin-section">
        <div class="admin-card">
            <div class="card-title">वेबसाइट एक्सेस रिक्वेस्ट एवं वेरिफिकेशन</div>
            <table class="doc-table">
                <thead>
                    <tr>
                        <th>यूजर / एचआर नाम</th>
                        <th>ईमेल / कंपनी</th>
                        <th>वेरिफिकेशन स्टेटस</th>
                        <th>एक्सेस लिमिट (Expiry Limit)</th>
                        <th>एक्शन / अपडेट</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $reqs = $conn->query("SELECT * FROM access_requests ORDER BY id DESC");
                    if($reqs && $reqs->num_rows > 0):
                        while($r = $reqs->fetch_assoc()):
                    ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($r['user_name']); ?></strong></td>
                        <td><?php echo htmlspecialchars($r['user_email']); ?></td>
                        <td>
                            <?php if($r['status'] == 'approved'): ?>
                                <span class="status-pill pill-active">Approved</span>
                            <?php else: ?>
                                <span class="status-pill" style="background:#451a03; color:#fbbf24;">Pending</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if(!empty($r['expires_at'])): ?>
                                <span style="font-size: 12px; color: #38bdf8; font-weight: 600;"><?php echo date('d M Y, h:i A', strtotime($r['expires_at'])); ?></span>
                            <?php else: ?>
                                <span style="font-size: 12px; color: #9ca3af;">Not Set / Expired</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if($r['status'] == 'pending'): ?>
                                <a href="admin.php?approve_id=<?php echo $r['id']; ?>" style="color: #34d399; font-weight: bold; margin-right: 8px;">Approve</a>
                            <?php endif; ?>
                            <button onclick="openExpiryModal(<?php echo $r['id']; ?>, '<?php echo $r['expires_at'] ?? ''; ?>')" style="background: #1f2937; border: 1px solid #374151; color: #38bdf8; padding: 4px 10px; font-size: 12px; border-radius: 4px; cursor: pointer;">📅 Update Limit</button>
                        </td>
                    </tr>
                    <?php 
                        endwhile;
                    else:
                    ?>
                    <tr>
                        <td colspan="5" style="text-align: center; color: #9ca3af;">कोई नया एक्सेस रिक्वेस्ट नहीं है।</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- SECTION 4: Documents Management -->
    <div id="documents-sec" class="admin-section">
        <div class="admin-card">
            <div class="card-title">नया डॉक्यूमेंट अपलोड करें</div>
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label>डॉक्यूमेंट का नाम (उदा. 10th Certificate):</label>
                    <input type="text" name="doc_name" placeholder="उदा. Marksheet / Certificate" required>
                </div>

                <div class="form-group">
                    <label>फाइल चुनें (PDF/Image):</label>
                    <input type="file" name="doc_file" required>
                </div>

                <button type="submit" name="upload_doc" class="btn-primary">डॉक्यूमेंट जोड़ें</button>
            </form>
        </div>

        <div class="admin-card">
            <div class="card-title">अपलोड किए गए डॉक्यूमेंट्स की सूची</div>
            <table class="doc-table">
                <thead>
                    <tr>
                        <th>डॉक्यूमेंट नाम</th>
                        <th>फ़ाइल</th>
                        <th>एक्शन</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $docs = $conn->query("SELECT * FROM documents ORDER BY id DESC");
                    if($docs && $docs->num_rows > 0):
                        while($d = $docs->fetch_assoc()):
                    ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($d['doc_name']); ?></strong></td>
                        <td><a href="<?php echo htmlspecialchars($d['file_path']); ?>" target="_blank" style="color: #38bdf8;">View File</a></td>
                        <td><a href="admin.php?delete_doc=<?php echo $d['id']; ?>" class="btn-del" onclick="return confirm('क्या आप इस फाइल को डिलीट करना चाहते हैं?')">Delete</a></td>
                    </tr>
                    <?php 
                        endwhile;
                    else:
                    ?>
                    <tr>
                        <td colspan="3" style="text-align: center; color: #9ca3af;">कोई डॉक्यूमेंट नहीं मिला।</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- SECTION 5: Telegram & Live Chat Logs (With Reply Option) -->
    <div id="chatlogs-sec" class="admin-section">
        <div class="admin-card">
            <div class="card-title">💬 Telegram & User Communication Logs</div>
            <p style="color: #9ca3af; font-size: 13.5px; margin-bottom: 20px;">
                Yahan sabhi registered users ki list, unke messages aur active status dikh rahe hain. Aap niche diye gaye box se seedhe reply bhej sakte hain:
            </p>

            <?php
            $chats = $conn->query("SELECT * FROM access_requests ORDER BY id DESC");
            if($chats && $chats->num_rows > 0):
                while($c = $chats->fetch_assoc()):
            ?>
            <div class="chat-log-box">
                <div class="chat-meta">
                    <span>🆔 ID: <strong><?php echo $c['id']; ?></strong> | 👤 Name: <strong><?php echo htmlspecialchars($c['user_name']); ?></strong> (<?php echo htmlspecialchars($c['user_email']); ?>)</span>
                    <span>
                        Status: 
                        <?php if($c['status'] == 'approved'): ?>
                            <span class="status-pill pill-active">Active Session</span>
                        <?php else: ?>
                            <span class="status-pill pill-offline">Pending / Inactive</span>
                        <?php endif; ?>
                    </span>
                </div>

                <!-- Samne Se Aaya Hua Message (User Message) -->
                <div style="font-size: 12px; color: #9ca3af; margin-bottom: 3px;">📥 User Message / Inquiry:</div>
                <div class="user-message-bubble">
                    <?php echo !empty($c['user_message']) ? htmlspecialchars($c['user_message']) : 'No message provided (Direct Access Request).'; ?>
                </div>

                <!-- Agar Admin Ne Pehle Reply Kiya Ho -->
                <?php if(!empty($c['admin_reply'])): ?>
                    <div style="font-size: 12px; color: #38bdf8; margin-bottom: 3px;">📤 Your Previous Reply:</div>
                    <div class="admin-reply-display">
                        <?php echo htmlspecialchars($c['admin_reply']); ?>
                    </div>
                <?php endif; ?>

                <!-- Admin Reply Form Box -->
                <form action="" method="POST" class="admin-reply-box">
                    <input type="hidden" name="req_id" value="<?php echo $c['id']; ?>">
                    <div style="display: flex; gap: 10px;">
                        <input type="text" name="admin_reply" placeholder="Type your reply message here..." required style="flex: 1; padding: 8px 10px; font-size: 13px;">
                        <button type="submit" name="send_admin_reply" style="background: #0ea5e9; color: #000; border: none; padding: 8px 16px; border-radius: 6px; font-weight: 600; font-size: 13px; cursor: pointer;">Send Reply</button>
                    </div>
                </form>
            </div>
            <?php 
                endwhile;
            else:
            ?>
            <div style="text-align: center; color: #9ca3af; padding: 20px;">कोई चैट या यूजर कम्युनिकेशन लॉग उपलब्ध नहीं है।</div>
            <?php endif; ?>
        </div>
    </div>

</div>

<!-- Expiry Limit Calendar Modal -->
<div id="expiryModal">
    <div class="modal-box">
        <span class="close-modal" onclick="closeExpiryModal()">&times;</span>
        <h3 style="color: #38bdf8; font-size: 16px; margin-bottom: 15px;">Set Access Time Limit</h3>
        <form action="" method="POST">
            <input type="hidden" name="req_id" id="modalReqId">
            <div class="form-group">
                <label>Select Expiry Date & Time:</label>
                <input type="datetime-local" name="new_expiry" id="modalExpiryInput" required>
            </div>
            <button type="submit" name="update_expiry" class="btn-primary" style="margin-top: 10px;">Update Limit & Approve</button>
        </form>
    </div>
</div>

<script>
    function toggleSidebar() {
        const drawer = document.getElementById('sidebarDrawer');
        const overlay = document.getElementById('sidebarOverlay');
        if(drawer.classList.contains('open')) {
            drawer.classList.remove('open');
            overlay.style.display = 'none';
        } else {
            drawer.classList.add('open');
            overlay.style.display = 'block';
        }
    }

    function switchSection(sectionId, tabElement) {
        const sections = document.querySelectorAll('.admin-section');
        sections.forEach(sec => sec.classList.remove('active-section'));

        const tabs = document.querySelectorAll('.nav-tab');
        tabs.forEach(tab => tab.classList.remove('active'));

        document.getElementById(sectionId).classList.add('active-section');
        tabElement.classList.add('active');

        toggleSidebar();
    }

    function openExpiryModal(reqId, currentExpiry) {
        document.getElementById('modalReqId').value = reqId;
        if(currentExpiry) {
            let formatted = currentExpiry.replace(' ', 'T');
            document.getElementById('modalExpiryInput').value = formatted;
        } else {
            let now = new Date();
            now.setHours(now.getHours() + 1);
            document.getElementById('modalExpiryInput').value = now.toISOString().slice(0, 16);
        }
        document.getElementById('expiryModal').style.display = 'flex';
    }

    function closeExpiryModal() {
        document.getElementById('expiryModal').style.display = 'none';
    }
</script>

</body>
</html>
