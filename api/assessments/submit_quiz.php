<?php
require_once '../../config/db.php';
$data = json_decode(file_get_contents("php://input"));

if(isset($data->assessmentId) && isset($data->studentId) && isset($data->score)) {
    try {
        $stmt = $conn->prepare("INSERT INTO Assessment_Submission (AssessmentID, StudentID, ScoreEarned, GradingStatus) VALUES (:aid, :sid, :score, 'graded')");
        $stmt->execute([
            'aid' => $data->assessmentId,
            'sid' => $data->studentId,
            'score' => $data->score
        ]);
        echo json_encode(["success" => true]);
    } catch (PDOException $e) {
        http_response_code(500); echo json_encode(["error" => $e->getMessage()]);
    }
}
?>