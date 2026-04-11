<?php
require_once '../../config/db.php';
$data = json_decode(file_get_contents("php://input"));

if(isset($data->courseId) && isset($data->title)) {
    try {
        $stmt = $conn->prepare("INSERT INTO Module (CourseID, Title, Description) VALUES (:cid, :title, :desc)");
        $stmt->execute([
            'cid' => $data->courseId,
            'title' => $data->title,
            'desc' => $data->description ?? ''
        ]);
        echo json_encode(["success" => true]);
    } catch (PDOException $e) {
        http_response_code(500); echo json_encode(["error" => $e->getMessage()]);
    }
}
?>