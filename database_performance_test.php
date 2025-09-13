<?php
/**
 * Database Performance Testing Script for Foxes Rental Management System
 * 
 * This script tests database performance including query optimization,
 * index analysis, and connection performance.
 */

require_once 'vendor/autoload.php';

class DatabasePerformanceTester
{
    private $connection;
    private $results = [];
    
    public function __construct()
    {
        $this->initializeConnection();
    }
    
    /**
     * Initialize database connection
     */
    private function initializeConnection()
    {
        try {
            // Load environment variables
            $envFile = '.env';
            if (file_exists($envFile)) {
                $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                foreach ($lines as $line) {
                    if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
                        list($key, $value) = explode('=', $line, 2);
                        $_ENV[trim($key)] = trim($value);
                    }
                }
            }
            
            $host = $_ENV['DB_HOST'] ?? '127.0.0.1';
            $port = $_ENV['DB_PORT'] ?? '3306';
            $database = $_ENV['DB_DATABASE'] ?? 'foxes_rentals';
            $username = $_ENV['DB_USERNAME'] ?? 'root';
            $password = $_ENV['DB_PASSWORD'] ?? '';
            
            $dsn = "mysql:host={$host};port={$port};dbname={$database};charset=utf8mb4";
            
            $this->connection = new PDO($dsn, $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
            
            echo "✅ Database connection established successfully\n";
            
        } catch (PDOException $e) {
            echo "❌ Database connection failed: " . $e->getMessage() . "\n";
            echo "Please ensure your database is running and .env file is configured correctly.\n";
            exit(1);
        }
    }
    
    /**
     * Run all database performance tests
     */
    public function runAllTests()
    {
        echo "🚀 Starting Database Performance Tests\n";
        echo str_repeat("=", 50) . "\n";
        
        $this->testConnectionPerformance();
        $this->testTablePerformance();
        $this->testQueryPerformance();
        $this->testIndexPerformance();
        $this->testJoinPerformance();
        $this->testAggregationPerformance();
        $this->analyzeSlowQueries();
        
        $this->generateReport();
    }
    
    /**
     * Test connection performance
     */
    private function testConnectionPerformance()
    {
        echo "\n🔌 Testing Connection Performance...\n";
        
        $iterations = 10;
        $totalTime = 0;
        
        for ($i = 0; $i < $iterations; $i++) {
            $start = microtime(true);
            
            try {
                $stmt = $this->connection->query("SELECT 1");
                $stmt->fetch();
                $connectionTime = microtime(true) - $start;
                $totalTime += $connectionTime;
            } catch (PDOException $e) {
                echo "Connection test failed: " . $e->getMessage() . "\n";
                return;
            }
        }
        
        $avgTime = $totalTime / $iterations;
        $this->results['connection'] = [
            'avg_time_ms' => round($avgTime * 1000, 2),
            'iterations' => $iterations
        ];
        
        echo "Average connection time: " . round($avgTime * 1000, 2) . "ms\n";
    }
    
    /**
     * Test table performance
     */
    private function testTablePerformance()
    {
        echo "\n📊 Testing Table Performance...\n";
        
        $tables = $this->getTables();
        
        foreach ($tables as $table) {
            $this->testTableStats($table);
        }
    }
    
    /**
     * Get list of tables
     */
    private function getTables()
    {
        try {
            $stmt = $this->connection->query("SHOW TABLES");
            $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
            return $tables;
        } catch (PDOException $e) {
            echo "Could not retrieve tables: " . $e->getMessage() . "\n";
            return [];
        }
    }
    
