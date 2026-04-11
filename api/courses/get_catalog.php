<?php
require_once '../../config/db.php';

$userId = isset($_GET['userId']) ? intval($_GET['userId']) : 0;

try {
    // Fetch published courses, count total students, and check if the current user is enrolled
    $stmt = $conn->prepare("
        SELECT c.CourseID, c.Title, c.Category, c.Level, c.Duration, c.ThumbnailURL, c.Rating,
               u.FirstName, u.LastName,
               (SELECT COUNT(*) FROM Enrollment WHERE CourseID = c.CourseID) as StudentCount,
               (SELECT COUNT(*) FROM Enrollment WHERE CourseID = c.CourseID AND StudentID = :uid) as IsEnrolled
        FROM Course c
        LEFT JOIN Users u ON c.InstructorID = u.UserID
        WHERE c.Status = 'published'
        ORDER BY c.CreatedAt DESC
    ");
    $stmt->execute(['uid' => $userId]);
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Format the data
    foreach ($courses as &$course) {
        $course['InstructorName'] = ($course['FirstName'] && $course['LastName']) ? $course['FirstName'] . ' ' . $course['LastName'] : 'Unknown Instructor';
        $course['ThumbnailURL'] = $course['ThumbnailURL'] ?: 'https://images.unsplash.com/photo-1517430816045-df4b7de11d1d?w=800'; // Default fallback
        $course['IsEnrolled'] = $course['IsEnrolled'] > 0 ? true : false;
        $course['Rating'] = $course['Rating'] ? round($course['Rating'], 1) : 0.0;
    }

    echo json_encode($courses);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Database error: " . $e->getMessage()]);
}
?>