<?php
require_once '../../config/db.php';
try {
    $query = "SELECT m.ReportID, m.Reason, m.ReportDate, m.ResolutionStatus, 
                     u.UserID, u.FirstName, u.LastName, u.Email
              FROM Moderation_Report m
              JOIN Users u ON m.ReportedUserID = u.UserID
              WHERE m.ResolutionStatus = 'pending'
              ORDER BY m.ReportDate DESC";
    $stmt = $conn->query($query);
    $reports = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($reports);
} catch (PDOException $e) {
    http_response_code(500); echo json_encode(["error" => $e->getMessage()]);
}
?>