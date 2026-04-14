<?php
require_once '../config/db.php';
header('Content-Type: application/json');

try {
    $stmtI = $conn->query("SELECT UserID, Email, FirstName, LastName FROM Users WHERE Role = 'instructor' LIMIT 1");
    $instructor = $stmtI->fetch(PDO::FETCH_ASSOC);

    $stmtC = $conn->query("SELECT CourseID, Title FROM Course WHERE InstructorID IS NULL OR InstructorID != " . ($instructor['UserID'] ?? 0) . " LIMIT 1");
    $course = $stmtC->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        'instructor' => $instructor,
        'course' => $course
    ]);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