    /**
     * Test individual table statistics
     */
    private function testTableStats($table)
    {
        try {
            // Count rows
            $start = microtime(true);
            $stmt = $this->connection->query("SELECT COUNT(*) as count FROM `{$table}`");
            $count = $stmt->fetch()['count'];
            $countTime = microtime(true) - $start;
            
            // Get table size
            $stmt = $this->connection->query("
                SELECT 
                    ROUND(((data_length + index_length) / 1024 / 1024), 2) AS size_mb
                FROM information_schema.TABLES 
                WHERE table_schema = DATABASE() 
                AND table_name = '{$table}'
            ");
            $size = $stmt->fetch()['size_mb'] ?? 0;
            
            $this->results['tables'][$table] = [
                'row_count' => $count,
                'count_time_ms' => round($countTime * 1000, 2),
                'size_mb' => $size
            ];
            
            echo "  {$table}: {$count} rows, {$size}MB, " . round($countTime * 1000, 2) . "ms\n";
            
        } catch (PDOException $e) {
            echo "  {$table}: Error - " . $e->getMessage() . "\n";
        }
    }
    
    /**
     * Test query performance
     */
    private function testQueryPerformance()
    {
        echo "\n🔍 Testing Query Performance...\n";
        
        $queries = [
            'SELECT * FROM users LIMIT 10' => 'Users (10 rows)',
            'SELECT * FROM properties LIMIT 10' => 'Properties (10 rows)',
            'SELECT * FROM tenants LIMIT 10' => 'Tenants (10 rows)',
            'SELECT * FROM payments LIMIT 10' => 'Payments (10 rows)',
            'SELECT COUNT(*) FROM users' => 'Count users',
            'SELECT COUNT(*) FROM properties' => 'Count properties',
            'SELECT COUNT(*) FROM tenants' => 'Count tenants',
            'SELECT COUNT(*) FROM payments' => 'Count payments',
        ];
        
        foreach ($queries as $query => $description) {
            $this->testQuery($query, $description);
        }
    }
    
    /**
     * Test individual query
     */
    private function testQuery($query, $description)
    {
        try {
            $iterations = 5;
            $totalTime = 0;
            
            for ($i = 0; $i < $iterations; $i++) {
                $start = microtime(true);
                $stmt = $this->connection->query($query);
                $results = $stmt->fetchAll();
                $queryTime = microtime(true) - $start;
                $totalTime += $queryTime;
            }
            
            $avgTime = $totalTime / $iterations;
            $this->results['queries'][$description] = [
                'avg_time_ms' => round($avgTime * 1000, 2),
                'iterations' => $iterations,
                'result_count' => count($results)
            ];
            
            echo "  {$description}: " . round($avgTime * 1000, 2) . "ms\n";
            
        } catch (PDOException $e) {
            echo "  {$description}: Error - " . $e->getMessage() . "\n";
        }
    }
    
    /**
     * Test index performance
     */
    private function testIndexPerformance()
    {
        echo "\n📈 Testing Index Performance...\n";
        
        $tables = $this->getTables();
        
        foreach ($tables as $table) {
            $this->analyzeTableIndexes($table);
        }
    }
    
    /**
     * Analyze table indexes
     */
    private function analyzeTableIndexes($table)
    {
        try {
            $stmt = $this->connection->query("SHOW INDEX FROM `{$table}`");
            $indexes = $stmt->fetchAll();
            
            $indexCount = count($indexes);
            $primaryKeys = array_filter($indexes, function($index) {
                return $index['Key_name'] === 'PRIMARY';
            });
            $uniqueIndexes = array_filter($indexes, function($index) {
                return $index['Non_unique'] == 0 && $index['Key_name'] !== 'PRIMARY';
            });
            $regularIndexes = array_filter($indexes, function($index) {
                return $index['Non_unique'] == 1;
            });
            
            $this->results['indexes'][$table] = [
                'total_indexes' => $indexCount,
                'primary_keys' => count($primaryKeys),
                'unique_indexes' => count($uniqueIndexes),
                'regular_indexes' => count($regularIndexes)
            ];
            
            echo "  {$table}: {$indexCount} indexes (PK: " . count($primaryKeys) . 
                 ", Unique: " . count($uniqueIndexes) . 
                 ", Regular: " . count($regularIndexes) . ")\n";
            
        } catch (PDOException $e) {
            echo "  {$table}: Error analyzing indexes - " . $e->getMessage() . "\n";
        }
    }
    
    /**
     * Test JOIN performance
     */
    private function testJoinPerformance()
    {
        echo "\n🔗 Testing JOIN Performance...\n";
        
        $joinQueries = [
            'SELECT u.*, p.name as property_name FROM users u LEFT JOIN properties p ON u.id = p.user_id LIMIT 10' => 'Users with Properties',
            'SELECT t.*, p.name as property_name FROM tenants t LEFT JOIN properties p ON t.property_id = p.id LIMIT 10' => 'Tenants with Properties',
            'SELECT p.*, u.name as user_name FROM payments p LEFT JOIN users u ON p.user_id = u.id LIMIT 10' => 'Payments with Users',
        ];
        
        foreach ($joinQueries as $query => $description) {
            $this->testQuery($query, $description);
        }
    }
    
    /**
     * Test aggregation performance
     */
    private function testAggregationPerformance()
    {
        echo "\n📊 Testing Aggregation Performance...\n";
        
        $aggregationQueries = [
            'SELECT COUNT(*) as total_users FROM users' => 'Count all users',
            'SELECT COUNT(*) as total_properties FROM properties' => 'Count all properties',
            'SELECT SUM(amount) as total_payments FROM payments' => 'Sum all payments',
            'SELECT AVG(amount) as avg_payment FROM payments' => 'Average payment',
            'SELECT MAX(created_at) as latest_payment FROM payments' => 'Latest payment',
            'SELECT MIN(created_at) as earliest_payment FROM payments' => 'Earliest payment',
        ];
        
        foreach ($aggregationQueries as $query => $description) {
            $this->testQuery($query, $description);
        }
    }
    
    /**
     * Analyze slow queries
     */
    private function analyzeSlowQueries()
    {
        echo "\n🐌 Analyzing Slow Query Log...\n";
        
        try {
            // Check if slow query log is enabled
            $stmt = $this->connection->query("SHOW VARIABLES LIKE 'slow_query_log'");
            $slowLogEnabled = $stmt->fetch()['Value'] ?? 'OFF';
            
            if ($slowLogEnabled === 'ON') {
                $stmt = $this->connection->query("SHOW VARIABLES LIKE 'long_query_time'");
                $longQueryTime = $stmt->fetch()['Value'] ?? '10';
                
                echo "Slow query log is enabled (threshold: {$longQueryTime}s)\n";
                $this->results['slow_queries'] = [
                    'enabled' => true,
                    'threshold_seconds' => $longQueryTime
                ];
            } else {
                echo "Slow query log is disabled\n";
                $this->results['slow_queries'] = [
                    'enabled' => false,
                    'recommendation' => 'Enable slow query log for better performance monitoring'
                ];
            }
            
        } catch (PDOException $e) {
            echo "Could not analyze slow query log: " . $e->getMessage() . "\n";
        }
    }
    
    /**
     * Generate performance report
     */
    private function generateReport()
    {
        echo "\n" . str_repeat("=", 60) . "\n";
        echo "📊 DATABASE PERFORMANCE REPORT\n";
        echo str_repeat("=", 60) . "\n";
        echo "Test Date: " . date('Y-m-d H:i:s') . "\n";
        echo "Database: " . ($_ENV['DB_DATABASE'] ?? 'Unknown') . "\n";
        echo "Host: " . ($_ENV['DB_HOST'] ?? 'Unknown') . "\n";
        echo "\n";
        
        // Connection Performance
        if (isset($this->results['connection'])) {
            echo "CONNECTION PERFORMANCE:\n";
            echo str_repeat("-", 30) . "\n";
            echo "Average Connection Time: " . $this->results['connection']['avg_time_ms'] . "ms\n";
            echo "Test Iterations: " . $this->results['connection']['iterations'] . "\n\n";
        }
        
        // Table Performance
        if (isset($this->results['tables'])) {
            echo "TABLE PERFORMANCE:\n";
            echo str_repeat("-", 30) . "\n";
            foreach ($this->results['tables'] as $table => $stats) {
                echo sprintf("%-20s: %d rows, %.2fMB, %sms\n", 
                    $table, 
                    $stats['row_count'], 
                    $stats['size_mb'], 
                    $stats['count_time_ms']
                );
            }
            echo "\n";
        }
        
        // Query Performance
        if (isset($this->results['queries'])) {
            echo "QUERY PERFORMANCE:\n";
            echo str_repeat("-", 30) . "\n";
            foreach ($this->results['queries'] as $query => $stats) {
                echo sprintf("%-30s: %sms\n", $query, $stats['avg_time_ms']);
            }
            echo "\n";
        }
        
        // Index Analysis
        if (isset($this->results['indexes'])) {
            echo "INDEX ANALYSIS:\n";
            echo str_repeat("-", 30) . "\n";
            foreach ($this->results['indexes'] as $table => $stats) {
                echo sprintf("%-20s: %d total indexes\n", $table, $stats['total_indexes']);
            }
            echo "\n";
        }
        
        // Recommendations
        $this->generateRecommendations();
    }
    
    /**
     * Generate performance recommendations
     */
    private function generateRecommendations()
    {
        echo "RECOMMENDATIONS:\n";
        echo str_repeat("-", 30) . "\n";
        
        $recommendations = [
            "Add indexes on frequently queried columns",
            "Use EXPLAIN to analyze query execution plans",
            "Enable slow query log for monitoring",
            "Consider using database connection pooling",
            "Optimize queries to avoid N+1 problems",
            "Use appropriate data types for columns",
            "Regularly analyze and optimize table statistics",
            "Consider partitioning for large tables",
            "Use prepared statements for better performance",
            "Monitor database performance regularly",
            "Consider read replicas for read-heavy workloads",
            "Use database caching where appropriate",
        ];
        
        foreach ($recommendations as $i => $recommendation) {
            echo ($i + 1) . ". {$recommendation}\n";
        }
        
        echo "\n✅ Database performance testing completed!\n";
    }
}

// Run the database performance test
try {
    $tester = new DatabasePerformanceTester();
    $tester->runAllTests();
} catch (Exception $e) {
    echo "❌ Error during database performance testing: " . $e->getMessage() . "\n";
    echo "Please ensure your database is properly configured and accessible.\n";
}

