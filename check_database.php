<?php
/**
 * Quick Database Check Script
 * Run this with: php check_database.php
 */

require_once 'vendor/autoload.php';

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

echo "=== Foxes Rental Database Status Check ===\n\n";

try {
    // Database connection
    $host = $_ENV['DB_HOST'] ?? '127.0.0.1';
    $port = $_ENV['DB_PORT'] ?? '3306';
    $database = $_ENV['DB_DATABASE'] ?? 'foxes_rentals';
    $username = $_ENV['DB_USERNAME'] ?? 'root';
    $password = $_ENV['DB_PASSWORD'] ?? '';
    
    echo "Connecting to database: $database@$host:$port\n";
    
    $dsn = "mysql:host=$host;port=$port;dbname=$database;charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
    
    echo "✅ Database connection successful!\n\n";
    
    // Get tables
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    echo "📊 Found " . count($tables) . " tables:\n";
    
    foreach ($tables as $table) {
        $count = $pdo->query("SELECT COUNT(*) FROM `$table`")->fetchColumn();
        echo "  - $table: $count records\n";
    }
    
    echo "\n";
    
    // Check key tables
    $keyTables = ['users', 'properties', 'houses', 'leases', 'invoices', 'payments', 'c2b_requests', 'stk_requests'];
    
    echo "🔍 Key Tables Status:\n";
    foreach ($keyTables as $table) {
        if (in_array($table, $tables)) {
            $count = $pdo->query("SELECT COUNT(*) FROM `$table`")->fetchColumn();
            echo "  ✅ $table: $count records\n";
        } else {
            echo "  ❌ $table: Table missing\n";
        }
    }
    
    // Check migrations
    echo "\n📝 Migration Status:\n";
    if (in_array('migrations', $tables)) {
        $migrations = $pdo->query("SELECT COUNT(*) FROM migrations")->fetchColumn();
        echo "  ✅ Migrations table: $migrations migrations recorded\n";
        
        // Show recent migrations
        $recent = $pdo->query("SELECT migration, batch FROM migrations ORDER BY batch DESC, id DESC LIMIT 5")->fetchAll();
        echo "  Recent migrations:\n";
        foreach ($recent as $migration) {
            echo "    - {$migration['migration']} (batch {$migration['batch']})\n";
        }
    } else {
        echo "  ❌ No migrations table found - database may not be migrated\n";
    }
    
    echo "\n🎉 Database check completed successfully!\n";
    
} catch (PDOException $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "\n";
    echo "\nTroubleshooting:\n";
    echo "1. Check if MySQL is running\n";
    echo "2. Verify database credentials in .env file\n";
    echo "3. Make sure database exists\n";
    exit(1);
}
