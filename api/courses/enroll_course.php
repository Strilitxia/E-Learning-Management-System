<?php
require_once '../../config/db.php';
$data = json_decode(file_get_contents("php://input"));

if (isset($data->studentId) && isset($data->courseId)) {
    try {
        // First check if already enrolled (prevent duplicates)
        $checkStmt = $conn->prepare("SELECT EnrollmentID FROM Enrollment WHERE StudentID = :sid AND CourseID = :cid");
        $checkStmt->execute(['sid' => $data->studentId, 'cid' => $data->courseId]);
        
        if ($checkStmt->rowCount() > 0) {
            http_response_code(400);
            echo json_encode(["error" => "Already enrolled in this course."]);
            exit();
        }

        // Insert new enrollment
        $stmt = $conn->prepare("INSERT INTO Enrollment (StudentID, CourseID) VALUES (:sid, :cid)");
        $stmt->execute([
            'sid' => $data->studentId,
            'cid' => $data->courseId
        ]);
        
        echo json_encode(["success" => true]);
        
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => "Database error: " . $e->getMessage()]);
    }
} else {
    http_response_code(400);
    echo json_encode(["error" => "Missing studentId or courseId"]);
}
?>