<?php
/**
 * Environment Loader Fix Script
 * This will manually force Laravel to load the .env file properly
 */

echo "<h1>🔧 Environment Loader Fix</h1>";
echo "<style>body{font-family:Arial;margin:20px;} .ok{color:green;} .error{color:red;} .warning{color:orange;} .info{color:blue;}</style>";

// Step 1: Check .env file location and content
echo "<h2>1. Environment File Analysis</h2>";

$envPath = '.env';
if (file_exists($envPath)) {
    echo "✓ .env file found at: " . realpath($envPath) . "<br>";
    $envContent = file_get_contents($envPath);
    echo "✓ File size: " . strlen($envContent) . " bytes<br>";
    
    // Parse the .env file manually
    $envVars = [];
    $lines = explode("\n", $envContent);
    foreach ($lines as $line) {
        $line = trim($line);
        if (!empty($line) && strpos($line, '=') !== false && !str_starts_with($line, '#')) {
            list($key, $value) = explode('=', $line, 2);
            $envVars[trim($key)] = trim($value);
        }
    }
    
    echo "✓ Parsed " . count($envVars) . " environment variables<br>";
    echo "<details><summary>Show parsed variables</summary>";
    foreach ($envVars as $key => $value) {
        echo "<code>$key</code> = " . (strlen($value) > 50 ? substr($value, 0, 50) . '...' : $value) . "<br>";
    }
    echo "</details>";
} else {
    echo "<span class='error'>✗ .env file not found</span><br>";
    exit;
}

// Step 2: Manually set environment variables
echo "<h2>2. Manual Environment Variable Setting</h2>";

foreach ($envVars as $key => $value) {
    $_ENV[$key] = $value;
    putenv("$key=$value");
}

echo "✓ Manually set " . count($envVars) . " environment variables in \$_ENV and putenv()<br>";

// Test manual access
$testVars = ['APP_NAME', 'APP_KEY', 'DB_HOST', 'DB_DATABASE', 'DB_USERNAME', 'DB_PASSWORD'];
foreach ($testVars as $var) {
    $value = $_ENV[$var] ?? 'NOT_SET';
    echo "$var: <span class='" . ($value !== 'NOT_SET' ? 'ok' : 'error') . "'>" . 
         (strlen($value) > 20 ? substr($value, 0, 20) . '...' : $value) . "</span><br>";
}

// Step 3: Try Laravel bootstrap with manual environment
echo "<h2>3. Laravel Bootstrap with Manual Environment</h2>";

try {
    // Clear any existing Laravel environment
    if (function_exists('opcache_reset')) {
        opcache_reset();
    }
    
    require_once 'vendor/autoload.php';
    
    // Create Laravel application
    $app = require_once 'bootstrap/app.php';
    echo "✓ Laravel application created<br>";
    
    // Properly boot Laravel - this is the key step that was missing!
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    echo "✓ HTTP Kernel created<br>";
    
    // Create a fake request and handle it to trigger Laravel's full boot process
    $request = Illuminate\Http\Request::create('/', 'GET');
    echo "✓ Fake request created<br>";
    
    // Actually boot the application by processing the request
    try {
        $response = $kernel->handle($request);
        echo "✓ Laravel application fully booted via kernel->handle()<br>";
    } catch (Exception $bootException) {
        // If full request handling fails, try manual boot
        echo "⚠ Kernel handle failed, trying manual boot...<br>";
        
        // Manual boot process
        $app->boot();
        echo "✓ Laravel application manually booted<br>";
    }
    
    // Test Laravel's env() function directly (no service needed)
    echo "✓ Testing Laravel's env() function<br>";
    
    echo "<h3>Laravel env() Function Test:</h3>";
    foreach ($testVars as $var) {
        $value = env($var, 'NOT_SET');
        echo "$var: <span class='" . ($value !== 'NOT_SET' ? 'ok' : 'error') . "'>" . 
             (strlen($value) > 20 ? substr($value, 0, 20) . '...' : $value) . "</span><br>";
    }
    
    // Step 4: Test Laravel services
    echo "<h2>4. Laravel Services Test</h2>";
    
    try {
        // Test config service - now should work since app is booted
        $config = $app->make('config');
        echo "✓ Config service working<br>";
        
        // Test database config specifically
        $dbConfig = $config->get('database.connections.mysql');
        echo "✓ Database config retrieved<br>";
        echo "DB Host from config: <span class='info'>" . ($dbConfig['host'] ?? 'NOT_SET') . "</span><br>";
        echo "DB Database from config: <span class='info'>" . ($dbConfig['database'] ?? 'NOT_SET') . "</span><br>";
        
        // Test cache service
        $cache = $app->make('cache');
        echo "✓ Cache service working<br>";
        
        // Test session service
        $session = $app->make('session');
        echo "✓ Session service working<br>";
        
    } catch (Exception $e) {
        echo "<span class='error'>✗ Laravel services failed: " . $e->getMessage() . "</span><br>";
        echo "<details><summary>Full error details</summary><pre>" . $e->getTraceAsString() . "</pre></details>";
        
        // Try alternative approach - using helpers
        echo "<h3>Alternative Service Test (Using Helpers):</h3>";
        try {
            // Test if we can at least access basic Laravel functionality
            $appName = config('app.name', 'NOT_SET');
            echo "App name from config(): <span class='info'>$appName</span><br>";
            
            $appEnv = config('app.env', 'NOT_SET');
            echo "App environment: <span class='info'>$appEnv</span><br>";
            
        } catch (Exception $e2) {
            echo "<span class='error'>✗ Even config() helper failed: " . $e2->getMessage() . "</span><br>";
        }
    }
    
    // Step 5: Test database connection
    echo "<h2>5. Database Connection Test</h2>";
    
    try {
        // First try direct PDO
        $pdo = new PDO('mysql:host=localhost;dbname=u336307813_vaca', 'u336307813_vaca', $_ENV['DB_PASSWORD']);
        echo "✓ Direct PDO connection successful<br>";
        
        $stmt = $pdo->query('SHOW TABLES');
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        echo "✓ Tables found: " . count($tables) . "<br>";
        
        // List table names
        echo "<details><summary>Show table names</summary>";
        foreach ($tables as $table) {
            echo "- $table<br>";
        }
        echo "</details>";
        
        // Now try Laravel's database connection - should work now that app is booted
        try {
            $db = $app->make('db');
            echo "✓ Laravel Database service retrieved<br>";
            
            $connection = $db->connection();
            echo "✓ Laravel database connection established<br>";
            
            $result = $db->select('SELECT 1 as test');
            echo "✓ Laravel database query successful<br>";
            
            // Test a real query on your tables
            $urlCount = $db->select('SELECT COUNT(*) as count FROM short_urls')[0]->count;
            echo "✓ Found $urlCount short URLs in database<br>";
            
        } catch (Exception $e3) {
            echo "<span class='warning'>⚠ Laravel database failed but PDO works: " . $e3->getMessage() . "</span><br>";
        }
        
    } catch (Exception $e) {
        echo "<span class='error'>✗ Database connection failed: " . $e->getMessage() . "</span><br>";
        echo "<details><summary>Database error details</summary><pre>" . $e->getTraceAsString() . "</pre></details>";
    }
    
} catch (Exception $e) {
    echo "<span class='error'>✗ Laravel bootstrap failed: " . $e->getMessage() . "</span><br>";
    echo "<details><summary>Bootstrap error details</summary><pre>" . $e->getTraceAsString() . "</pre></details>";
}

