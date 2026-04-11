<?php
require_once '../../config/db.php';
$data = json_decode(file_get_contents("php://input"));

if(isset($data->moduleId) && isset($data->title) && isset($data->type)) {
    try {
        $stmt = $conn->prepare("INSERT INTO Lesson (ModuleID, Title, ContentType, ContentURL, TextBody, FileURL) 
                                VALUES (:mid, :title, :type, :url, :body, :file)");
        $stmt->execute([
            'mid' => $data->moduleId,
            'title' => $data->title,
            'type' => $data->type,
            'url' => $data->url ?? null,
            'body' => $data->textBody ?? null,
            'file' => $data->fileUrl ?? null
        ]);
        echo json_encode(["success" => true]);
    } catch (PDOException $e) {
        http_response_code(500); echo json_encode(["error" => $e->getMessage()]);
    }
}
?>