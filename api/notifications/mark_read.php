<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once '../../config/db.php';

$data = json_decode(file_get_contents("php://input"));

if (!isset($data->notificationId)) {
    http_response_code(400); 
    echo json_encode(["error" => "Missing notificationId"]);
    exit();
}

// Ignore if it's a dynamic notification (won't be in DB)
if (strpos($data->notificationId, 'dynamic_') === 0) {
    echo json_encode(["success" => true, "message" => "Dynamic alert dismissed locally"]);
    exit();
}

try {
    $stmt = $conn->prepare("UPDATE Notification SET IsRead = 1 WHERE NotificationID = :nid");
    $stmt->execute(['nid' => intval($data->notificationId)]);
    
    echo json_encode(["success" => true]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Database error: " . $e->getMessage()]);
}
?>
