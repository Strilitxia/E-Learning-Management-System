<?php
require_once '../../config/db.php';
$data = json_decode(file_get_contents("php://input"));

if(isset($data->userId) && isset($data->action)) {
    try {
        if ($data->action === 'update_role') {
            $stmt = $conn->prepare("UPDATE Users SET Role = :role WHERE UserID = :id");
            $stmt->execute(['role' => $data->role, 'id' => $data->userId]);
        } elseif ($data->action === 'ban') {
            $stmt = $conn->prepare("UPDATE Users SET Status = 'banned' WHERE UserID = :id");
            $stmt->execute(['id' => $data->userId]);
        }
        echo json_encode(["success" => true]);
    } catch (PDOException $e) {
        http_response_code(500); echo json_encode(["error" => $e->getMessage()]);
    }
}
?>