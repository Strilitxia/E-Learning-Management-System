<?php
require_once '../../config/db.php';

echo "<h2>Starting Database Migration...</h2>";

try {
    // 1. Add SubmissionLink column
    $sql1 = "ALTER TABLE Assessment_Submission ADD COLUMN SubmissionLink TEXT AFTER AnswersJSON";
    try {
        $conn->exec($sql1);
        echo "<p>✅ Added 'SubmissionLink' column.</p>";
    } catch (PDOException $e) {
        if ($e->getCode() == "42S21") echo "<p>ℹ️ 'SubmissionLink' already exists skipping.</p>";
        else throw $e;
    }

    // 2. Add Feedback column
    $sql2 = "ALTER TABLE Assessment_Submission ADD COLUMN Feedback TEXT AFTER SubmissionLink";
    try {
        $conn->exec($sql2);
        echo "<p>✅ Added 'Feedback' column.</p>";
    } catch (PDOException $e) {
        if ($e->getCode() == "42S21") echo "<p>ℹ️ 'Feedback' already exists skipping.</p>";
        else throw $e;
    }

    // 3. Ensure ScoreEarned is nullable (for pending grades)
    $sql3 = "ALTER TABLE Assessment_Submission MODIFY COLUMN ScoreEarned INT NULL";
    $conn->exec($sql3);
    echo "<p>✅ Modified 'ScoreEarned' to be nullable.</p>";

    echo "<h3>Migration Completed Successfully!</h3>";
} catch (PDOException $e) {
    echo "<h3 style='color:red;'>Migration Failed: " . $e->getMessage() . "</h3>";
}
?>
