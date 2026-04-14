<?php
require_once '../../config/db.php';

try {
    $conn->beginTransaction();

    // Add CreatedAt to Module
    try {
        $conn->exec("ALTER TABLE Module ADD COLUMN CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP");
        echo "Added CreatedAt to Module table.\n";
    } catch (PDOException $e) { if ($e->getCode() == "42S21") echo "Module.CreatedAt already exists.\n"; else throw $e; }

    // Add CreatedAt to Assessment
    try {
        $conn->exec("ALTER TABLE Assessment ADD COLUMN CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP");
        echo "Added CreatedAt to Assessment table.\n";
    } catch (PDOException $e) { if ($e->getCode() == "42S21") echo "Assessment.CreatedAt already exists.\n"; else throw $e; }

    // Add CreatedAt to Lesson
    try {
        $conn->exec("ALTER TABLE Lesson ADD COLUMN CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP");
        echo "Added CreatedAt to Lesson table.\n";
    } catch (PDOException $e) { if ($e->getCode() == "42S21") echo "Lesson.CreatedAt already exists.\n"; else throw $e; }

    $conn->commit();
    echo "Chronos migration completed successfully.";
} catch (PDOException $e) {
    if ($conn->inTransaction()) $conn->rollBack();
    echo "Migration failed: " . $e->getMessage();
}
?>
