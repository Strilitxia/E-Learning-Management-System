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

if (!isset($data->courseId) || !isset($data->title) || !isset($data->type)) {
    http_response_code(400); 
    echo json_encode(["error" => "Missing required fields"]);
    exit();
}

try {
    $stmt = $conn->prepare("INSERT INTO Assessment (CourseID, Title, Type, DueDate, TimeLimit, MaxScore, Status) 
                            VALUES (:courseId, :title, :type, :dueDate, :timeLimit, :maxScore, 'active')");
                            
    $stmt->execute([
        'courseId' => intval($data->courseId),
        'title' => htmlspecialchars($data->title),
        'type' => htmlspecialchars($data->type),
        'dueDate' => isset($data->dueDate) && !empty($data->dueDate) ? $data->dueDate : null,
        'timeLimit' => isset($data->timeLimit) ? intval($data->timeLimit) : 0,
        'maxScore' => isset($data->maxScore) ? intval($data->maxScore) : 100
    ]);
    
    $assessmentId = $conn->lastInsertId();
    echo json_encode(["success" => true, "assessmentId" => $assessmentId, "message" => "Assessment created successfully"]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Database error: " . $e->getMessage()]);
}
?>
