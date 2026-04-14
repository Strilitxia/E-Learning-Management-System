<?php
require_once '../../config/db.php';
$data = json_decode(file_get_contents("php://input"));

if(isset($data->reportId) && isset($data->userId) && isset($data->action)) {
    try {
        $userStatus = '';
        if ($data->action === 'approve') $userStatus = 'active';
        if ($data->action === 'suspend') $userStatus = 'pending';
        if ($data->action === 'ban') $userStatus = 'banned';

        $conn->beginTransaction(); // Start transaction

        // 1. Update Reported User Status
        $stmt = $conn->prepare("UPDATE Users SET Status = :status WHERE UserID = :id");
        $stmt->execute(['status' => $userStatus, 'id' => $data->userId]);

        // 2. Fetch reporter and mark report as resolved
        $stmtGetReporter = $conn->prepare("SELECT ReportedByUserID, Reason FROM Moderation_Report WHERE ReportID = ?");
        $stmtGetReporter->execute([$data->reportId]);
        $reportData = $stmtGetReporter->fetch(PDO::FETCH_ASSOC);

        $stmt2 = $conn->prepare("UPDATE Moderation_Report SET ResolutionStatus = 'resolved' WHERE ReportID = :rid");
        $stmt2->execute(['rid' => $data->reportId]);

        // 3. Send Notification to Reporter (if course/student report)
        if ($reportData && !empty($reportData['ReportedByUserID'])) {
            $actionWord = $data->action === 'approve' ? 'dismissed (no violation)' : $data->action;
            $msg = "Update on your report: The administrator has reviewed your report and the issue has been " . $actionWord . ".";
            
            $stmtNotif = $conn->prepare("INSERT INTO Notifications (UserID, Message, IsRead, CreatedAt) VALUES (?, ?, 0, NOW())");
            $stmtNotif->execute([$reportData['ReportedByUserID'], $msg]);
        }

        $conn->commit(); // Save changes
        echo json_encode(["success" => true]);
    } catch (PDOException $e) {
        $conn->rollBack();
        http_response_code(500); echo json_encode(["error" => $e->getMessage()]);
    }
}
?>