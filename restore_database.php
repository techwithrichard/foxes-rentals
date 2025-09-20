<?php
/**
 * Database Restore Script for Foxes Rentals
 * Restores database from backup file
 */

// Function to read .env file
function readEnv($key, $default = null) {
    $envFile = __DIR__ . '/.env';
    if (!file_exists($envFile)) {
        return $default;
    }
    
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
            list($envKey, $envValue) = explode('=', $line, 2);
            $envKey = trim($envKey);
            $envValue = trim($envValue);
            
            // Remove quotes if present
            if ((substr($envValue, 0, 1) === '"' && substr($envValue, -1) === '"') ||
                (substr($envValue, 0, 1) === "'" && substr($envValue, -1) === "'")) {
                $envValue = substr($envValue, 1, -1);
            }
            
            if ($envKey === $key) {
                return $envValue;
            }
        }
    }
    
    return $default;
}

// Database configuration
$host = readEnv('DB_HOST', 'localhost');
$username = readEnv('DB_USERNAME', 'root');
$password = readEnv('DB_PASSWORD', '');
$database = readEnv('DB_DATABASE', 'foxes_rentals');
$port = readEnv('DB_PORT', '3306');

// Get backup file from command line argument or use latest
$backupFile = $argv[1] ?? __DIR__ . '/backups/latest_backup.sql';

if (!file_exists($backupFile)) {
    echo "❌ Backup file not found: {$backupFile}\n";
    echo "💡 Available backup files:\n";
    
    $backupDir = dirname($backupFile);
    if (is_dir($backupDir)) {
        $files = glob($backupDir . '/*.sql');
        foreach ($files as $file) {
            $size = formatBytes(filesize($file));
            $date = date('Y-m-d H:i:s', filemtime($file));
            echo "   📁 " . basename($file) . " ({$size}) - {$date}\n";
        }
    }
    exit(1);
}

echo "🔄 Starting database restore...\n";
echo "📊 Database: {$database}\n";
echo "📁 Backup file: {$backupFile}\n";
echo "📊 File size: " . formatBytes(filesize($backupFile)) . "\n";
echo "📅 Backup date: " . date('Y-m-d H:i:s', filemtime($backupFile)) . "\n\n";

// Confirm restore
echo "⚠️  WARNING: This will completely replace the current database!\n";
echo "📋 Current database: {$database}\n";
echo "📁 Restoring from: " . basename($backupFile) . "\n\n";

if (!isset($argv[2]) || $argv[2] !== '--force') {
    echo "❓ Do you want to continue? (yes/no): ";
    $handle = fopen("php://stdin", "r");
    $line = fgets($handle);
    fclose($handle);
    
    if (trim(strtolower($line)) !== 'yes') {
        echo "❌ Restore cancelled by user\n";
        exit(0);
    }
}

try {
    // Create restore command
    $command = sprintf(
        'mysql --host=%s --port=%s --user=%s --password=%s %s < %s',
        escapeshellarg($host),
        escapeshellarg($port),
        escapeshellarg($username),
        escapeshellarg($password),
        escapeshellarg($database),
        escapeshellarg($backupFile)
    );

    echo "🔄 Executing restore command...\n";
    
    // Execute the restore command
    $output = [];
    $returnCode = 0;
    exec($command, $output, $returnCode);

    if ($returnCode === 0) {
        echo "✅ Database restore completed successfully!\n";
        echo "📊 Database: {$database}\n";
        echo "📁 Restored from: " . basename($backupFile) . "\n";
        echo "📅 Restore date: " . date('Y-m-d H:i:s') . "\n\n";
        
        // Verify restore
        echo "🔍 Verifying restore...\n";
        verifyRestore($host, $port, $username, $password, $database);
        
    } else {
        throw new Exception("mysql command failed with return code: {$returnCode}");
    }

} catch (Exception $e) {
    echo "❌ Restore failed: " . $e->getMessage() . "\n";
    echo "💡 Make sure MySQL is running and credentials are correct\n";
    echo "💡 Check if mysql command is available in your PATH\n";
    exit(1);
}

/**
 * Format bytes to human readable format
 */
function formatBytes($size, $precision = 2) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
        $size /= 1024;
    }
    return round($size, $precision) . ' ' . $units[$i];
}

/**
 * Verify the restore by checking table counts
 */
function verifyRestore($host, $port, $username, $password, $database) {
    try {
        $pdo = new PDO(
            "mysql:host={$host};port={$port};dbname={$database}",
            $username,
            $password,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
        
        // Get table count
        $stmt = $pdo->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        $tableCount = count($tables);
        
        echo "✅ Verification successful!\n";
        echo "📊 Tables restored: {$tableCount}\n";
        
        // Check key tables
        $keyTables = ['users', 'properties', 'leases', 'payments'];
        foreach ($keyTables as $table) {
            if (in_array($table, $tables)) {
                $stmt = $pdo->query("SELECT COUNT(*) FROM {$table}");
                $count = $stmt->fetchColumn();
                echo "📋 {$table}: {$count} records\n";
            }
        }
        
        echo "\n🎉 Database restore completed and verified!\n";
        
    } catch (Exception $e) {
        echo "⚠️  Restore completed but verification failed: " . $e->getMessage() . "\n";
    }
}

echo "🚀 Database restore process completed!\n";
