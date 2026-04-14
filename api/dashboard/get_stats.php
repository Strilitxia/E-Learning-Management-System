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
    $stmt = $conn->prepare("SELECT COUNT(*) FROM Enrollment WHERE StudentID = ?");
    $stmt->execute([$userId]);
    $response['enrolled'] = $stmt->fetchColumn();

    $stmt = $conn->prepare("SELECT COUNT(*) FROM Enrollment WHERE StudentID = ? AND IsCompleted = 1");
    $stmt->execute([$userId]);
    $response['completed'] = $stmt->fetchColumn();

    $stmt = $conn->prepare("SELECT COUNT(*) FROM Enrollment WHERE StudentID = ? AND CertificateIssued = 1");
    $stmt->execute([$userId]);
    $response['certificates'] = $stmt->fetchColumn();

    // 2. Continue Learning (Most recent incomplete course)
    $stmt = $conn->prepare("
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
    $stmt = $conn->prepare("SELECT COUNT(DISTINCT DATE(SubmissionDate)) FROM Assessment_Submission WHERE StudentID = ? AND SubmissionDate >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)");
    $stmt->execute([$userId]);
    $response['streak'] = $stmt->fetchColumn(); 

    // 4. Study Activity Chart (Submissions over last 7 days)
    $stmt = $conn->prepare("
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
    $stmt = $conn->prepare("
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

        // 3. Average Course Rating (Dynamic: Performance + Engagement)
        $stmtRating = $conn->prepare("
            SELECT 
                (
                    COALESCE((SELECT AVG(sub.ScoreEarned / a.MaxScore * 100) 
                     FROM Assessment_Submission sub 
                     JOIN Assessment a ON sub.AssessmentID = a.AssessmentID
                     JOIN Course c2 ON a.CourseID = c2.CourseID
                     WHERE c2.InstructorID = :uid AND sub.GradingStatus = 'graded'), 0)
                    +
                    COALESCE((SELECT AVG(e.ProgressPercentage) 
                     FROM Enrollment e 
                     JOIN Course c3 ON e.CourseID = c3.CourseID
                     WHERE c3.InstructorID = :uid), 0)
                ) / 2 / 20 as derived_rating
        ");
        $stmtRating->execute(['uid' => $userId]);
        $avg = $stmtRating->fetch(PDO::FETCH_ASSOC)['derived_rating'];
        $response['avg_rating'] = $avg ? round($avg, 1) : 0.0;

        // 4. Pending Assignments
        $stmt = $conn->prepare("
            SELECT COUNT(*) as pending 
            FROM Assessment_Submission sub
            JOIN Assessment a ON sub.AssessmentID = a.AssessmentID
            JOIN Course c ON a.CourseID = c.CourseID
            WHERE c.InstructorID = :uid AND sub.GradingStatus = 'pending'
        ");
        $stmt->execute(['uid' => $userId]);
        $response['pending_assignments'] = $stmt->fetch(PDO::FETCH_ASSOC)['pending'];

        // 5. Enrollment Trend Chart (Last 7 Days)
        $labels = [];
        $dataCount = [];
        for ($i = 6; $i >= 0; $i--) {
            $labels[] = date('D', strtotime("-$i days"));
            $dateQuery = date('Y-m-d', strtotime("-$i days"));
            
            $stmtTrend = $conn->prepare("
                SELECT COUNT(e.EnrollmentID) as cnt 
                FROM Enrollment e
                JOIN Course c ON e.CourseID = c.CourseID
                WHERE c.InstructorID = :uid AND DATE(e.EnrollmentDate) = :dt
            ");
            $stmtTrend->execute(['uid' => $userId, 'dt' => $dateQuery]);
            $dataCount[] = $stmtTrend->fetch(PDO::FETCH_ASSOC)['cnt'];
        }
        $response['instructor_growth'] = [
            'labels' => $labels,
            'data' => $dataCount
        ];

        // 6. Action Items
        $actionItems = [];
        
        // 6a. System Alerts (New Assignments)
        $stmtSystemNotif = $conn->prepare("
            SELECT Message 
            FROM Notification 
            WHERE UserID = :uid AND IsRead = 0 AND Message LIKE 'ACTION_REQUIRED:%'
            ORDER BY CreatedAt DESC
        ");
        $stmtSystemNotif->execute(['uid' => $userId]);
        $systemAlerts = $stmtSystemNotif->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($systemAlerts as $alert) {
            // Clean the message for better UI: remove ACTION_REQUIRED prefix
            $displayTitle = str_replace('ACTION_REQUIRED: ', '', $alert['Message']);
            $actionItems[] = [
                'type' => 'alert', // Changed from info to alert for better icon handling
                'title' => $displayTitle,
                'detail' => 'Please create at least one module to begin course setup.',
                'link' => 'pages/content.html', // Specific link for content creation
                'count' => 0
            ];
        }

        // 6b. Grading Tasks
        $stmtAction = $conn->prepare("
            SELECT a.Title, c.Title as CourseTitle, COUNT(sub.SubmissionID) as NeedsGrading
            FROM Assessment_Submission sub
            JOIN Assessment a ON sub.AssessmentID = a.AssessmentID
            JOIN Course c ON a.CourseID = c.CourseID
            WHERE c.InstructorID = :uid AND sub.GradingStatus = 'pending'
            GROUP BY a.AssessmentID
            LIMIT 5
        ");
        $stmtAction->execute(['uid' => $userId]);
        $pendingTasks = $stmtAction->fetchAll(PDO::FETCH_ASSOC);
        
        foreach($pendingTasks as $task) {
            $actionItems[] = [
                'type' => 'grade',
                'title' => 'Grade ' . $task['Title'],
                'detail' => $task['CourseTitle'],
                'link' => 'pages/assessment.html', // Correct link for grading
                'count' => $task['NeedsGrading']
            ];
        }

        if (empty($actionItems)) {
            $actionItems[] = [
                'type' => 'info',
                'title' => 'All Caught Up!',
                'detail' => 'You have no pending system alerts or grading tasks.',
                'link' => '#',
                'count' => 0
            ];
        }
        
        // Quota: Max 2 alerts + remaining grading tasks up to 5 total
        $alerts = array_filter($actionItems, function($i) { return $i['type'] === 'alert'; });
        $grades = array_filter($actionItems, function($i) { return $i['type'] === 'grade'; });
        
        $finalInstructorActions = array_merge(
            array_slice($alerts, 0, 2),
            array_slice($grades, 0, 5 - min(2, count($alerts)))
        );

        if (empty($finalInstructorActions)) {
             $finalInstructorActions = [$actionItems[0]]; // Show "All Caught Up"
        }

        $response['action_items'] = $finalInstructorActions;

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

        // 4. Platform Growth Data (Last 6 Months)
        $months = [];
        for ($i = 5; $i >= 0; $i--) {
            $months[] = date('M', strtotime("-$i months"));
        }
        
        $userCounts = [];
        $courseCounts = [];
        foreach ($months as $m) {
            // Very brute force but clean for this scale
            $stmtU = $conn->prepare("SELECT COUNT(*) as cnt FROM Users WHERE DATE_FORMAT(JoinedDate, '%b') = :m AND JoinedDate >= DATE_SUB(Now(), INTERVAL 6 MONTH)");
            $stmtU->execute(['m' => $m]);
            $userCounts[] = $stmtU->fetch(PDO::FETCH_ASSOC)['cnt'];

            $stmtC = $conn->prepare("SELECT COUNT(*) as cnt FROM Course WHERE DATE_FORMAT(CreatedAt, '%b') = :m AND CreatedAt >= DATE_SUB(Now(), INTERVAL 6 MONTH)");
            $stmtC->execute(['m' => $m]);
            $courseCounts[] = $stmtC->fetch(PDO::FETCH_ASSOC)['cnt'];
        }

    $response['admin'] = [
            'labels' => $months,
            'users' => $userCounts,
            'courses' => $courseCounts
        ];

        // 5. Admin Reported Issues (Last 7 Days)
        $stmt = $conn->query("SELECT COUNT(*) as total FROM Moderation_Report WHERE ReportDate >= DATE_SUB(Now(), INTERVAL 7 DAY)");
        $response['admin_recent_reports'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // 6. Admin Activity Log (Federated Query)
        $stmt = $conn->query("
            (SELECT 'signup' as type, CONCAT(FirstName, ' ', LastName) as title, JoinedDate as dt FROM Users)
            UNION
            (SELECT 'course' as type, Title as title, CreatedAt as dt FROM Course)
            UNION
            (SELECT 'report' as type, Reason as title, ReportDate as dt FROM Moderation_Report)
            ORDER BY dt DESC LIMIT 10
        ");
        $response['admin_activity_log'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    }

    // 4. Unified Student Activity & Updates Feed
    if ($role === 'student') {

        // 4a. Upcoming Deadlines (Due in 14 days)
        $stmtDl = $conn->prepare("
            SELECT Title, DueDate, 'deadline' as type, CourseID
            FROM Assessment 
            WHERE CourseID IN (SELECT CourseID FROM Enrollment WHERE StudentID = ?)
            AND DueDate >= CURDATE()
            AND DueDate <= DATE_ADD(CURDATE(), INTERVAL 14 DAY)
            ORDER BY DueDate ASC
        ");
        $stmtDl->execute([$userId]);
        $deadlines = $stmtDl->fetchAll(PDO::FETCH_ASSOC);

        // 4b. Recently Assigned (Created in last 7 days)
        $stmtNewAssign = $conn->prepare("
            SELECT Title, CreatedAt as dt, 'assignment' as type, CourseID
            FROM Assessment
            WHERE CourseID IN (SELECT CourseID FROM Enrollment WHERE StudentID = ?)
            AND CreatedAt >= DATE_SUB(Now(), INTERVAL 7 DAY)
        ");
        $stmtNewAssign->execute([$userId]);
        $newAssign = $stmtNewAssign->fetchAll(PDO::FETCH_ASSOC);

        // 4c. New Modules (Last 7 days)
        $stmtNewMod = $conn->prepare("
            SELECT m.Title, m.CreatedAt as dt, 'module' as type, m.CourseID, c.Title as CourseTitle
            FROM Module m
            JOIN Course c ON m.CourseID = c.CourseID
            WHERE m.CourseID IN (SELECT CourseID FROM Enrollment WHERE StudentID = ?)
            AND m.CreatedAt >= DATE_SUB(Now(), INTERVAL 7 DAY)
        ");
        $stmtNewMod->execute([$userId]);
        $newMod = $stmtNewMod->fetchAll(PDO::FETCH_ASSOC);

        // 4d. New Platform Courses (Last 3 days - excluding enrolled)
        $stmtNewCourse = $conn->prepare("
            SELECT Title, CreatedAt as dt, 'course' as type, CourseID
            FROM Course
            WHERE CreatedAt >= DATE_SUB(Now(), INTERVAL 3 DAY)
            AND Status = 'published'
            AND CourseID NOT IN (SELECT CourseID FROM Enrollment WHERE StudentID = ?)
        ");
        $stmtNewCourse->execute([$userId]);
        $newCourses = $stmtNewCourse->fetchAll(PDO::FETCH_ASSOC);

        // 4e. Quota-based merge: Top 3 Deadlines + fill to total 8
        $finalActivity = [];

        // Pick Top 3 Deadlines (already sorted by DueDate ASC)
        $pickedDeadlines = array_slice($deadlines, 0, 3);
        foreach ($pickedDeadlines as $d) {
            $d['urgency'] = 1;
            $finalActivity[] = $d;
        }

        // Aggregate all other activities
        $otherActivities = [];
        foreach ($newAssign as $a) {
            // Avoid duplicate if same title is already a picked deadline
            $exists = false;
            foreach ($finalActivity as $item) {
                if ($item['type'] === 'deadline' && $item['Title'] === $a['Title']) { $exists = true; break; }
            }
            if (!$exists) { $a['urgency'] = 2; $otherActivities[] = $a; }
        }
        foreach ($newMod as $m) { $m['urgency'] = 3; $otherActivities[] = $m; }
        foreach ($newCourses as $c) { $c['urgency'] = 4; $otherActivities[] = $c; }

        // Sort other activities by recency (latest first)
        usort($otherActivities, function($a, $b) {
            return strtotime($b['dt']) - strtotime($a['dt']);
        });

        // Fill remaining slots up to 8 items total
        $remainingSlots = 5 - count($finalActivity);
        if ($remainingSlots > 0) {
            $pickedOthers = array_slice($otherActivities, 0, $remainingSlots);
            foreach ($pickedOthers as $o) {
                $finalActivity[] = $o;
            }
        }

        $response['student_activity'] = $finalActivity;
    }

    echo json_encode($response);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Database error: " . $e->getMessage()]);
}
?>