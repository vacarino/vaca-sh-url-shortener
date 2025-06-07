<?php

/*
|--------------------------------------------------------------------------
| Optimized Laravel Bootstrap for Production
|--------------------------------------------------------------------------
*/

// Define Laravel start time
define('LARAVEL_START', microtime(true));

// Enhanced error handling
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

try {
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

    // Check for maintenance mode
    if (file_exists($maintenance = __DIR__.'/storage/framework/maintenance.php')) {
        require $maintenance;
    }

    // Load Composer autoloader
    require __DIR__.'/vendor/autoload.php';

    // Bootstrap Laravel application
    $app = require_once __DIR__.'/bootstrap/app.php';

    // Handle the request
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    
    $response = $kernel->handle(
        $request = Illuminate\Http\Request::capture()
    );
    
    $response->send();
    
    $kernel->terminate($request, $response);

} catch (Throwable $e) {
    // Log the error
    if (function_exists('error_log')) {
        error_log("Laravel Bootstrap Error: " . $e->getMessage() . " in " . $e->getFile() . " line " . $e->getLine());
    }
    
    // Show user-friendly error page
    http_response_code(500);
    echo "<!DOCTYPE html>
<html>
<head>
    <title>Vaca.Sh - Temporary Issue</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; padding: 50px; background: #f8f9fa; }
        .container { max-width: 600px; margin: 0 auto; background: white; padding: 40px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #e74c3c; margin-bottom: 20px; }
        p { color: #666; line-height: 1.6; }
        .code { background: #f1f1f1; padding: 3px 6px; border-radius: 3px; font-family: monospace; }
    </style>
</head>
<body>
    <div class='container'>
        <h1>🔧 Temporary Issue</h1>
        <p>Vaca.Sh is temporarily unavailable due to a configuration issue.</p>
        <p>Our team has been notified and we're working to resolve this quickly.</p>
        <p>Please try again in a few minutes.</p>
        <hr style='margin: 30px 0; border: none; border-top: 1px solid #eee;'>
        <p style='font-size: 14px; color: #999;'>Error ID: " . substr(md5($e->getMessage() . time()), 0, 8) . "</p>
    </div>
</body>
</html>";
}
?>