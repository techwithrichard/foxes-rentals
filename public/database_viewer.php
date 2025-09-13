<?php
/**
 * Foxes Rental Management System - Database Viewer
 * Access this at: http://localhost:8000/database_viewer.php
 */

// Load Laravel environment
require_once '../vendor/autoload.php';
$app = require_once '../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Foxes Rental - Database Viewer</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { background: #2c3e50; color: white; padding: 20px; border-radius: 8px; margin-bottom: 20px; }
        .table-info { background: #ecf0f1; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .table-structure { border-collapse: collapse; width: 100%; margin: 10px 0; }
        .table-structure th, .table-structure td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .table-structure th { background-color: #3498db; color: white; }
        .table-data { border-collapse: collapse; width: 100%; margin: 10px 0; font-size: 12px; }
        .table-data th, .table-data td { border: 1px solid #ddd; padding: 4px; text-align: left; }
        .table-data th { background-color: #e74c3c; color: white; }
        .success { color: #27ae60; font-weight: bold; }
        .warning { color: #f39c12; font-weight: bold; }
        .error { color: #e74c3c; font-weight: bold; }
        .stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin: 20px 0; }
        .stat-card { background: #3498db; color: white; padding: 15px; border-radius: 5px; text-align: center; }
        .stat-number { font-size: 24px; font-weight: bold; }
        .nav { background: #34495e; padding: 10px; border-radius: 5px; margin-bottom: 20px; }
        .nav a { color: white; text-decoration: none; margin-right: 15px; padding: 5px 10px; border-radius: 3px; }
        .nav a:hover { background: #2c3e50; }
        .section { margin: 30px 0; padding: 20px; border: 1px solid #ddd; border-radius: 5px; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h1>🏠 Foxes Rental Management System</h1>
            <p>Database Viewer & System Analysis</p>
        </div>";

try {
    $db = DB::connection();
    $tables = DB::select('SHOW TABLES');
    $tableNames = array_map(function($table) { return array_values((array)$table)[0]; }, $tables);
    
    echo "<div class='nav'>";
    echo "<a href='#overview'>Overview</a>";
    echo "<a href='#tables'>All Tables</a>";
    echo "<a href='#key-tables'>Key Tables</a>";
    echo "<a href='#migrations'>Migrations</a>";
    echo "<a href='#analysis'>System Analysis</a>";
    echo "</div>";
    
    // Overview Section
    echo "<div id='overview' class='section'>";
    echo "<h2>📊 System Overview</h2>";
    echo "<div class='stats'>";
    
    $totalRecords = 0;
    $keyTables = ['users', 'properties', 'houses', 'leases', 'invoices', 'payments', 'c2b_requests', 'stk_requests'];
    
    foreach ($keyTables as $table) {
        if (in_array($table, $tableNames)) {
            $count = DB::table($table)->count();
            $totalRecords += $count;
            echo "<div class='stat-card'>";
            echo "<div class='stat-number'>$count</div>";
            echo "<div>" . ucfirst(str_replace('_', ' ', $table)) . "</div>";
            echo "</div>";
        }
    }
    
    echo "<div class='stat-card'>";
    echo "<div class='stat-number'>" . count($tableNames) . "</div>";
    echo "<div>Total Tables</div>";
    echo "</div>";
    
    echo "<div class='stat-card'>";
    echo "<div class='stat-number'>$totalRecords</div>";
    echo "<div>Total Records</div>";
    echo "</div>";
    
    echo "</div>";
    echo "</div>";
    
    // Key Tables Analysis
    echo "<div id='key-tables' class='section'>";
    echo "<h2>🔑 Key Tables Analysis</h2>";
    
    foreach ($keyTables as $table) {
        if (in_array($table, $tableNames)) {
            $count = DB::table($table)->count();
            echo "<div class='table-info'>";
            echo "<h3>$table ($count records)</h3>";
            
            // Get sample data
            if ($count > 0) {
                $sample = DB::table($table)->limit(3)->get();
                if ($sample->count() > 0) {
                    echo "<table class='table-data'>";
                    echo "<tr>";
                    $firstRow = $sample->first();
                    foreach (array_keys((array)$firstRow) as $column) {
                        echo "<th>$column</th>";
                    }
                    echo "</tr>";
                    foreach ($sample as $row) {
                        echo "<tr>";
                        foreach ($row as $value) {
                            $display = $value === null ? 'NULL' : substr($value, 0, 50);
                            echo "<td>$display</td>";
                        }
                        echo "</tr>";
                    }
                    echo "</table>";
                }
            } else {
                echo "<p class='warning'>⚠️ Table is empty</p>";
            }
            echo "</div>";
        } else {
            echo "<div class='table-info'>";
            echo "<h3>$table</h3>";
            echo "<p class='error'>❌ Table missing - this may indicate incomplete migration</p>";
            echo "</div>";
        }
    }
    echo "</div>";
    
    // All Tables Section
    echo "<div id='tables' class='section'>";
    echo "<h2>📋 All Database Tables</h2>";
    
    foreach ($tableNames as $table) {
        $count = DB::table($table)->count();
        echo "<div class='table-info'>";
        echo "<h4>$table <span class='success'>($count records)</span></h4>";
        
        // Get table structure
        $columns = DB::select("DESCRIBE `$table`");
        echo "<table class='table-structure'>";
        echo "<tr><th>Column</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
        foreach ($columns as $column) {
            echo "<tr>";
            echo "<td>{$column->Field}</td>";
            echo "<td>{$column->Type}</td>";
            echo "<td>{$column->Null}</td>";
            echo "<td>{$column->Key}</td>";
            echo "<td>" . ($column->Default ?? 'NULL') . "</td>";
            echo "<td>{$column->Extra}</td>";
            echo "</tr>";
        }
        echo "</table>";
        echo "</div>";
    }
    echo "</div>";
    
    // Migrations Section
    echo "<div id='migrations' class='section'>";
    echo "<h2>📝 Migration Status</h2>";
    
    if (in_array('migrations', $tableNames)) {
        $migrations = DB::table('migrations')->orderBy('batch', 'desc')->orderBy('id', 'desc')->get();
        $batchCounts = DB::table('migrations')->select('batch', DB::raw('count(*) as count'))->groupBy('batch')->get();
        
        echo "<div class='table-info'>";
        echo "<h3>Migration Batches</h3>";
        foreach ($batchCounts as $batch) {
            echo "<p>Batch {$batch->batch}: {$batch->count} migrations</p>";
        }
        echo "</div>";
        
        echo "<div class='table-info'>";
        echo "<h3>Recent Migrations</h3>";
        echo "<table class='table-structure'>";
        echo "<tr><th>Migration</th><th>Batch</th><th>Applied At</th></tr>";
        foreach ($migrations->take(10) as $migration) {
            echo "<tr>";
            echo "<td>{$migration->migration}</td>";
            echo "<td>{$migration->batch}</td>";
            echo "<td>Recently</td>";
            echo "</tr>";
        }
        echo "</table>";
        echo "</div>";
    } else {
        echo "<p class='error'>❌ No migrations table found</p>";
    }
    echo "</div>";
    
    // System Analysis
    echo "<div id='analysis' class='section'>";
    echo "<h2>🔍 System Analysis</h2>";
    
    echo "<div class='table-info'>";
    echo "<h3>System Status</h3>";
    
    $issues = [];
    $warnings = [];
    
    // Check for empty key tables
    foreach ($keyTables as $table) {
        if (in_array($table, $tableNames)) {
            $count = DB::table($table)->count();
            if ($count === 0) {
                $warnings[] = "$table table is empty";
            }
        } else {
            $issues[] = "$table table is missing";
        }
    }
    
    // Check MPesa tables
    if (in_array('c2b_requests', $tableNames)) {
        $c2bCount = DB::table('c2b_requests')->count();
        if ($c2bCount === 0) {
            $warnings[] = "No C2B payment requests found";
        }
    }
    
    if (in_array('stk_requests', $tableNames)) {
        $stkCount = DB::table('stk_requests')->count();
        if ($stkCount === 0) {
            $warnings[] = "No STK payment requests found";
        }
    }
    
    if (empty($issues) && empty($warnings)) {
        echo "<p class='success'>✅ System appears to be properly configured</p>";
    } else {
        if (!empty($issues)) {
            echo "<h4 class='error'>Critical Issues:</h4>";
            foreach ($issues as $issue) {
                echo "<p class='error'>❌ $issue</p>";
            }
        }
        
        if (!empty($warnings)) {
            echo "<h4 class='warning'>Warnings:</h4>";
            foreach ($warnings as $warning) {
                echo "<p class='warning'>⚠️ $warning</p>";
            }
        }
    }
    
    echo "<h4>Recommendations:</h4>";
    echo "<ul>";
    echo "<li>Ensure all MPesa environment variables are configured</li>";
    echo "<li>Run <code>php artisan migrate</code> if any tables are missing</li>";
    echo "<li>Check MPesa API credentials and callback URLs</li>";
    echo "<li>Test payment flows with sample data</li>";
    echo "</ul>";
    
    echo "</div>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div class='error'>";
    echo "<h2>❌ Database Connection Error</h2>";
    echo "<p>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<h3>Troubleshooting Steps:</h3>";
    echo "<ol>";
    echo "<li>Check if MySQL server is running</li>";
    echo "<li>Verify database credentials in .env file</li>";
    echo "<li>Make sure the database exists</li>";
    echo "<li>Run: <code>php artisan migrate</code></li>";
    echo "</ol>";
    echo "</div>";
}

echo "</div></body></html>";
?>
