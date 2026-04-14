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

if (!isset($data->instructorId) || !isset($data->title) || !isset($data->category) || !isset($data->level)) {
    http_response_code(400); 
    echo json_encode(["error" => "Missing required fields (instructorId, title, category, level)"]);
    exit();
}

try {
    // 1. Validate Instructor existence and role
    $stmtInst = $conn->prepare("SELECT Role FROM Users WHERE UserID = :instructorId");
    $stmtInst->execute(['instructorId' => intval($data->instructorId)]);
    $instructor = $stmtInst->fetch(PDO::FETCH_ASSOC);

    if (!$instructor) {
        echo json_encode(["success" => false, "error" => "Instructor ID does not exist"]);
        exit();
    }
    
    if ($instructor['Role'] !== 'instructor') {
        echo json_encode(["success" => false, "error" => "Target user is a '{$instructor['Role']}', not an instructor"]);
        exit();
    }

    // 2. Perform Course Creation
    $stmt = $conn->prepare("INSERT INTO Course (InstructorID, Title, Category, Level, Duration, ThumbnailURL, Status, Rating) 
                            VALUES (:instructorId, :title, :category, :level, :duration, :thumbnailUrl, :status, 0.0)");
                            
    $stmt->execute([
        'instructorId' => intval($data->instructorId),
        'title' => htmlspecialchars($data->title),
        'category' => htmlspecialchars($data->category),
        'level' => htmlspecialchars($data->level),
        'duration' => isset($data->duration) ? intval($data->duration) : 0,
        'thumbnailUrl' => isset($data->thumbnailUrl) && !empty($data->thumbnailUrl) ? $data->thumbnailUrl : null,
        'status' => 'published'
    ]);
    
    $courseId = $conn->lastInsertId();
    echo json_encode(["success" => true, "courseId" => $courseId, "message" => "Course created successfully"]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Database error: " . $e->getMessage()]);
}
?>
