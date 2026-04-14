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

if (!isset($data->studentId) || !isset($data->courseId) || !isset($data->progressPercentage)) {
    http_response_code(400); 
    echo json_encode(["error" => "Missing required fields (studentId, courseId, progressPercentage)"]);
    exit();
}

try {
    $progress = floatval($data->progressPercentage);
    $isCompleted = ($progress >= 100.0) ? 1 : 0;

    $stmt = $conn->prepare("
        UPDATE Enrollment 
        SET ProgressPercentage = :progress, IsCompleted = :completed 
        WHERE StudentID = :studentId AND CourseID = :courseId
    ");
    $stmt->execute([
        'progress' => $progress > 100.0 ? 100.0 : $progress,
        'completed' => $isCompleted,
        'studentId' => intval($data->studentId),
        'courseId' => intval($data->courseId)
    ]);

    if ($stmt->rowCount() > 0 || $stmt->errorInfo()[0] == '00000') {
        echo json_encode(["success" => true, "message" => "Progress updated successfully"]);
    } else {
        echo json_encode(["success" => false, "error" => "Failed to update enrollment record. Make sure user is enrolled."]);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Database error: " . $e->getMessage()]);
}
?>
