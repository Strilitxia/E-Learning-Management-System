<?php
require_once '../../config/db.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"));

if (!isset($data->reportedUserId) || !isset($data->reportedByUserId) || !isset($data->reason)) {
    http_response_code(400);
    echo json_encode(["error" => "Missing required fields (reportedUserId, reportedByUserId, reason)"]);
    exit();
}

try {
    $stmt = $conn->prepare("
        INSERT INTO Moderation_Report (ReportedUserID, ReportedByUserID, Reason, ReportDate, ResolutionStatus)
        VALUES (?, ?, ?, NOW(), 'pending')
    ");
    
    $success = $stmt->execute([
        intval($data->reportedUserId),
        intval($data->reportedByUserId),
        htmlspecialchars($data->reason)
    ]);

    if ($success) {
        echo json_encode(["success" => true, "message" => "Report submitted successfully. Admin will review it."]);
    } else {
        echo json_encode(["success" => false, "error" => "Failed to submit report."]);
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}
?>
