<?php
require_once '../../config/db.php';
$data = json_decode(file_get_contents("php://input"));

if(isset($data->moduleId)) {
    try {
        $stmt = $conn->prepare("DELETE FROM Module WHERE ModuleID = :mid");
        $stmt->execute(['mid' => $data->moduleId]);
        echo json_encode(["success" => true]);
    } catch (PDOException $e) {
        http_response_code(500); echo json_encode(["error" => $e->getMessage()]);
    }
}
?>