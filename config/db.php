<?php
// config/db.php
$host = 'localhost';
$db_name = 'elp_db';
$username = 'root'; // Default XAMPP username
$password = '';     // Default XAMPP password is empty

try {
    $conn = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);
    // Set PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Ensure data is sent in JSON format for the API
    header('Content-Type: application/json');
    header('Access-Control-Allow-Origin: *'); // Allow local testing
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
    header('Access-Control-Allow-Headers: Content-Type');
} catch(PDOException $e) {
    echo json_encode(["error" => "Connection failed: " . $e->getMessage()]);
    die();
}
?>