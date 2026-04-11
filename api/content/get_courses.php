<?php
require_once '../../config/db.php';
$userId = isset($_GET['instructorId']) ? intval($_GET['instructorId']) : 0;

try {
    $stmt = $conn->prepare("SELECT CourseID, Title, Category FROM Course WHERE InstructorID = :uid");
    $stmt->execute(['uid' => $userId]);
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
} catch (PDOException $e) {
    http_response_code(500); echo json_encode(["error" => $e->getMessage()]);
}
?>