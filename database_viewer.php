<?php
/**
 * Database Viewer Script for Foxes Rental Management System
 * This script will help you view your MySQL database structure and data
 */

// Database configuration - Update these values according to your setup
$host = '127.0.0.1';
$port = '3306';
$database = 'foxes_rentals';
$username = 'root';
$password = ''; // Update this with your MySQL password

try {
    // Create PDO connection
    $dsn = "mysql:host=$host;port=$port;dbname=$database;charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    
    echo "<h1>Foxes Rental Management System - Database Viewer</h1>";
    echo "<h2>Database Connection: SUCCESS</h2>";
    echo "<p>Connected to database: <strong>$database</strong></p>";
    
    // Get all tables
    $tablesQuery = "SHOW TABLES";
    $tables = $pdo->query($tablesQuery)->fetchAll(PDO::FETCH_COLUMN);
    
    echo "<h3>Database Tables (" . count($tables) . " tables found):</h3>";
    echo "<ul>";
    foreach ($tables as $table) {
        echo "<li><a href='#table_$table'>$table</a></li>";
    }
    echo "</ul>";
    
    // Show table details
    foreach ($tables as $table) {
        echo "<hr>";
        echo "<h3 id='table_$table'>Table: $table</h3>";
        
        // Get table structure
        $structureQuery = "DESCRIBE `$table`";
        $structure = $pdo->query($structureQuery)->fetchAll();
        
        echo "<h4>Table Structure:</h4>";
        echo "<table border='1' cellpadding='5' cellspacing='0'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
        foreach ($structure as $column) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($column['Field']) . "</td>";
            echo "<td>" . htmlspecialchars($column['Type']) . "</td>";
            echo "<td>" . htmlspecialchars($column['Null']) . "</td>";
            echo "<td>" . htmlspecialchars($column['Key']) . "</td>";
            echo "<td>" . htmlspecialchars($column['Default'] ?? 'NULL') . "</td>";
            echo "<td>" . htmlspecialchars($column['Extra']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Get row count
        $countQuery = "SELECT COUNT(*) as count FROM `$table`";
        $count = $pdo->query($countQuery)->fetch()['count'];
        echo "<p><strong>Total Records:</strong> $count</p>";
        
        // Show sample data (first 5 records)
        if ($count > 0) {
            echo "<h4>Sample Data (first 5 records):</h4>";
            $dataQuery = "SELECT * FROM `$table` LIMIT 5";
            $data = $pdo->query($dataQuery)->fetchAll();
            
            if (!empty($data)) {
                echo "<table border='1' cellpadding='5' cellspacing='0'>";
                // Header
                echo "<tr>";
                foreach (array_keys($data[0]) as $column) {
                    echo "<th>" . htmlspecialchars($column) . "</th>";
                }
                echo "</tr>";
                
                // Data rows
                foreach ($data as $row) {
                    echo "<tr>";
                    foreach ($row as $value) {
                        $displayValue = $value === null ? 'NULL' : htmlspecialchars(substr($value, 0, 100));
                        echo "<td>$displayValue</td>";
                    }
                    echo "</tr>";
                }
                echo "</table>";
            }
        }
    }
    
    // Check migration status
    echo "<hr>";
    echo "<h3>Migration Status Check</h3>";
    if (in_array('migrations', $tables)) {
        $migrationQuery = "SELECT * FROM migrations ORDER BY batch DESC, migration DESC LIMIT 10";
        $migrations = $pdo->query($migrationQuery)->fetchAll();
        
        echo "<h4>Recent Migrations:</h4>";
        echo "<table border='1' cellpadding='5' cellspacing='0'>";
        echo "<tr><th>Migration</th><th>Batch</th></tr>";
        foreach ($migrations as $migration) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($migration['migration']) . "</td>";
            echo "<td>" . htmlspecialchars($migration['batch']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p><strong>Warning:</strong> No migrations table found. Database may not be properly migrated.</p>";
    }
    
} catch (PDOException $e) {
    echo "<h2>Database Connection Error</h2>";
    echo "<p><strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<h3>Troubleshooting Steps:</h3>";
    echo "<ol>";
    echo "<li>Make sure MySQL server is running</li>";
    echo "<li>Check if the database '$database' exists</li>";
    echo "<li>Verify username and password</li>";
    echo "<li>Check if the host and port are correct</li>";
    echo "</ol>";
    echo "<h3>To create the database:</h3>";
    echo "<p>Run this SQL command in your MySQL client:</p>";
    echo "<code>CREATE DATABASE IF NOT EXISTS $database CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;</code>";
}
?>
