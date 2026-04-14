<?php
header('Content-Type: application/json');
require_once '../../config/db.php';

$assessmentId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($assessmentId === 0) {
    http_response_code(400);
    echo json_encode(["error" => "Missing assessment ID"]);
    exit();
}

try {
    // 1. Get Assessment Info
    $stmt1 = $conn->prepare("SELECT Title, MaxScore, Type FROM Assessment WHERE AssessmentID = :aid");
    $stmt1->execute(['aid' => $assessmentId]);
    $assessment = $stmt1->fetch(PDO::FETCH_ASSOC);

    if (!$assessment) {
        http_response_code(404);
        echo json_encode(["error" => "Assessment not found"]);
        exit();
    }

    // 2. Get Submissions
    $stmt2 = $conn->prepare("
        SELECT sub.SubmissionID, sub.StudentID, sub.SubmissionDate, sub.ScoreEarned, sub.GradingStatus, sub.SubmissionLink, sub.Feedback, sub.AnswersJSON,
               u.FirstName, u.LastName
        FROM Assessment_Submission sub
        JOIN Users u ON sub.StudentID = u.UserID
        WHERE sub.AssessmentID = :aid
        ORDER BY sub.SubmissionDate DESC
    ");
    $stmt2->execute(['aid' => $assessmentId]);
    $submissions = $stmt2->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "assessment" => $assessment,
        "submissions" => $submissions
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Database error: " . $e->getMessage()]);
}
?>
