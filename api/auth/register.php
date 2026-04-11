<?php
// api/auth/register.php
require_once '../../config/db.php';

// Get JSON payload from JavaScript
$data = json_decode(file_get_contents("php://input"));

if (!empty($data->firstName) && !empty($data->lastName) && !empty($data->email) && !empty($data->password) && !empty($data->role)) {
    
    // Hash the password for security
    $passwordHash = password_hash($data->password, PASSWORD_BCRYPT);
    
    try {
        $query = "INSERT INTO Users (FirstName, LastName, Email, PasswordHash, Role) 
                  VALUES (:firstName, :lastName, :email, :passwordHash, :role)";
        
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':firstName', $data->firstName);
        $stmt->bindParam(':lastName', $data->lastName);
        $stmt->bindParam(':email', $data->email);
        $stmt->bindParam(':passwordHash', $passwordHash);
        $stmt->bindParam(':role', $data->role);
        
        if ($stmt->execute()) {
            http_response_code(201);
            echo json_encode(["message" => "User registered successfully."]);
        }
    } catch(PDOException $e) {
        http_response_code(400);
        // Handle duplicate email error
        if($e->getCode() == 23000) {
            echo json_encode(["message" => "Email already exists."]);
        } else {
            echo json_encode(["message" => "Registration failed: " . $e->getMessage()]);
        }
    }
} else {
    http_response_code(400);
    echo json_encode(["message" => "Incomplete data."]);
}
?>