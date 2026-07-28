<?php
include 'db.php';

if (isset($_GET['id']) && isset($_GET['status'])) {
    $id = (int)$_GET['id'];
    $status = $_GET['status']; // 'approved' ya 'rejected'
    $timeValue = isset($_GET['time']) ? (int)$_GET['time'] : 1; // Default 1 minute
    $timeUnit = isset($_GET['unit']) ? $_GET['unit'] : 'MINUTE'; // MINUTE, HOUR, DAY, WEEK

    if ($status === 'approved') {
        // SQL query to add dynamic time interval (MINUTE, HOUR, DAY, WEEK)
        $stmt = $conn->prepare("UPDATE access_requests SET status = 'approved', expires_at = DATE_ADD(NOW(), INTERVAL ? {$timeUnit}) WHERE id = ?");
        $stmt->bind_param("ii", $timeValue, $id);
        $stmt->execute();
        
        echo "<h2 style='color:green; text-align:center; margin-top:50px;'>Request Approved Successfully for {$timeValue} {$timeUnit}(s)! You can close this window.</h2>";
    } elseif ($status === 'rejected') {
        $conn->query("UPDATE access_requests SET status = 'rejected' WHERE id = $id");
        echo "<h2 style='color:red; text-align:center; margin-top:50px;'>Request Rejected Successfully!</h2>";
    }
}
?>
