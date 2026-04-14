<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../../config/db.php';

$userId = isset($_GET['userId']) ? intval($_GET['userId']) : 0;
$courseId = isset($_GET['courseId']) ? intval($_GET['courseId']) : 0;

if ($userId === 0 || $courseId === 0) {
    http_response_code(400);
    echo json_encode(["error" => "Missing userId or courseId"]);
    exit();
}

try {
    // 1. Fetch certificate data and verify completion
    $stmt = $conn->prepare("
        SELECT u.FirstName, u.LastName, c.Title as CourseTitle, 
               inst.FirstName as InstFirstName, inst.LastName as InstLastName,
               e.EnrollmentDate, e.IsCompleted
        FROM Enrollment e
        JOIN Users u ON e.StudentID = u.UserID
        JOIN Course c ON e.CourseID = c.CourseID
        LEFT JOIN Users inst ON c.InstructorID = inst.UserID
        WHERE e.StudentID = :uid AND e.CourseID = :cid
    ");
    $stmt->execute(['uid' => $userId, 'cid' => $courseId]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$data) {
        http_response_code(404);
        echo json_encode(["error" => "Enrollment record not found"]);
        exit();
    }

    if ($data['IsCompleted'] != 1) {
        http_response_code(403);
        echo json_encode(["error" => "Course not completed yet"]);
        exit();
    }

    // 2. Mark as CertificateIssued
    $update = $conn->prepare("UPDATE Enrollment SET CertificateIssued = 1 WHERE StudentID = :uid AND CourseID = :cid");
    $update->execute(['uid' => $userId, 'cid' => $courseId]);

    // 3. Construct response
    $response = [
        "success" => true,
        "studentName" => $data['FirstName'] . ' ' . $data['LastName'],
        "courseTitle" => $data['CourseTitle'],
        "instructorName" => ($data['InstFirstName'] && $data['InstLastName']) ? $data['InstFirstName'] . ' ' . $data['InstLastName'] : "ELP Platform",
        "date" => date('F d, Y'), // Today's date as issuance date
        "certId" => "ELP-" . strtoupper(substr(md5($userId . $courseId), 0, 8)) // Unique-ish ID
    ];

    echo json_encode($response);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Database error: " . $e->getMessage()]);
}
?>
