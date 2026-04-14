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

if(isset($data->assessmentId) && isset($data->studentId)) {
    try {
        $conn->beginTransaction();

        $type = isset($data->type) ? $data->type : 'quiz';
        $status = ($type === 'quiz') ? 'graded' : 'pending';
        $score = isset($data->score) ? intval($data->score) : null;
        $link = isset($data->submissionLink) ? $data->submissionLink : null;
        $answers = isset($data->answers) ? json_encode($data->answers) : null;

        $stmt = $conn->prepare("INSERT INTO Assessment_Submission (AssessmentID, StudentID, ScoreEarned, GradingStatus, AnswersJSON, SubmissionLink) VALUES (:aid, :sid, :score, :status, :answers, :link)");
        $stmt->execute([
            'aid' => intval($data->assessmentId),
            'sid' => intval($data->studentId),
            'score' => $score,
            'status' => $status,
            'answers' => $answers,
            'link' => $link
        ]);

        // Get CourseID for this assessment
        $stmtCourse = $conn->prepare("SELECT CourseID FROM Assessment WHERE AssessmentID = :aid");
        $stmtCourse->execute(['aid' => intval($data->assessmentId)]);
        $courseId = $stmtCourse->fetchColumn();

        if ($courseId) {
            // Count total assessments in course
            $stmt1 = $conn->prepare("SELECT COUNT(*) FROM Assessment WHERE CourseID = :cid");
            $stmt1->execute(['cid' => $courseId]);
            $totalAssessments = $stmt1->fetchColumn();

            // Count completed assessments for this student in course
            $stmt2 = $conn->prepare("SELECT COUNT(DISTINCT a.AssessmentID) 
                                     FROM Assessment_Submission sub 
                                     JOIN Assessment a ON sub.AssessmentID = a.AssessmentID 
                                     WHERE a.CourseID = :cid AND sub.StudentID = :sid");
            $stmt2->execute(['cid' => $courseId, 'sid' => intval($data->studentId)]);
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

            // Update Enrollment Progress
            $stmtUpd = $conn->prepare("UPDATE Enrollment SET ProgressPercentage = :prog, IsCompleted = :comp WHERE StudentID = :sid AND CourseID = :cid");
            $stmtUpd->execute([
                'prog' => $progress,
                'comp' => $isCompleted,
                'sid' => intval($data->studentId),
                'cid' => $courseId
            ]);
        }

        $conn->commit();
        echo json_encode(["success" => true, "progress" => $progress]);

    } catch (PDOException $e) {
        $conn->rollBack();
        http_response_code(500); 
        echo json_encode(["error" => $e->getMessage()]);
    }
} else {
    http_response_code(400); 
    echo json_encode(["error" => "Missing required fields"]);
}
?>