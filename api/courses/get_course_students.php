<?php
require_once '../../config/db.php';

header('Content-Type: application/json');

$courseId = isset($_GET['courseId']) ? intval($_GET['courseId']) : 0;
$instructorId = isset($_GET['instructorId']) ? intval($_GET['instructorId']) : 0;

if (!$courseId || !$instructorId) {
    http_response_code(400);
    echo json_encode(["error" => "Missing courseId or instructorId"]);
    exit();
}

try {
    // 1. Verify that this instructor actually owns the course
    $stmtVerify = $conn->prepare("SELECT InstructorID FROM Course WHERE CourseID = ?");
    $stmtVerify->execute([$courseId]);
    $course = $stmtVerify->fetch(PDO::FETCH_ASSOC);

    if (!$course || $course['InstructorID'] != $instructorId) {
        http_response_code(403);
        echo json_encode(["error" => "Unauthorized access to this course student list"]);
        exit();
    }

    // 2. Fetch enrolled students
    $stmt = $conn->prepare("
        SELECT u.UserID, u.FirstName, u.LastName, u.Email, u.JoinedDate,
               e.EnrollmentDate, e.ProgressPercentage, e.IsCompleted
        FROM Enrollment e
        JOIN Users u ON e.StudentID = u.UserID
        WHERE e.CourseID = ?
        ORDER BY u.FirstName ASC
    ");
    $stmt->execute([$courseId]);
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($students);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}
?>
