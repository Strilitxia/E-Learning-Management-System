<?php
require_once '../../config/db.php';

// Get parameters from the URL (e.g., ?userId=1&role=student)
$userId = isset($_GET['userId']) ? intval($_GET['userId']) : 0;
$role = isset($_GET['role']) ? $_GET['role'] : '';

if ($userId === 0 || $role === '') {
    http_response_code(400);
    echo json_encode(["error" => "Missing userId or role"]);
    exit();
}

$response = [];

try {
    if ($role === 'student') {
    // 1. Basic Stats (Already existing)
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM Enrollment WHERE StudentID = ?");
    $stmt->execute([$userId]);
    $response['enrolled'] = $stmt->fetchColumn();

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM Enrollment WHERE StudentID = ? AND IsCompleted = 1");
    $stmt->execute([$userId]);
    $response['completed'] = $stmt->fetchColumn();

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM Enrollment WHERE StudentID = ? AND CertificateIssued = 1");
    $stmt->execute([$userId]);
    $response['certificates'] = $stmt->fetchColumn();

    // 2. Continue Learning (Most recent incomplete course)
    $stmt = $pdo->prepare("
        SELECT c.Title, c.ThumbnailURL, e.ProgressPercentage 
        FROM Enrollment e 
        JOIN Course c ON e.CourseID = c.CourseID 
        WHERE e.StudentID = ? AND e.IsCompleted = 0 
        ORDER BY e.EnrollmentDate DESC LIMIT 1
    ");
    $stmt->execute([$userId]);
    $response['continue_learning'] = $stmt->fetch(PDO::FETCH_ASSOC);

    // 3. Learning Streak
    // Note: True streaks require a daily login log. Based on your schema, 
    // we can approximate it by checking recent Assessment Submissions.
    $stmt = $pdo->prepare("SELECT COUNT(DISTINCT DATE(SubmissionDate)) FROM Assessment_Submission WHERE StudentID = ? AND SubmissionDate >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)");
    $stmt->execute([$userId]);
    $response['streak'] = $stmt->fetchColumn(); 

    // 4. Study Activity Chart (Submissions over last 7 days)
    $stmt = $pdo->prepare("
        SELECT DATE(SubmissionDate) as Date, COUNT(*) as Count 
        FROM Assessment_Submission 
        WHERE StudentID = ? AND SubmissionDate >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
        GROUP BY DATE(SubmissionDate)
        ORDER BY Date ASC
    ");
    $stmt->execute([$userId]);
    $activityData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Format for Chart.js
    $response['charts']['activity'] = [
        'labels' => array_column($activityData, 'Date'),
        'data' => array_column($activityData, 'Count')
    ];

    // 5. Skills Progress (Average scores grouped by Course Category)
    $stmt = $pdo->prepare("
        SELECT c.Category, AVG(asub.ScoreEarned / a.MaxScore * 100) as AvgScore
        FROM Assessment_Submission asub
        JOIN Assessment a ON asub.AssessmentID = a.AssessmentID
        JOIN Course c ON a.CourseID = c.CourseID
        WHERE asub.StudentID = ?
        GROUP BY c.Category
    ");
    $stmt->execute([$userId]);
    $skillsData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $response['charts']['skills'] = [
        'labels' => array_column($skillsData, 'Category'),
        'data' => array_column($skillsData, 'AvgScore')
    ];

    } elseif ($role === 'instructor') {
        // 1. Active Courses
        $stmt = $conn->prepare("SELECT COUNT(*) as total FROM Course WHERE InstructorID = :uid AND Status = 'published'");
        $stmt->execute(['uid' => $userId]);
        $response['active_courses'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // 2. Total Students (Count enrollments for this instructor's courses)
        $stmt = $conn->prepare("
            SELECT COUNT(e.EnrollmentID) as total 
            FROM Enrollment e
            JOIN Course c ON e.CourseID = c.CourseID
            WHERE c.InstructorID = :uid
        ");
        $stmt->execute(['uid' => $userId]);
        $response['total_students'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // 3. Average Course Rating
        $stmt = $conn->prepare("SELECT AVG(Rating) as avg_rating FROM Course WHERE InstructorID = :uid");
        $stmt->execute(['uid' => $userId]);
        $avg = $stmt->fetch(PDO::FETCH_ASSOC)['avg_rating'];
        $response['avg_rating'] = $avg ? round($avg, 1) : 0.0;

    } elseif ($role === 'admin') {
        // 1. Total Users
        $stmt = $conn->query("SELECT COUNT(*) as total FROM Users");
        $response['total_users'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // 2. Active Courses
        $stmt = $conn->query("SELECT COUNT(*) as total FROM Course WHERE Status = 'published'");
        $response['active_courses'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // 3. Pending Approvals/Reports
        $stmt = $conn->query("SELECT COUNT(*) as total FROM Moderation_Report WHERE ResolutionStatus = 'pending'");
        $response['pending_reports'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    echo json_encode($response);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Database error: " . $e->getMessage()]);
}
?>