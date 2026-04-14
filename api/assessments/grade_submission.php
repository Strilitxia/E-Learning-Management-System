<?php
header('Content-Type: application/json');
require_once '../../config/db.php';

$data = json_decode(file_get_contents("php://input"));

if (!isset($data->submissionId) || !isset($data->score)) {
    http_response_code(400);
    echo json_encode(["error" => "Missing submissionId or score"]);
    exit();
}

try {
    $conn->beginTransaction();

    // 1. Update the submission
    $stmt = $conn->prepare("UPDATE Assessment_Submission SET ScoreEarned = :score, Feedback = :fb, GradingStatus = 'graded' WHERE SubmissionID = :sid");
    $stmt->execute([
        'score' => intval($data->score),
        'fb' => isset($data->feedback) ? $data->feedback : null,
        'sid' => intval($data->submissionId)
    ]);

    // 2. Fetch context for progress update
    $stmtCtx = $conn->prepare("
        SELECT sub.StudentID, a.CourseID 
        FROM Assessment_Submission sub
        JOIN Assessment a ON sub.AssessmentID = a.AssessmentID
        WHERE sub.SubmissionID = :sid
    ");
    $stmtCtx->execute(['sid' => intval($data->submissionId)]);
    $ctx = $stmtCtx->fetch(PDO::FETCH_ASSOC);

    if ($ctx) {
        $studentId = $ctx['StudentID'];
        $courseId = $ctx['CourseID'];

        // Recalculate progress (same logic as submit_quiz.php)
        $stmt1 = $conn->prepare("SELECT COUNT(*) FROM Assessment WHERE CourseID = :cid AND Status = 'active'");
        $stmt1->execute(['cid' => $courseId]);
        $totalAssessments = $stmt1->fetchColumn();

        $stmt2 = $conn->prepare("
            SELECT COUNT(DISTINCT sub.AssessmentID) 
            FROM Assessment_Submission sub 
            JOIN Assessment a ON sub.AssessmentID = a.AssessmentID 
            WHERE a.CourseID = :cid AND sub.StudentID = :sid AND sub.GradingStatus = 'graded'
        ");
        $stmt2->execute(['cid' => $courseId, 'sid' => $studentId]);
        $completedAssessments = $stmt2->fetchColumn();

        $progress = 0;
        $isCompleted = 0;
        if ($totalAssessments > 0) {
            $progress = round(($completedAssessments / $totalAssessments) * 100);
            if ($progress >= 100) {
                $progress = 100;
                $isCompleted = 1;
            }
        }

        // Update Enrollment
        $stmtUpd = $conn->prepare("UPDATE Enrollment SET ProgressPercentage = :prog, IsCompleted = :comp WHERE StudentID = :sid AND CourseID = :cid");
        $stmtUpd->execute([
            'prog' => $progress,
            'comp' => $isCompleted,
            'sid' => $studentId,
            'cid' => $courseId
        ]);
    }

    $conn->commit();
    echo json_encode(["success" => true, "message" => "Grade saved and progress updated"]);

} catch (PDOException $e) {
    if ($conn->inTransaction()) $conn->rollBack();
    http_response_code(500);
    echo json_encode(["error" => "Database error: " . $e->getMessage()]);
}
?>
