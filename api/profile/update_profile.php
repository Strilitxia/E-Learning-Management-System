<?php
require_once '../../config/db.php';
$data = json_decode(file_get_contents("php://input"));

if (isset($data->userId) && isset($data->firstName) && isset($data->lastName)) {
    try {
        $stmt = $conn->prepare("
            UPDATE Users 
            SET FirstName = :fname, LastName = :lname, Phone = :phone, Bio = :bio, ProfilePictureURL = :profilePic 
            WHERE UserID = :uid
        ");
        
        $stmt->execute([
            'fname' => $data->firstName,
            'lname' => $data->lastName,
            'phone' => $data->phone ?? null,
            'bio' => $data->bio ?? null,
            'profilePic' => $data->profilePictureURL ?? null,
            'uid' => $data->userId
        ]);
        
        echo json_encode(["success" => true]);
    } catch (PDOException $e) {
        http_response_code(500); echo json_encode(["error" => $e->getMessage()]);
    }
} else {
    http_response_code(400); echo json_encode(["error" => "Missing required fields"]);
}
?>