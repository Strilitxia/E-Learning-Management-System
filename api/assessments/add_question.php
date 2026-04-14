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

if (!isset($data->assessmentId) || !isset($data->text) || !isset($data->options) || !isset($data->correctIndex)) {
    http_response_code(400); 
    echo json_encode(["error" => "Missing required fields"]);
    exit();
}

try {
    $stmt = $conn->prepare("INSERT INTO Question (AssessmentID, QuestionText, OptionA, OptionB, OptionC, OptionD, CorrectOptionIndex) 
                            VALUES (:assessmentId, :text, :optA, :optB, :optC, :optD, :correctIndex)");
                            
    $stmt->execute([
        'assessmentId' => intval($data->assessmentId),
        'text' => htmlspecialchars($data->text),
        'optA' => htmlspecialchars($data->options[0] ?? ''),
        'optB' => htmlspecialchars($data->options[1] ?? ''),
        'optC' => htmlspecialchars($data->options[2] ?? ''),
        'optD' => htmlspecialchars($data->options[3] ?? ''),
        'correctIndex' => intval($data->correctIndex)
    ]);
    
    echo json_encode(["success" => true, "questionId" => $conn->lastInsertId()]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Database error: " . $e->getMessage()]);
}
?>
