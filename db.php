<?php
/**
 * Database Connection File
 * ALTA iHub Project
 */

$servername = "localhost";
$username = "root";
$password = "";
$database = "updh2";

try {
    // PDO Connection
    $pdo = new PDO(
        "mysql:host=$servername;dbname=$database;charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );

    // MySQLi Connection (for some legacy queries if needed)
    $conn = new mysqli($servername, $username, $password, $database);
    
    if ($conn->connect_error) {
        throw new Exception("MySQLi Connection failed: " . $conn->connect_error);
    }

    $conn->set_charset("utf8mb4");

} catch (PDOException $e) {
    die("Database Connection Failed: " . $e->getMessage());
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
?>