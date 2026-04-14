<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../../config/db.php';

$userId = isset($_GET['userId']) ? intval($_GET['userId']) : 0;
$assessmentId = isset($_GET['assessmentId']) ? intval($_GET['assessmentId']) : 0;

if ($userId === 0 || $assessmentId === 0) {
    http_response_code(400);
    echo json_encode(["error" => "Missing userId or assessmentId"]);
    exit();
}

try {
    // 1. Fetch submission record
    $stmtSub = $conn->prepare("
        SELECT sub.ScoreEarned, sub.SubmissionDate, sub.AnswersJSON, 
               a.Title as QuizTitle, a.MaxScore, c.Title as CourseTitle
        FROM Assessment_Submission sub
        JOIN Assessment a ON sub.AssessmentID = a.AssessmentID
        JOIN Course c ON a.CourseID = c.CourseID
        WHERE sub.StudentID = :uid AND sub.AssessmentID = :aid
        ORDER BY sub.SubmissionDate DESC LIMIT 1
    ");
    $stmtSub->execute(['uid' => $userId, 'aid' => $assessmentId]);
    $submission = $stmtSub->fetch(PDO::FETCH_ASSOC);

    if (!$submission) {
        http_response_code(404);
        echo json_encode(["error" => "Submission not found"]);
        exit();
    }

    // 2. Fetch original questions
    $stmtQ = $conn->prepare("SELECT * FROM Question WHERE AssessmentID = :aid ORDER BY QuestionID ASC");
    $stmtQ->execute(['aid' => $assessmentId]);
    $questions = $stmtQ->fetchAll(PDO::FETCH_ASSOC);

    // 3. Format response
    $studentAnswers = json_decode($submission['AnswersJSON'], true) ?: [];
    
    $reviewData = [];
    foreach ($questions as $index => $q) {
        $reviewData[] = [
            'questionText' => $q['QuestionText'],
            'options' => [$q['OptionA'], $q['OptionB'], $q['OptionC'], $q['OptionD']],
            'correctIndex' => (int)$q['CorrectOptionIndex'],
            'studentIndex' => isset($studentAnswers[$index]) ? (int)$studentAnswers[$index] : null
        ];
    }

    echo json_encode([
        "success" => true,
        "quizTitle" => $submission['QuizTitle'],
        "courseTitle" => $submission['CourseTitle'],
        "score" => $submission['ScoreEarned'],
        "maxScore" => $submission['MaxScore'],
        "date" => $submission['SubmissionDate'],
        "questions" => $reviewData
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}
?>
