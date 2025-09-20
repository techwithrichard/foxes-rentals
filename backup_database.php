<?php
/**
 * Database Backup Script for Foxes Rentals
 * Creates a complete backup of the database before Phase 1 implementation
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

// Backup configuration
$backupDir = __DIR__ . '/backups';
$timestamp = date('Y-m-d_H-i-s');
$backupFile = "{$backupDir}/foxes_rentals_backup_{$timestamp}.sql";

// Create backup directory if it doesn't exist
if (!is_dir($backupDir)) {
    mkdir($backupDir, 0755, true);
    echo "✅ Created backup directory: {$backupDir}\n";
}

echo "🚀 Starting database backup...\n";
echo "📊 Database: {$database}\n";
echo "📁 Backup file: {$backupFile}\n";
echo "⏰ Timestamp: {$timestamp}\n\n";

try {
    // Create mysqldump command
    $command = sprintf(
        'mysqldump --host=%s --port=%s --user=%s --password=%s --single-transaction --routines --triggers --add-drop-table --add-locks --create-options --disable-keys --extended-insert --quick --set-charset %s > %s',
        escapeshellarg($host),
        escapeshellarg($port),
        escapeshellarg($username),
        escapeshellarg($password),
        escapeshellarg($database),
        escapeshellarg($backupFile)
    );

    echo "🔄 Executing backup command...\n";
    
    // Execute the backup command
    $output = [];
    $returnCode = 0;
    exec($command, $output, $returnCode);

    if ($returnCode === 0) {
        // Check if backup file was created and has content
        if (file_exists($backupFile) && filesize($backupFile) > 0) {
            $fileSize = formatBytes(filesize($backupFile));
            echo "✅ Database backup completed successfully!\n";
            echo "📁 Backup file: {$backupFile}\n";
            echo "📊 File size: {$fileSize}\n";
            echo "📅 Created: " . date('Y-m-d H:i:s') . "\n\n";
            
            // Create a symlink to the latest backup
            $latestBackup = "{$backupDir}/latest_backup.sql";
            if (file_exists($latestBackup)) {
                unlink($latestBackup);
            }
            symlink(basename($backupFile), $latestBackup);
            echo "🔗 Created symlink: {$latestBackup}\n";
            
            // Display backup summary
            displayBackupSummary($backupFile);
            
        } else {
            throw new Exception("Backup file was not created or is empty");
        }
    } else {
        throw new Exception("mysqldump command failed with return code: {$returnCode}");
    }

} catch (Exception $e) {
    echo "❌ Backup failed: " . $e->getMessage() . "\n";
    echo "💡 Make sure MySQL is running and credentials are correct\n";
    echo "💡 Check if mysqldump is available in your PATH\n";
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
 * Display backup summary
 */
function displayBackupSummary($backupFile) {
    echo "\n📋 BACKUP SUMMARY\n";
    echo "================\n";
    
    // Read first few lines to get table information
    $handle = fopen($backupFile, 'r');
    $tableCount = 0;
    $lineCount = 0;
    
    while (($line = fgets($handle)) !== false && $lineCount < 100) {
        if (strpos($line, 'CREATE TABLE') !== false) {
            $tableCount++;
        }
        $lineCount++;
    }
    fclose($handle);
    
    echo "📊 Estimated tables: {$tableCount}+\n";
    echo "📁 Backup location: " . dirname($backupFile) . "\n";
    echo "🔒 Backup type: Complete database dump\n";
    echo "📅 Backup date: " . date('Y-m-d H:i:s') . "\n";
    echo "✅ Status: Ready for Phase 1 implementation\n\n";
    
    echo "🚀 NEXT STEPS:\n";
    echo "==============\n";
    echo "1. ✅ Database backup completed\n";
    echo "2. 🔄 Proceed with Phase 1: Critical Fixes\n";
    echo "3. 📝 Complete empty controller methods\n";
    echo "4. 🔒 Implement input validation\n";
    echo "5. 🛡️ Fix security vulnerabilities\n";
    echo "6. 📊 Add database indexes\n\n";
    
    echo "💡 To restore this backup later, use:\n";
    echo "   mysql -u {$GLOBALS['username']} -p {$GLOBALS['database']} < {$backupFile}\n\n";
}

echo "🎉 Backup process completed successfully!\n";
echo "🚀 Ready to proceed with Phase 1 implementation!\n";
