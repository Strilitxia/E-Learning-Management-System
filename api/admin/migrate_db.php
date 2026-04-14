<?php
require_once '../../config/db.php';

try {
    $sql = "ALTER TABLE Assessment_Submission ADD COLUMN AnswersJSON TEXT";
    $conn->exec($sql);
    echo "Migration successful: AnswersJSON column added to Assessment_Submission table.";
} catch (PDOException $e) {
    if ($e->getCode() == "42S21") {
        echo "Migration skipped: Column 'AnswersJSON' already exists.";
    } else {
        echo "Migration failed: " . $e->getMessage();
    }
}
?>
