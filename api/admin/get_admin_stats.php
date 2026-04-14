<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../../config/db.php';
$response = [];

try {
    // Top Quick Stats
    $response['total_users'] = $conn->query("SELECT COUNT(*) FROM Users")->fetchColumn();
    $response['active_courses'] = $conn->query("SELECT COUNT(*) FROM Course WHERE Status = 'published' OR Status = 'active'")->fetchColumn();
    $response['enrollments'] = $conn->query("SELECT COUNT(*) FROM Enrollment")->fetchColumn();
    $response['pending_reports'] = $conn->query("SELECT COUNT(*) FROM Moderation_Report WHERE ResolutionStatus = 'pending'")->fetchColumn();
    $response['high_priority_reports'] = $conn->query("SELECT COUNT(*) FROM Moderation_Report WHERE ResolutionStatus = 'pending' AND ReportDate >= DATE_SUB(Now(), INTERVAL 1 DAY)")->fetchColumn();

    // Analytics Dashboard Stats
    $response['enrollments_today'] = $conn->query("SELECT COUNT(*) FROM Enrollment WHERE DATE(EnrollmentDate) = CURRENT_DATE")->fetchColumn();
    
    $avgComp = $conn->query("SELECT AVG(ProgressPercentage) FROM Enrollment")->fetchColumn();
    $response['avg_completion'] = $avgComp !== null ? round($avgComp) : 0;
    
    $response['active_instructors'] = $conn->query("SELECT COUNT(*) FROM Users WHERE Role = 'instructor' AND Status = 'active'")->fetchColumn();
    
    $response['total_quizzes'] = $conn->query("SELECT COUNT(*) FROM Assessment_Submission")->fetchColumn();

    // Line Chart: User Registration Trend (Last 6 Months)
    $stmtReg = $conn->query("
        SELECT DATE_FORMAT(JoinedDate, '%M') AS Month, COUNT(*) as Count 
        FROM Users 
        WHERE JoinedDate >= DATE_SUB(Now(), INTERVAL 6 MONTH)
        GROUP BY YEAR(JoinedDate), MONTH(JoinedDate)
        ORDER BY YEAR(JoinedDate), MONTH(JoinedDate)
    ");
    $registrations = $stmtReg->fetchAll(PDO::FETCH_ASSOC);
    $response['registration_trend'] = [
        'labels' => array_column($registrations, 'Month'),
        'data' => array_column($registrations, 'Count')
    ];
    
    // Fallback if empty to avoid broken chart
    if (empty($response['registration_trend']['labels'])) {
        $response['registration_trend'] = [
            'labels' => [date('M')],
            'data' => [$response['total_users']]
        ];
    }

    // Doughnut Chart: Course Category Distribution
    $stmtCat = $conn->query("
        SELECT Category, COUNT(*) as Count 
        FROM Course 
        GROUP BY Category
    ");
    $categories = $stmtCat->fetchAll(PDO::FETCH_ASSOC);
    $response['category_distribution'] = [
        'labels' => array_column($categories, 'Category'),
        'data' => array_column($categories, 'Count')
    ];
    
    // Fallback
    if (empty($response['category_distribution']['labels'])) {
        $response['category_distribution'] = [
            'labels' => ['No Data'],
            'data' => [1]
        ];
    }

    echo json_encode($response);
} catch (PDOException $e) {
    http_response_code(500); echo json_encode(["error" => $e->getMessage()]);
}
?>