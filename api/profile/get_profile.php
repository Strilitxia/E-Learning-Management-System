<?php
require_once '../../config/db.php';

$userId = isset($_GET['userId']) ? intval($_GET['userId']) : 0;

if ($userId === 0) {
    http_response_code(400); die(json_encode(["error" => "Missing userId"]));
}

try {
    // 1. Get basic user info
    $stmt = $conn->prepare("SELECT FirstName, LastName, Email, Role, Phone, Bio, ProfilePictureURL, JoinedDate FROM Users WHERE UserID = :uid");
    $stmt->execute(['uid' => $userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        http_response_code(404); die(json_encode(["error" => "User not found"]));
    }

    // 2. Get platform activity stats
    $stats = ["enrolled" => 0, "completed" => 0, "certificates" => 0];
    
    if ($user['Role'] === 'student') {
        $statsStmt = $conn->prepare("
            SELECT 
                COUNT(*) as enrolled,
                SUM(CASE WHEN IsCompleted = 1 THEN 1 ELSE 0 END) as completed,
                SUM(CASE WHEN CertificateIssued = 1 THEN 1 ELSE 0 END) as certs
            FROM Enrollment WHERE StudentID = :uid
        ");
        $statsStmt->execute(['uid' => $userId]);
        $res = $statsStmt->fetch(PDO::FETCH_ASSOC);
        $stats['enrolled'] = $res['enrolled'] ?? 0;
        $stats['completed'] = $res['completed'] ?? 0;
        $stats['certificates'] = $res['certs'] ?? 0;
    }

    echo json_encode([
        "user" => $user,
        "stats" => $stats
    ]);

} catch (PDOException $e) {
    http_response_code(500); echo json_encode(["error" => $e->getMessage()]);
}
?>