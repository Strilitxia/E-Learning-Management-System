<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../../config/db.php';

try {
    $stmt = $conn->query("
        SELECT c.CourseID, c.Title, c.InstructorID, c.Category, c.Level, c.Status, c.CreatedAt, c.ThumbnailURL,
               u.FirstName as InstructorFirst, u.LastName as InstructorLast
        FROM Course c
        LEFT JOIN Users u ON c.InstructorID = u.UserID
        ORDER BY c.CreatedAt DESC
    ");
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode(["success" => true, "courses" => $courses]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Database error: " . $e->getMessage()]);
}
?>
