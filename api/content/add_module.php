<?php
require_once '../../config/db.php';
$data = json_decode(file_get_contents("php://input"));

if(isset($data->courseId) && isset($data->title)) {
    try {
        $stmt = $conn->prepare("INSERT INTO Module (CourseID, Title, Description) VALUES (:cid, :title, :desc)");
        $stmt->execute([
            'cid' => $data->courseId,
            'title' => $data->title,
            'desc' => $data->description ?? ''
        ]);

        // 1. Find the instructor of this course to clear their notification
        $stmtInst = $conn->prepare("SELECT InstructorID FROM Course WHERE CourseID = ?");
        $stmtInst->execute([$data->courseId]);
        $instructorId = $stmtInst->fetchColumn();

        if ($instructorId) {
            // 2. Mark any "ACTION_REQUIRED" notification for this course as read
            $searchPattern = "%(ID:" . $data->courseId . ")";
            $stmtClear = $conn->prepare("
                UPDATE Notification 
                SET IsRead = 1 
                WHERE UserID = ? 
                AND Message LIKE 'ACTION_REQUIRED:%' 
                AND Message LIKE ?
                AND IsRead = 0
            ");
            $stmtClear->execute([$instructorId, $searchPattern]);
        }

        echo json_encode(["success" => true]);
    } catch (PDOException $e) {
        http_response_code(500); echo json_encode(["error" => $e->getMessage()]);
    }
}
?>