<?php
require_once '../../config/db.php';
$quizId = isset($_GET['quizId']) ? intval($_GET['quizId']) : 0;

try {
    // Get Assessment Details
    $stmt1 = $conn->prepare("SELECT AssessmentID, Title, TimeLimit, Type, MaxScore FROM Assessment WHERE AssessmentID = :qid");
    $stmt1->execute(['qid' => $quizId]);
    $quiz = $stmt1->fetch(PDO::FETCH_ASSOC);

    if (!$quiz) { http_response_code(404); die(json_encode(["error" => "Quiz not found"])); }

    // Get Questions
    $stmt2 = $conn->prepare("SELECT QuestionText, OptionA, OptionB, OptionC, OptionD, CorrectOptionIndex FROM Question WHERE AssessmentID = :qid");
    $stmt2->execute(['qid' => $quizId]);
    $questionsRaw = $stmt2->fetchAll(PDO::FETCH_ASSOC);

    // Format to match JavaScript expectation
    $questions = [];
    foreach ($questionsRaw as $q) {
        $questions[] = [
            "text" => $q['QuestionText'],
            "options" => [$q['OptionA'], $q['OptionB'], $q['OptionC'], $q['OptionD']],
            "correct" => (int)$q['CorrectOptionIndex']
        ];
    }

    echo json_encode([
        "id" => $quiz['AssessmentID'],
        "title" => $quiz['Title'],
        "type" => $quiz['Type'],
        "maxScore" => (int)$quiz['MaxScore'],
        "duration" => (int)$quiz['TimeLimit'] * 60, // Convert minutes to seconds for JS timer
        "questions" => $questions
    ]);
} catch (PDOException $e) {
    http_response_code(500); echo json_encode(["error" => $e->getMessage()]);
}
?>