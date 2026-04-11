<?php
require_once '../../config/db.php';

$userId = isset($_GET['userId']) ? intval($_GET['userId']) : 0;
$role = isset($_GET['role']) ? $_GET['role'] : '';

if ($userId === 0 || $role === '') {
    http_response_code(400);
    echo json_encode(["error" => "Missing userId or role"]);
    exit();
}

try {
    if ($role === 'student') {
        // Fetch enrolled courses with progress and instructor name
        $stmt = $conn->prepare("
            SELECT c.CourseID, c.Title, c.Category, c.ThumbnailURL, 
                   u.FirstName, u.LastName,
                   e.ProgressPercentage, e.IsCompleted
            FROM Course c
            JOIN Enrollment e ON c.CourseID = e.CourseID
            LEFT JOIN Users u ON c.InstructorID = u.UserID
            WHERE e.StudentID = :uid
        ");
        $stmt->execute(['uid' => $userId]);

    } elseif ($role === 'instructor') {
        // Fetch courses assigned to this instructor
        $stmt = $conn->prepare("
            SELECT c.CourseID, c.Title, c.Category, c.ThumbnailURL,
                   u.FirstName, u.LastName
            FROM Course c
            LEFT JOIN Users u ON c.InstructorID = u.UserID
            WHERE c.InstructorID = :uid
        ");
        $stmt->execute(['uid' => $userId]);

    } elseif ($role === 'admin') {
        // Fetch all courses for moderation
        $stmt = $conn->prepare("
            SELECT c.CourseID, c.Title, c.Category, c.ThumbnailURL, c.Status,
                   u.FirstName, u.LastName
            FROM Course c
            LEFT JOIN Users u ON c.InstructorID = u.UserID
            ORDER BY c.CreatedAt DESC
        ");
        $stmt->execute();
    }

    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Format the response to handle NULL thumbnails/names
    foreach ($courses as &$course) {
        $course['InstructorName'] = ($course['FirstName'] && $course['LastName']) ? $course['FirstName'] . ' ' . $course['LastName'] : 'Unknown Instructor';
        $course['ThumbnailURL'] = $course['ThumbnailURL'] ?: 'https://images.unsplash.com/photo-1517430816045-df4b7de11d1d?w=800'; // Default fallback image
    }

    echo json_encode($courses);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Database error: " . $e->getMessage()]);
}
?>