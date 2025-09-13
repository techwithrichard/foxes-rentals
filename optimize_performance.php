<?php
/**
 * Foxes Rental Management System - Performance Optimization Script
 * 
 * This script applies various performance optimizations to improve site speed
 */

echo "🚀 Starting Performance Optimization for Foxes Rental Management System\n";
echo str_repeat("=", 60) . "\n";

// 1. Enable OPcache (if available)
echo "1. Checking OPcache status...\n";
if (function_exists('opcache_get_status')) {
    $opcacheStatus = opcache_get_status();
    if ($opcacheStatus && $opcacheStatus['opcache_enabled']) {
        echo "   ✅ OPcache is enabled\n";
        echo "   📊 OPcache hit rate: " . round($opcacheStatus['opcache_statistics']['opcache_hit_rate'], 2) . "%\n";
    } else {
        echo "   ⚠️  OPcache is disabled - Enable it in php.ini for better performance\n";
        echo "   💡 Add these lines to php.ini:\n";
        echo "      opcache.enable=1\n";
        echo "      opcache.memory_consumption=128\n";
        echo "      opcache.max_accelerated_files=4000\n";
        echo "      opcache.revalidate_freq=60\n";
    }
} else {
    echo "   ❌ OPcache extension not available\n";
}

// 2. Check Redis availability
echo "\n2. Checking Redis availability...\n";
if (class_exists('Redis')) {
    try {
        $redis = new Redis();
        $redis->connect('127.0.0.1', 6379);
        $redis->ping();
        echo "   ✅ Redis is available and running\n";
        echo "   📊 Redis info: " . $redis->info('memory')['used_memory_human'] . " used\n";
        $redis->close();
    } catch (Exception $e) {
        echo "   ⚠️  Redis not available: " . $e->getMessage() . "\n";
        echo "   💡 Install Redis for better caching performance\n";
    }
} else {
    echo "   ⚠️  Redis extension not available\n";
    echo "   💡 Install Redis PHP extension for better caching performance\n";
}

// 3. Check database connection
echo "\n3. Checking database connection...\n";
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
    $pdo = new PDO($dsn, $username, $password);
    
    echo "   ✅ Database connection successful\n";
    
    // Check for slow queries
    $stmt = $pdo->query("SHOW VARIABLES LIKE 'slow_query_log'");
    $slowLog = $stmt->fetch();
    if ($slowLog && $slowLog['Value'] === 'ON') {
        echo "   ✅ Slow query log is enabled\n";
    } else {
        echo "   ⚠️  Slow query log is disabled - Enable for performance monitoring\n";
    }
    
} catch (Exception $e) {
    echo "   ❌ Database connection failed: " . $e->getMessage() . "\n";
}

// 4. Check file permissions
echo "\n4. Checking file permissions...\n";
$directories = [
    'storage/framework/cache',
    'storage/framework/sessions',
    'storage/framework/views',
    'bootstrap/cache'
];

foreach ($directories as $dir) {
    if (is_dir($dir) && is_writable($dir)) {
        echo "   ✅ {$dir} is writable\n";
    } else {
        echo "   ⚠️  {$dir} is not writable - Fix permissions for better performance\n";
    }
}

// 5. Check asset optimization
echo "\n5. Checking asset optimization...\n";
$assetDirs = [
    'public/assets',
    'public/build',
    'resources/css',
    'resources/js'
];

foreach ($assetDirs as $dir) {
    if (is_dir($dir)) {
        $files = glob($dir . '/*.{css,js}', GLOB_BRACE);
        $totalSize = 0;
        foreach ($files as $file) {
            $totalSize += filesize($file);
        }
        echo "   📁 {$dir}: " . count($files) . " files, " . round($totalSize / 1024, 2) . "KB\n";
    }
}

// 6. Performance recommendations
echo "\n6. Performance Recommendations:\n";
echo str_repeat("-", 40) . "\n";

$recommendations = [
    "Enable OPcache in php.ini for 30-50% performance improvement",
    "Install and configure Redis for caching (20-30% improvement)",
    "Run 'php artisan optimize' for Laravel optimizations",
    "Run 'php artisan config:cache' to cache configuration",
    "Run 'php artisan route:cache' to cache routes",
    "Run 'php artisan view:cache' to cache views",
    "Use 'yarn build' to optimize frontend assets",
    "Enable gzip compression in web server",
    "Set up browser caching headers",
    "Optimize database queries and add indexes",
    "Use CDN for static assets",
    "Implement database query result caching",
    "Enable HTTP/2 for better performance",
    "Optimize images (use WebP format)",
    "Minify CSS and JavaScript files"
];

foreach ($recommendations as $i => $recommendation) {
    echo ($i + 1) . ". {$recommendation}\n";
}

echo "\n✅ Performance optimization analysis completed!\n";
echo "\n💡 Next steps:\n";
echo "   1. Run 'php artisan optimize' to apply Laravel optimizations\n";
echo "   2. Install Redis and update CACHE_DRIVER=redis in .env\n";
echo "   3. Enable OPcache in php.ini\n";
echo "   4. Run 'yarn build' to optimize frontend assets\n";
echo "   5. Test performance with 'php performance_test.php'\n";
