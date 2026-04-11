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

        // Update User Status
        $stmt = $conn->prepare("UPDATE Users SET Status = :status WHERE UserID = :id");
        $stmt->execute(['status' => $userStatus, 'id' => $data->userId]);

        // Mark report as resolved
        $stmt2 = $conn->prepare("UPDATE Moderation_Report SET ResolutionStatus = 'resolved' WHERE ReportID = :rid");
        $stmt2->execute(['rid' => $data->reportId]);

        $conn->commit(); // Save changes
        echo json_encode(["success" => true]);
    } catch (PDOException $e) {
        $conn->rollBack();
        http_response_code(500); echo json_encode(["error" => $e->getMessage()]);
    }
}
?>