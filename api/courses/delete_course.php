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

if (!isset($data->courseId)) {
    http_response_code(400); 
    echo json_encode(["error" => "Missing required field: courseId"]);
    exit();
}

try {
    // Due to ON DELETE CASCADE on foreign keys in DB (per schema notes), 
    // this single delete should clear out Modules, Lessons, Enrollments, Assessments related to this course.
    $stmt = $conn->prepare("DELETE FROM Course WHERE CourseID = :courseId");
    $stmt->execute([
        'courseId' => intval($data->courseId)
    ]);
    
    if ($stmt->rowCount() > 0) {
        echo json_encode(["success" => true, "message" => "Course deleted successfully"]);
    } else {
        echo json_encode(["success" => false, "error" => "Course not found or already deleted"]);
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Database error: " . $e->getMessage()]);
}
?>
