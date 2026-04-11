<?php
require_once '../../config/db.php';
$userId = isset($_GET['userId']) ? intval($_GET['userId']) : 0;

try {
    $stmt = $conn->prepare("
        SELECT a.AssessmentID, a.Title, a.Status, a.MaxScore, c.Title as CourseTitle,
               (SELECT COUNT(*) FROM Assessment_Submission WHERE AssessmentID = a.AssessmentID) as SubmissionsCount,
               (SELECT AVG(ScoreEarned) FROM Assessment_Submission WHERE AssessmentID = a.AssessmentID) as AvgScore
        FROM Assessment a
        JOIN Course c ON a.CourseID = c.CourseID
        WHERE c.InstructorID = :uid
    ");
    $stmt->execute(['uid' => $userId]);
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
} catch (PDOException $e) {
    http_response_code(500); echo json_encode(["error" => $e->getMessage()]);
}
?>