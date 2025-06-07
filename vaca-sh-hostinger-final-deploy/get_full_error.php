<?php
/**
 * Get Full Laravel Error and Test Routing
 * This will show the complete error message and test if Laravel routing works
 */

echo "<h1>🔍 Full Error Analysis</h1>";
echo "<style>body{font-family:Arial;margin:20px;} .ok{color:green;} .error{color:red;} .warning{color:orange;} .info{color:blue;}</style>";

// Load environment variables first
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

echo "<h2>1. Complete Laravel Error Log</h2>";

$logFile = 'storage/logs/laravel.log';
if (file_exists($logFile)) {
    $logContent = file_get_contents($logFile);
    
    // Get the last 5000 characters to see recent errors
    $recentLog = substr($logContent, -5000);
    
    echo "<h3>Recent Laravel Log (Last 5000 chars):</h3>";
    echo "<div style='background:#f5f5f5; padding:10px; max-height:400px; overflow-y:scroll;'>";
    echo "<pre>" . htmlspecialchars($recentLog) . "</pre>";
    echo "</div>";
    
    // Try to extract the actual error message
    $lines = explode("\n", $recentLog);
    foreach ($lines as $line) {
        if (strpos($line, 'ERROR') !== false || strpos($line, 'CRITICAL') !== false || strpos($line, 'Exception') !== false) {
            echo "<h3>Found Error Line:</h3>";
            echo "<p style='background:#ffe6e6; padding:10px; border-left:4px solid red;'>" . htmlspecialchars($line) . "</p>";
            break;
        }
    }
} else {
    echo "No Laravel log file found<br>";
}

echo "<h2>2. Test Laravel Routing Manually</h2>";

try {
    // Load Laravel
    require_once 'vendor/autoload.php';
    $app = require_once 'bootstrap/app.php';
    
    echo "✓ Laravel loaded successfully<br>";
    
    // Test if we can get the router
    $router = $app->make('router');
    echo "✓ Router created<br>";
    
    // Get all registered routes
    $routes = $router->getRoutes();
    $routeCount = count($routes);
    echo "✓ Found $routeCount registered routes<br>";
    
    // Test if we can create a kernel
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    echo "✓ HTTP Kernel created<br>";
    
    // Try to create a simple request to test routing
    $request = Illuminate\Http\Request::create('/', 'GET');
    echo "✓ Test request created<br>";
    
    // This is where it might fail - let's try to handle the request
    echo "<h3>Testing Request Handling:</h3>";
    try {
        $response = $kernel->handle($request);
        echo "✓ Request handled successfully<br>";
        echo "Response status: " . $response->getStatusCode() . "<br>";
        echo "Response content length: " . strlen($response->getContent()) . " chars<br>";
    } catch (Exception $e) {
        echo "<span class='error'>✗ Request handling failed: " . $e->getMessage() . "</span><br>";
        echo "<h4>Exception Details:</h4>";
        echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    }
    
} catch (Exception $e) {
    echo "<span class='error'>✗ Laravel setup failed: " . $e->getMessage() . "</span><br>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}

echo "<h2>3. Check Routes File</h2>";

if (file_exists('routes/web.php')) {
    echo "✓ routes/web.php exists<br>";
    $routesContent = file_get_contents('routes/web.php');
    echo "Routes file size: " . strlen($routesContent) . " bytes<br>";
    
    // Show first few lines of routes
    $lines = explode("\n", $routesContent);
    echo "<h3>First 15 lines of routes/web.php:</h3>";
    echo "<pre>";
    for ($i = 0; $i < min(15, count($lines)); $i++) {
        echo htmlspecialchars($lines[$i]) . "\n";
    }
    echo "</pre>";
} else {
    echo "<span class='error'>✗ routes/web.php not found</span><br>";
}

echo "<h2>4. Check for Missing Dependencies</h2>";

// Check if key Laravel directories exist
$requiredDirs = [
    'bootstrap/cache',
    'storage/framework/cache',
    'storage/framework/sessions',
    'storage/framework/views',
    'storage/logs'
];

foreach ($requiredDirs as $dir) {
    if (is_dir($dir)) {
        $writable = is_writable($dir) ? 'writable' : 'not writable';
        echo "✓ $dir exists ($writable)<br>";
    } else {
        echo "<span class='error'>✗ $dir missing</span><br>";
    }
}

echo "<h2>5. Test Simple Route Response</h2>";

// Create a super simple test that mimics what index.php does
try {
    // This should mimic exactly what index.php does
    define('LARAVEL_START', microtime(true));
    
    require_once 'vendor/autoload.php';
    $app = require_once 'bootstrap/app.php';
    
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    
    // Create the exact same request as a browser would
    $request = Illuminate\Http\Request::createFromGlobals();
    $request = Illuminate\Http\Request::create('/', 'GET', [], [], [], [
        'REQUEST_METHOD' => 'GET',
        'REQUEST_URI' => '/',
        'SERVER_NAME' => 'vaca.sh',
        'HTTP_HOST' => 'vaca.sh'
    ]);
    
    echo "✓ Created realistic request<br>";
    echo "Request URL: " . $request->url() . "<br>";
    echo "Request method: " . $request->method() . "<br>";
    
    // This is the exact line that fails in index.php:74
    $response = $kernel->handle($request);
    
    echo "✓ Request handled successfully!<br>";
    echo "Response status: " . $response->getStatusCode() . "<br>";
    echo "Content type: " . $response->headers->get('content-type') . "<br>";
    
    $content = $response->getContent();
    echo "Response length: " . strlen($content) . " characters<br>";
    
    // Show first 200 chars of response
    if (strlen($content) > 0) {
        echo "<h3>Response Preview:</h3>";
        echo "<div style='background:#f0f8ff; padding:10px; border:1px solid #ccc;'>";
        echo htmlspecialchars(substr($content, 0, 200)) . "...";
        echo "</div>";
    }
    
} catch (Exception $e) {
    echo "<span class='error'>✗ Realistic request test failed: " . $e->getMessage() . "</span><br>";
    echo "<h4>This is likely the same error causing the 500 error!</h4>";
    echo "<pre style='background:#ffe6e6; padding:10px;'>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    
    // Try to get more details about the error
    if (method_exists($e, 'getPrevious') && $e->getPrevious()) {
        echo "<h4>Previous Exception:</h4>";
        $prev = $e->getPrevious();
        echo "<p>Message: " . htmlspecialchars($prev->getMessage()) . "</p>";
        echo "<pre>" . htmlspecialchars($prev->getTraceAsString()) . "</pre>";
    }
}

?> 