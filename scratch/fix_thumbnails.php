<?php
require_once '../config/db.php';

try {
    $stmt = $conn->prepare("UPDATE Course SET ThumbnailURL = NULL WHERE ThumbnailURL LIKE '%via.placeholder.com%'");
    $stmt->execute();
    $count = $stmt->rowCount();
    echo "Success: $count course(s) updated. Broken placeholders were reset to NULL.";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
