<?php

/**
 * Bulletproof Laravel Bootstrap for Vaca.Sh
 * Handles various production environment issues
 */

// Define Laravel start time
define('LARAVEL_START', microtime(true));

// Error handling for production
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

// Function to show user-friendly error
function showMaintenancePage($errorId = null) {
    http_response_code(500);
    echo "<!DOCTYPE html>
<html lang=\"en\">
<head>
    <meta charset=\"UTF-8\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
    <title>Vaca.Sh - Temporary Maintenance</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #333;
        }
        .container { 
            background: white;
            padding: 3rem;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            text-align: center;
            max-width: 500px;
            width: 90%;
        }
        h1 { color: #667eea; margin-bottom: 1rem; font-size: 2.5rem; }
        p { color: #666; line-height: 1.6; margin-bottom: 1rem; }
        .status { background: #f8f9fa; padding: 1rem; border-radius: 8px; margin-top: 2rem; }
        .logo { font-size: 3rem; margin-bottom: 1rem; }
    </style>
</head>
<body>
    <div class=\"container\">
        <div class=\"logo\">🔗</div>
        <h1>Vaca.Sh</h1>
        <p>We're performing some quick maintenance to improve your experience.</p>
        <p>We'll be back online shortly!</p>
        <div class=\"status\">
            <small>Status: Updating configurations</small>" . ($errorId ? "<br><small>Error ID: $errorId</small>" : "") . "
        </div>
    </div>
</body>
</html>";
    exit;
}

try {
    // Step 1: Load environment variables manually (most reliable method)
    if (file_exists(__DIR__ . "/.env")) {
        $envContent = file_get_contents(__DIR__ . "/.env");
        $lines = explode("\n", $envContent);
        foreach ($lines as $line) {
            $line = trim($line);
            if (!empty($line) && strpos($line, "=") !== false && !str_starts_with($line, "#")) {
                list($key, $value) = explode("=", $line, 2);
                $key = trim($key);
                $value = trim($value);
                $_ENV[$key] = $value;
                putenv("$key=$value");
            }
        }
    }

    // Step 2: Check for maintenance mode
    if (file_exists(__DIR__ . '/storage/framework/maintenance.php')) {
        require __DIR__ . '/storage/framework/maintenance.php';
    }

    // Step 3: Load Composer autoloader
    if (!file_exists(__DIR__ . '/vendor/autoload.php')) {
        throw new Exception('Composer dependencies not installed');
    }
    require __DIR__ . '/vendor/autoload.php';

    // Step 4: Bootstrap Laravel with error handling
    if (!file_exists(__DIR__ . '/bootstrap/app.php')) {
        throw new Exception('Laravel bootstrap file not found');
    }
    
    $app = require_once __DIR__ . '/bootstrap/app.php';
    
    if (!$app || !is_object($app)) {
        throw new Exception('Laravel application failed to initialize');
    }

    // Step 5: Test critical services before handling request
    try {
        // Test if we can create the HTTP kernel
        $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
        
        // Test basic configuration access
        $appName = $app->make('config')->get('app.name', 'Laravel');
        
    } catch (Exception $e) {
        // If services fail, log the specific error and show maintenance page
        error_log("Laravel Service Error: " . $e->getMessage() . " in " . $e->getFile() . " line " . $e->getLine());
        showMaintenancePage(substr(md5($e->getMessage()), 0, 8));
    }

    // Step 6: Handle the request
    $response = $kernel->handle(
        $request = Illuminate\Http\Request::capture()
    );

    $response->send();

    $kernel->terminate($request, $response);

} catch (Throwable $e) {
    // Log the error for debugging
    $errorMsg = "Laravel Bootstrap Error: " . $e->getMessage() . " in " . $e->getFile() . " line " . $e->getLine();
    $errorId = substr(md5($errorMsg . time()), 0, 8);
    
    if (function_exists('error_log')) {
        error_log($errorMsg . " [Error ID: $errorId]");
    }
    
    showMaintenancePage($errorId);
}

?>