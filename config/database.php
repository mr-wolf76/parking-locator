<?php
/**
 * Database Connection Configuration
 * Project: Parking Information and Locator System (Kathmandu)
 * Academic Project - Bachelor of Information Management (BIM)
 * 
 * Works with XAMPP (Apache + MySQL) out of the box.
 */

// Database configuration settings
$dbHost = '127.0.0.1';
$dbPort = '3306';
$dbName = 'parking_locator';
$dbUser = 'root';
$dbPass = ''; // Default XAMPP password is empty

// Function to establish and return the PDO database connection
function getDbConnection() {
    global $dbHost, $dbPort, $dbName, $dbUser, $dbPass;
    static $pdo = null;

    if ($pdo !== null) {
        return $pdo;
    }

    try {
        // Connect to MySQL server
        $dsn = "mysql:host={$dbHost};port={$dbPort};dbname={$dbName};charset=utf8mb4";
        $pdo = new PDO($dsn, $dbUser, $dbPass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_TIMEOUT => 2
        ]);
        return $pdo;
    } catch (PDOException $e) {
        // If database doesn't exist in MySQL, attempt to auto-create it
        try {
            $serverPdo = new PDO("mysql:host={$dbHost};port={$dbPort};charset=utf8mb4", $dbUser, $dbPass);
            $serverPdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbName}` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            
            $pdo = new PDO("mysql:host={$dbHost};port={$dbPort};dbname={$dbName};charset=utf8mb4", $dbUser, $dbPass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]);

            // Auto-populate tables from SQL file if available
            $sqlFile = dirname(__DIR__) . '/database/parking_locator.sql';
            if (file_exists($sqlFile)) {
                $sql = file_get_contents($sqlFile);
                $statements = array_filter(array_map('trim', explode(';', $sql)));
                foreach ($statements as $stmt) {
                    if (empty($stmt)) continue;
                    if (stripos($stmt, 'CREATE DATABASE') === 0 || stripos($stmt, 'USE ') === 0) continue;
                    try {
                        $pdo->exec($stmt);
                    } catch (Exception $ign) {}
                }
            }
            return $pdo;
        } catch (Exception $creationErr) {
            // Fallback for cloud development container if MySQL service is not local
            $sqliteFile = dirname(__DIR__) . '/database/parking_locator.sqlite';
            if (file_exists($sqliteFile)) {
                $pdo = new PDO("sqlite:" . $sqliteFile);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                return $pdo;
            }

            // Friendly error message for XAMPP
            die("<div style='font-family:sans-serif;padding:30px;max-width:500px;margin:50px auto;border:1px solid #ccc;border-radius:8px;'>" .
                "<h3>MySQL Connection Error</h3>" .
                "<p>Could not connect to MySQL database <strong>{$dbName}</strong>.</p>" .
                "<p><strong>How to fix in XAMPP:</strong><br>1. Open XAMPP Control Panel.<br>2. Start <strong>MySQL</strong>.<br>3. Open phpMyAdmin and import <code>database/parking_locator.sql</code>.</p>" .
                "</div>");
        }
    }
}
