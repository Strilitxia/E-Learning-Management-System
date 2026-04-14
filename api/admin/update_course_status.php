<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') exit();

require_once '../../config/db.php';

$data = json_decode(file_get_contents("php://input"));

if (!isset($data->courseId) || !isset($data->status)) {
     http_response_code(400); 
     echo json_encode(["error" => "Missing courseId or status"]);
     exit();
}

try {
    $stmt = $conn->prepare("UPDATE Course SET Status = :status WHERE CourseID = :id");
    $stmt->execute(['status' => $data->status, 'id' => $data->courseId]);
    
    echo json_encode(["success" => true, "message" => "Course status updated to " . $data->status]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Database error: " . $e->getMessage()]);
}
?>
