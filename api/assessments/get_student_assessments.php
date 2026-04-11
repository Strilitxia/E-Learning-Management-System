<?php
require_once '../../config/db.php';
$userId = isset($_GET['userId']) ? intval($_GET['userId']) : 0;

if ($userId === 0) { http_response_code(400); die(json_encode(["error" => "Missing userId"])); }

try {
    // 1. Get Pending Assessments (Enrolled courses, but no submission yet)
    $stmt1 = $conn->prepare("
        SELECT a.AssessmentID, a.Title, a.TimeLimit, a.DueDate, c.Title as CourseTitle
        FROM Assessment a
        JOIN Enrollment e ON a.CourseID = e.CourseID
        JOIN Course c ON a.CourseID = c.CourseID
        WHERE e.StudentID = :uid AND a.Status = 'active'
        AND a.AssessmentID NOT IN (SELECT AssessmentID FROM Assessment_Submission WHERE StudentID = :uid)
    ");
    $stmt1->execute(['uid' => $userId]);
    $pending = $stmt1->fetchAll(PDO::FETCH_ASSOC);

    // 2. Get Completed Assessments
    $stmt2 = $conn->prepare("
        SELECT sub.SubmissionDate, sub.ScoreEarned, a.Title, a.MaxScore, c.Title as CourseTitle
        FROM Assessment_Submission sub
        JOIN Assessment a ON sub.AssessmentID = a.AssessmentID
        JOIN Course c ON a.CourseID = c.CourseID
        WHERE sub.StudentID = :uid
        ORDER BY sub.SubmissionDate DESC
    ");
    $stmt2->execute(['uid' => $userId]);
    $completed = $stmt2->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(["pending" => $pending, "completed" => $completed]);
} catch (PDOException $e) {
    http_response_code(500); echo json_encode(["error" => $e->getMessage()]);
}
?>