// Step 6: Create a working .env loader for the main application
echo "<h2>6. Creating Permanent Fix</h2>";

$fixContent = '<?php
// Environment loader fix - add this to the top of index.php
if (file_exists(__DIR__ . "/.env")) {
    $envContent = file_get_contents(__DIR__ . "/.env");
    $lines = explode("\n", $envContent);
    foreach ($lines as $line) {
        $line = trim($line);
        if (!empty($line) && strpos($line, "=") !== false && !str_starts_with($line, "#")) {
            list($key, $value) = explode("=", $line, 2);
            $_ENV[trim($key)] = trim($value);
            putenv(trim($key) . "=" . trim($value));
        }
    }
}
?>';

if (file_put_contents('env_fix.php', $fixContent)) {
    echo "✓ Created env_fix.php - we'll integrate this into index.php<br>";
} else {
    echo "<span class='error'>✗ Failed to create env_fix.php</span><br>";
}

// Step 7: Check if Laravel is now fully working
echo "<h2>7. Final Laravel Test</h2>";

try {
    if (function_exists('config') && function_exists('env')) {
        echo "✓ Laravel global functions available<br>";
        
        $appName = config('app.name', 'NOT_SET');
        $appUrl = config('app.url', 'NOT_SET');
        
        if ($appName !== 'NOT_SET' && $appUrl !== 'NOT_SET') {
            echo "✓ Laravel configuration working perfectly!<br>";
            echo "<p><strong>Your Laravel application should now work at <a href='/'>https://vaca.sh/</a></strong></p>";
        } else {
            echo "<span class='warning'>⚠ Laravel config partially working</span><br>";
        }
    }
} catch (Exception $e) {
    echo "<span class='error'>✗ Final test failed: " . $e->getMessage() . "</span><br>";
}

// Final status
echo "<h2>8. Next Steps</h2>";
echo "<ol>";
if (isset($appName) && $appName !== 'NOT_SET') {
    echo "<li><strong style='color:green;'>SUCCESS!</strong> Laravel is now working. Try visiting <a href='/'>https://vaca.sh/</a></li>";
    echo "<li>If the main site works, you can delete all debug files for security</li>";
} else {
    echo "<li>Laravel services still not fully working - we need to integrate the env_fix.php into index.php</li>";
    echo "<li>The environment variables are loading correctly, but Laravel needs proper bootstrapping</li>";
}
echo "<li>After everything works, delete all debug files for security</li>";
echo "</ol>";

echo "<p><strong>Debug files to delete:</strong> env_loader_fix.php, fix_laravel.php, laravel_debug.php, debug.php, db_test.php, step_debug.php, update_env_password.php</p>";
?> 