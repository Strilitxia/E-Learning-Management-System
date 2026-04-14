<?php
require_once '../../config/db.php';
$userId = isset($_GET['userId']) ? intval($_GET['userId']) : 0;

try {
    // 1. Get all assessments for this instructor
    $stmt = $conn->prepare("
        SELECT a.AssessmentID, a.Title, a.Status, a.MaxScore, a.Type, c.Title as CourseTitle,
               (SELECT COUNT(*) FROM Assessment_Submission WHERE AssessmentID = a.AssessmentID) as SubmissionsCount,
               (SELECT AVG(ScoreEarned) FROM Assessment_Submission WHERE AssessmentID = a.AssessmentID) as AvgScore
        FROM Assessment a
        JOIN Course c ON a.CourseID = c.CourseID
        WHERE c.InstructorID = :uid
    ");
    $stmt->execute(['uid' => $userId]);
    $assessments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 2. Get "Needs Attention" (Last 5 recent submissions for instructor's courses)
    $stmtSub = $conn->prepare("
        SELECT sub.SubmissionID, sub.SubmissionDate, sub.ScoreEarned,
               a.Title as AssessmentTitle, a.MaxScore, c.Title as CourseTitle,
               u.FirstName, u.LastName
        FROM Assessment_Submission sub
        JOIN Assessment a ON sub.AssessmentID = a.AssessmentID
        JOIN Course c ON a.CourseID = c.CourseID
        JOIN Users u ON sub.StudentID = u.UserID
        WHERE c.InstructorID = :uid
        ORDER BY sub.SubmissionDate DESC
        LIMIT 5
    ");
    $stmtSub->execute(['uid' => $userId]);
    $needsAttention = $stmtSub->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "assessments" => $assessments,
        "needs_attention" => $needsAttention
    ]);
} catch (PDOException $e) {
    http_response_code(500); echo json_encode(["error" => $e->getMessage()]);
}
?>