<?php
require_once '../../config/db.php';
$data = json_decode(file_get_contents("php://input"));

if (isset($data->userId) && isset($data->currentPassword) && isset($data->newPassword)) {
    try {
        // 1. Get the current password hash
        $stmt = $conn->prepare("SELECT PasswordHash FROM Users WHERE UserID = :uid");
        $stmt->execute(['uid' => $data->userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // 2. Verify current password
            if (password_verify($data->currentPassword, $user['PasswordHash'])) {
                // 3. Hash new password and save
                $newHash = password_hash($data->newPassword, PASSWORD_BCRYPT);
                $updateStmt = $conn->prepare("UPDATE Users SET PasswordHash = :hash WHERE UserID = :uid");
                $updateStmt->execute(['hash' => $newHash, 'uid' => $data->userId]);
                
                echo json_encode(["success" => true]);
            } else {
                http_response_code(401); echo json_encode(["error" => "Current password is incorrect"]);
            }
        } else {
            http_response_code(404); echo json_encode(["error" => "User not found"]);
        }
    } catch (PDOException $e) {
        http_response_code(500); echo json_encode(["error" => $e->getMessage()]);
    }
} else {
    http_response_code(400); echo json_encode(["error" => "Missing required fields"]);
}
?>