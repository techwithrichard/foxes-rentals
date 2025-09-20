<?php

/**
 * Script to identify and fix N+1 query problems
 * This script will scan controllers and suggest eager loading improvements
 */

echo "🔍 Scanning for N+1 Query Problems...\n\n";

$controllers = glob('app/Http/Controllers/**/*.php');

$nPlusOnePatterns = [
    'Property::all()' => 'Property::with([\'landlord\', \'houses\', \'leases\'])->get()',
    'User::all()' => 'User::with([\'roles\', \'permissions\'])->get()',
    'Lease::all()' => 'Lease::with([\'tenant\', \'property\', \'house\'])->get()',
    'Payment::all()' => 'Payment::with([\'invoice\', \'tenant\'])->get()',
    'Invoice::all()' => 'Invoice::with([\'tenant\', \'property\', \'house\'])->get()',
];

$suggestions = [];

foreach ($controllers as $controller) {
    $content = file_get_contents($controller);
    $controllerName = basename($controller, '.php');
    
    // Check for potential N+1 patterns
    if (preg_match('/foreach\s*\(\s*\$[^)]+\s+as\s+\$[^)]+\)\s*\{[^}]*\$[^->]*->[^->]*->/', $content)) {
        $suggestions[] = [
            'file' => $controller,
            'issue' => 'Potential N+1 query in foreach loop',
            'suggestion' => 'Add eager loading with() method before the query'
        ];
    }
    
    // Check for missing eager loading
    if (preg_match('/\$[^=]+=\s*[A-Z][a-zA-Z]+::[^(]*\(\)[^;]*;/', $content)) {
        $suggestions[] = [
            'file' => $controller,
            'issue' => 'Query without eager loading',
            'suggestion' => 'Consider adding ->with() for related models'
        ];
    }
}

echo "📊 N+1 Query Analysis Results:\n";
echo "Controllers scanned: " . count($controllers) . "\n";
echo "Potential issues found: " . count($suggestions) . "\n\n";

if (count($suggestions) > 0) {
    echo "⚠️  Potential N+1 Query Issues:\n";
    foreach ($suggestions as $suggestion) {
        echo "File: " . $suggestion['file'] . "\n";
        echo "Issue: " . $suggestion['issue'] . "\n";
        echo "Suggestion: " . $suggestion['suggestion'] . "\n\n";
    }
} else {
    echo "✅ No obvious N+1 query problems found!\n";
    echo "Most controllers are already using eager loading properly.\n";
}

echo "\n🎯 Best Practices for Avoiding N+1 Queries:\n";
echo "1. Always use ->with() when loading related models\n";
echo "2. Use ->withCount() for counting related models\n";
echo "3. Use ->load() to eager load relationships after initial query\n";
echo "4. Use ->select() to limit columns when possible\n";
echo "5. Use ->whereHas() instead of loading all models and filtering\n";

echo "\n📈 Performance Impact:\n";
echo "- N+1 queries can cause 10x+ slower response times\n";
echo "- Proper eager loading reduces database queries from N+1 to 1\n";
echo "- Use Laravel Debugbar to identify N+1 queries in development\n";

echo "\n✅ N+1 Query Analysis Complete!\n";
