 <?php
// config/database.php

/**
 * Database connection using PDO
 * Best practices include:
 * - Using environment variables
 * - Error handling
 * - Setting default fetch mode
 * - Avoiding hardcoded credentials in production
 */

$host = 'localhost';
$db   = 'admin_panel';
$user = 'root';
$pass = 'root'; // Default MAMP password, change as needed
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

define ("BASE_URL", "http://localhost:8888/Real-Estate-Management-Geofrey/admin-folder");
?>