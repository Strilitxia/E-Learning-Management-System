<?php
require_once '../../config/db.php';
$response = [];
try {
    $response['total_users'] = $conn->query("SELECT COUNT(*) FROM Users")->fetchColumn();
    $response['active_courses'] = $conn->query("SELECT COUNT(*) FROM Course WHERE Status = 'published'")->fetchColumn();
    $response['enrollments'] = $conn->query("SELECT COUNT(*) FROM Enrollment")->fetchColumn();
    $response['pending_reports'] = $conn->query("SELECT COUNT(*) FROM Moderation_Report WHERE ResolutionStatus = 'pending'")->fetchColumn();
    echo json_encode($response);
} catch (PDOException $e) {
    http_response_code(500); echo json_encode(["error" => $e->getMessage()]);
}
?>