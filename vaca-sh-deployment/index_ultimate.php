<?php

/**
 * Ultimate Bulletproof Laravel Bootstrap
 * Handles all known production issues
 */

define('LARAVEL_START', microtime(true));

// Production error handling
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

function showErrorPage($error = null, $errorId = null) {
    http_response_code(500);
    $errorDetails = $error ? htmlspecialchars($error) : "Unknown error occurred";
    echo "<!DOCTYPE html>
<html lang=\"en\">
<head>
    <meta charset=\"UTF-8\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
    <title>Vaca.Sh - Service Temporarily Unavailable</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh; display: flex; align-items: center; justify-content: center; color: #333;
        }
        .container { 
            background: white; padding: 3rem; border-radius: 15px; 
            box-shadow: 0 20px 40px rgba(0,0,0,0.1); text-align: center; max-width: 600px; width: 90%; 
        }
        h1 { color: #667eea; margin-bottom: 1rem; font-size: 2.5rem; }
        p { color: #666; line-height: 1.6; margin-bottom: 1rem; }
        .status { background: #f8f9fa; padding: 1rem; border-radius: 8px; margin-top: 2rem; font-size: 0.9rem; }
        .logo { font-size: 3rem; margin-bottom: 1rem; }
        .details { background: #fff3cd; padding: 1rem; border-radius: 5px; margin-top: 1rem; font-size: 0.8rem; }
    </style>
</head>
<body>
    <div class=\"container\">
        <div class=\"logo\">🔗</div>
        <h1>Vaca.Sh</h1>
        <p>We apologize for the inconvenience. Our service is temporarily unavailable.</p>
        <p>Our technical team has been notified and is working to resolve this issue.</p>
        <div class=\"status\">
            <strong>Status:</strong> Under Maintenance<br>
            <strong>Expected Resolution:</strong> Within 30 minutes" . ($errorId ? "<br><strong>Reference ID:</strong> $errorId" : "") . "
        </div>
        <div class=\"details\">
            For urgent matters, please contact our support team with the reference ID above.
        </div>
    </div>
</body>
</html>";
    exit;
}

try {
    // Step 1: Environment loading with multiple fallbacks
    $envLoaded = false;
    $envPaths = [__DIR__ . '/.env', __DIR__ . '/../.env'];
    
    foreach ($envPaths as $envPath) {
        if (file_exists($envPath)) {
            $envContent = file_get_contents($envPath);
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
            $envLoaded = true;
            break;
        }
    }
    
    if (!$envLoaded) {
        throw new Exception("Environment file not found");
    }

    // Step 2: Maintenance mode check
    if (file_exists(__DIR__ . '/storage/framework/maintenance.php')) {
        require __DIR__ . '/storage/framework/maintenance.php';
    }

    // Step 3: Autoloader with fallbacks
    $autoloaderPaths = [__DIR__ . '/vendor/autoload.php', __DIR__ . '/../vendor/autoload.php'];
    $autoloaderLoaded = false;
    
    foreach ($autoloaderPaths as $autoloaderPath) {
        if (file_exists($autoloaderPath)) {
            require $autoloaderPath;
            $autoloaderLoaded = true;
            break;
        }
    }
    
    if (!$autoloaderLoaded) {
        throw new Exception("Composer autoloader not found");
    }

    // Step 4: Laravel bootstrap with validation
    $bootstrapPaths = [__DIR__ . '/bootstrap/app.php', __DIR__ . '/../bootstrap/app.php'];
    $app = null;
    
    foreach ($bootstrapPaths as $bootstrapPath) {
        if (file_exists($bootstrapPath)) {
            $app = require $bootstrapPath;
            break;
        }
    }
    
    if (!$app || !is_object($app) || !($app instanceof Illuminate\Foundation\Application)) {
        throw new Exception("Laravel application failed to initialize");
    }

    // Step 5: Critical services validation
    $criticalServices = ['config', 'router'];
    foreach ($criticalServices as $service) {
        try {
            $app->make($service);
        } catch (Exception $e) {
            throw new Exception("Critical service '$service' failed: " . $e->getMessage());
        }
    }

    // Step 6: Request handling
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    $response = $kernel->handle($request = Illuminate\Http\Request::capture());
    $response->send();
    $kernel->terminate($request, $response);

} catch (Throwable $e) {
    $errorMsg = $e->getMessage() . " in " . $e->getFile() . " line " . $e->getLine();
    $errorId = substr(md5($errorMsg . microtime()), 0, 8);
    
    if (function_exists('error_log')) {
        error_log("Laravel Error [$errorId]: $errorMsg");
    }
    
    showErrorPage($e->getMessage(), $errorId);
}

?>