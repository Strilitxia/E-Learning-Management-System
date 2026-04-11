<?php
require_once '../../config/db.php';
$data = json_decode(file_get_contents("php://input"));

if(isset($data->lessonId)) {
    try {
        $stmt = $conn->prepare("DELETE FROM Lesson WHERE LessonID = :lid");
        $stmt->execute(['lid' => $data->lessonId]);
        echo json_encode(["success" => true]);
    } catch (PDOException $e) {
        http_response_code(500); echo json_encode(["error" => $e->getMessage()]);
    }
}
?>