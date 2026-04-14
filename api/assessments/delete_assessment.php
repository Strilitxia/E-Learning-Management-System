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

if (!isset($data->assessmentId)) {
    http_response_code(400); 
    echo json_encode(["error" => "Missing required field: assessmentId"]);
    exit();
}

try {
    $stmt = $conn->prepare("DELETE FROM Assessment WHERE AssessmentID = :assessmentId");
    $stmt->execute([
        'assessmentId' => intval($data->assessmentId)
    ]);
    
    if ($stmt->rowCount() > 0) {
        echo json_encode(["success" => true, "message" => "Assessment deleted successfully"]);
    } else {
        echo json_encode(["success" => false, "error" => "Assessment not found"]);
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Database error: " . $e->getMessage()]);
}
?>
