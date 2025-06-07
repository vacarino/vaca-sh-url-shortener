<?php
/**
 * Test Script to Verify Main Application Fix
 * This will check if the environment loader fix is working in the main app
 */

echo "<h1>🔧 Main Application Test</h1>";
echo "<style>body{font-family:Arial;margin:20px;} .ok{color:green;} .error{color:red;} .warning{color:orange;} .info{color:blue;}</style>";

// Step 1: Test if environment variables are loaded
echo "<h2>1. Environment Variables Test</h2>";

// Test the same env loading code that should be in index.php
if (file_exists(__DIR__ . "/.env")) {
    echo "✓ .env file exists<br>";
    $envContent = file_get_contents(__DIR__ . "/.env");
    $lines = explode("\n", $envContent);
    $envCount = 0;
    foreach ($lines as $line) {
        $line = trim($line);
        if (!empty($line) && strpos($line, "=") !== false && !str_starts_with($line, "#")) {
            list($key, $value) = explode("=", $line, 2);
            $_ENV[trim($key)] = trim($value);
            putenv(trim($key) . "=" . trim($value));
            $envCount++;
        }
    }
    echo "✓ Loaded $envCount environment variables<br>";
} else {
    echo "<span class='error'>✗ .env file not found</span><br>";
}

// Test key environment variables
$testVars = ['APP_NAME', 'APP_KEY', 'DB_HOST', 'DB_DATABASE', 'DB_USERNAME'];
foreach ($testVars as $var) {
    $value = $_ENV[$var] ?? 'NOT_SET';
    echo "$var: <span class='" . ($value !== 'NOT_SET' ? 'ok' : 'error') . "'>" . 
         (strlen($value) > 20 ? substr($value, 0, 20) . '...' : $value) . "</span><br>";
}

echo "<h2>2. Check Index.php File</h2>";

// Check if index.php contains our fix
if (file_exists('index.php')) {
    echo "✓ index.php exists<br>";
    $indexContent = file_get_contents('index.php');
    
    if (strpos($indexContent, 'Environment Loader Fix') !== false) {
        echo "✓ Environment Loader Fix found in index.php<br>";
    } else {
        echo "<span class='error'>✗ Environment Loader Fix NOT found in index.php</span><br>";
        echo "<span class='warning'>The fix may not have been uploaded properly</span><br>";
    }
    
    // Show first few lines of index.php
    $lines = explode("\n", $indexContent);
    echo "<h3>First 10 lines of index.php:</h3>";
    echo "<pre>";
    for ($i = 0; $i < min(10, count($lines)); $i++) {
        echo htmlspecialchars($lines[$i]) . "\n";
    }
    echo "</pre>";
    
} else {
    echo "<span class='error'>✗ index.php not found</span><br>";
}

echo "<h2>3. Test Laravel Bootstrap (Minimal)</h2>";

try {
    // Try to load Laravel with our environment fix
    require_once 'vendor/autoload.php';
    echo "✓ Autoloader loaded<br>";
    
    $app = require_once 'bootstrap/app.php';
    echo "✓ Laravel app created<br>";
    
    // Test environment access
    if (function_exists('env')) {
        $appName = env('APP_NAME', 'NOT_SET');
        echo "APP_NAME via env(): <span class='" . ($appName !== 'NOT_SET' ? 'ok' : 'error') . "'>$appName</span><br>";
    } else {
        echo "<span class='error'>✗ env() function not available</span><br>";
    }
    
} catch (Throwable $e) {
    echo "<span class='error'>✗ Laravel bootstrap failed: " . $e->getMessage() . "</span><br>";
    echo "<details><summary>Full error</summary><pre>" . $e->getTraceAsString() . "</pre></details>";
}

echo "<h2>4. Check Server Logs</h2>";

// Try to find error logs
$logLocations = [
    'storage/logs/laravel.log',
    'storage/logs',
    '/var/log/apache2/error.log',
    '/var/log/nginx/error.log'
];

foreach ($logLocations as $logPath) {
    if (file_exists($logPath)) {
        echo "Found log: $logPath<br>";
        if (is_file($logPath)) {
            $logContent = file_get_contents($logPath);
            if (!empty(trim($logContent))) {
                echo "<details><summary>Recent log entries</summary><pre>" . 
                     htmlspecialchars(substr($logContent, -2000)) . "</pre></details>";
            } else {
                echo "Log file is empty<br>";
            }
        } else {
            echo "$logPath is a directory<br>";
        }
    }
}

echo "<h2>5. Direct Database Test</h2>";

try {
    $pdo = new PDO('mysql:host=localhost;dbname=u336307813_vaca', 'u336307813_vaca', $_ENV['DB_PASSWORD'] ?? 'password_not_set');
    echo "✓ Direct database connection works<br>";
    
    $stmt = $pdo->query('SELECT COUNT(*) as count FROM short_urls');
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "✓ Found {$result['count']} short URLs<br>";
    
} catch (Exception $e) {
    echo "<span class='error'>✗ Database connection failed: " . $e->getMessage() . "</span><br>";
}

echo "<h2>6. Recommendations</h2>";

if (strpos(file_get_contents('index.php') ?? '', 'Environment Loader Fix') === false) {
    echo "<p><strong style='color:red;'>ISSUE FOUND:</strong> The Environment Loader Fix is missing from index.php</p>";
    echo "<p><strong>Solution:</strong> Re-upload the vaca.sh.zip file and make sure to extract/replace all files</p>";
} else {
    echo "<p>The Environment Loader Fix is present in index.php. There may be another issue.</p>";
    echo "<p>Check the Laravel log files or contact hosting support for server error logs.</p>";
}

?> 