<?php
require_once '../../config/db.php';
try {
    // Get all users, newest first
    $stmt = $conn->query("SELECT UserID, FirstName, LastName, Email, Role, JoinedDate, Status FROM Users ORDER BY JoinedDate DESC");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($users);
} catch (PDOException $e) {
    http_response_code(500); echo json_encode(["error" => $e->getMessage()]);
}
?>