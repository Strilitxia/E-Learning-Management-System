<?php
// api/auth/login.php
require_once '../../config/db.php';

$data = json_decode(file_get_contents("php://input"));

if (!empty($data->email) && !empty($data->password)) {
    
    $query = "SELECT UserID, FirstName, LastName, Email, PasswordHash, Role, Status FROM Users WHERE Email = :email LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':email', $data->email);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Check if account is banned
        if($row['Status'] === 'banned') {
            http_response_code(403);
            echo json_encode(["message" => "Account is banned."]);
            exit();
        }

        // Verify password
        if (password_verify($data->password, $row['PasswordHash'])) {
            http_response_code(200);
            echo json_encode([
                "message" => "Login successful",
                "user" => [
                    "id" => $row['UserID'],
                    "name" => $row['FirstName'] . ' ' . $row['LastName'],
                    "email" => $row['Email'],
                    "role" => $row['Role']
                ]
            ]);
        } else {
            http_response_code(401);
            echo json_encode(["message" => "Incorrect password."]);
        }
    } else {
        http_response_code(404);
        echo json_encode(["message" => "User not found."]);
    }
} else {
    http_response_code(400);
    echo json_encode(["message" => "Incomplete data."]);
}
?>