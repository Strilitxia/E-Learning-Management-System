<?php
require_once '../../config/db.php';
$courseId = isset($_GET['courseId']) ? intval($_GET['courseId']) : 0;

try {
    // Get Modules
    $stmtMod = $conn->prepare("SELECT * FROM Module WHERE CourseID = :cid ORDER BY ModuleID ASC");
    $stmtMod->execute(['cid' => $courseId]);
    $modules = $stmtMod->fetchAll(PDO::FETCH_ASSOC);

    // Attach Lessons to each Module
    foreach ($modules as &$mod) {
        $stmtLes = $conn->prepare("SELECT * FROM Lesson WHERE ModuleID = :mid ORDER BY LessonID ASC");
        $stmtLes->execute(['mid' => $mod['ModuleID']]);
        $mod['contents'] = $stmtLes->fetchAll(PDO::FETCH_ASSOC);
    }

    echo json_encode($modules);
} catch (PDOException $e) {
    http_response_code(500); echo json_encode(["error" => $e->getMessage()]);
}
?>