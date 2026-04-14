<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../../config/db.php';

$userId = isset($_GET['userId']) ? intval($_GET['userId']) : 0;

if ($userId === 0) { 
    http_response_code(400); 
    die(json_encode(["error" => "Missing userId"])); 
}

try {
    // 1. Generate deadline notifications (dynamic, if not already physically in DB we could just inject them, 
    // but better to fetch them as well). Let's generate a quick check for due tasks for students.
    
    // For simplicity, we just fetch DB notifications. The system cron/logic would normally insert them.
    // However, since it's a lightweight demo, we can dynamically append "approaching deadline" warnings right in this API!
    
    $notifications = [];

    // Fetch physical notifications
    $stmt = $conn->prepare("SELECT * FROM Notification WHERE UserID = :uid ORDER BY CreatedAt DESC LIMIT 20");
    $stmt->execute(['uid' => $userId]);
    $dbNotifs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Dynamic Check: Pending Assessments that are due within 3 days for students
    $stmtDynamic = $conn->prepare("
        SELECT a.Title, a.DueDate
        FROM Assessment a
        JOIN Enrollment e ON a.CourseID = e.CourseID
        WHERE e.StudentID = :uid 
          AND a.Status = 'active'
          AND a.DueDate IS NOT NULL
          AND a.DueDate <= DATE_ADD(NOW(), INTERVAL 3 DAY)
          AND a.DueDate >= NOW()
          AND a.AssessmentID NOT IN (SELECT AssessmentID FROM Assessment_Submission WHERE StudentID = :uid)
    ");
    $stmtDynamic->execute(['uid' => $userId]);
    $pendingDue = $stmtDynamic->fetchAll(PDO::FETCH_ASSOC);

    foreach($pendingDue as $due) {
        $notifications[] = [
            "NotificationID" => "dynamic_" . uniqid(),
            "Message" => "Reminder: " . $due['Title'] . " is due on " . date('M d, Y', strtotime($due['DueDate'])),
            "CreatedAt" => date('Y-m-d H:i:s'),
            "IsRead" => 0,
            "Type" => "alert" // custom tag for frontend
        ];
    }
    
    // Merge
    $notifications = array_merge($notifications, $dbNotifs);

    echo json_encode(["success" => true, "notifications" => $notifications]);

} catch (PDOException $e) {
    http_response_code(500); 
    echo json_encode(["error" => "Database error: " . $e->getMessage()]);
}
?>
