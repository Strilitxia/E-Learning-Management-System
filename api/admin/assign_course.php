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

if (!isset($data->courseId) || !isset($data->instructorId)) {
    http_response_code(400); 
    echo json_encode(["error" => "Missing required fields (courseId, instructorId)"]);
    exit();
}

try {
    // 1. Validate if the instructor exists and has the correct role
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

    // 2. Validate if the course exists
    $stmtCourse = $conn->prepare("SELECT Title, InstructorID FROM Course WHERE CourseID = :courseId");
    $stmtCourse->execute(['courseId' => intval($data->courseId)]);
    $course = $stmtCourse->fetch(PDO::FETCH_ASSOC);

    if (!$course) {
        echo json_encode(["success" => false, "error" => "Course ID does not exist"]);
        exit();
    }

    // 3. Assign the course
    if ($course['InstructorID'] == $data->instructorId) {
        echo json_encode(["success" => true, "message" => "Course is already assigned to this instructor"]);
        exit();
    }

    $stmtUpdate = $conn->prepare("UPDATE Course SET InstructorID = :instructorId WHERE CourseID = :courseId");
    $stmtUpdate->execute([
        'instructorId' => intval($data->instructorId),
        'courseId' => intval($data->courseId)
    ]);

    // 4. Create Notification for the instructor
    $notifMsg = "ACTION_REQUIRED: Newly Assigned Course: " . $course['Title'] . " (ID:" . $data->courseId . ")";
    $stmtNotif = $conn->prepare("INSERT INTO Notification (UserID, Message, IsRead) VALUES (:uid, :msg, 0)");
    $stmtNotif->execute([
        'uid' => intval($data->instructorId),
        'msg' => $notifMsg
    ]);
    
    echo json_encode(["success" => true, "message" => "Course successfully assigned to Instructor! Notification sent."]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Database error: " . $e->getMessage()]);
}
?>
