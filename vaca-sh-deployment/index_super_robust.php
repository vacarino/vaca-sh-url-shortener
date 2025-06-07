<?php

/**
 * Super Robust Laravel Bootstrap for Vaca.Sh
 * Bypasses service container issues and handles all edge cases
 */

define('LARAVEL_START', microtime(true));

// Disable errors for production
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

function renderMaintenancePage($message = null, $errorId = null) {
    http_response_code(503); // Service Unavailable
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vaca.Sh - Maintenance</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh; display: flex; align-items: center; justify-content: center;
        }
        .container { 
            background: white; padding: 3rem; border-radius: 20px; 
            box-shadow: 0 25px 50px rgba(0,0,0,0.15); text-align: center; max-width: 500px; width: 90%; 
        }
        .logo { font-size: 4rem; margin-bottom: 1rem; }
        h1 { color: #667eea; margin-bottom: 1rem; font-size: 2.5rem; font-weight: 700; }
        p { color: #666; line-height: 1.6; margin-bottom: 1rem; font-size: 1.1rem; }
        .status { 
            background: linear-gradient(135deg, #f8f9fa, #e9ecef); 
            padding: 1.5rem; border-radius: 10px; margin-top: 2rem; 
        }
        .pulse { animation: pulse 2s infinite; }
        @keyframes pulse { 0% { opacity: 1; } 50% { opacity: 0.7; } 100% { opacity: 1; } }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo pulse">🔗</div>
        <h1>Vaca.Sh</h1>
        <p>We're performing essential maintenance to enhance your experience.</p>
        <p>Thank you for your patience!</p>
        <div class="status">
            <strong>🔧 Status:</strong> System Updates in Progress<br>
            <strong>⏱️ ETA:</strong> Back online shortly<?php if ($errorId): ?><br><strong>🆔 Ref:</strong> <?= $errorId ?><?php endif; ?>
        </div>
    </div>
</body>
</html>
    <?php
    exit;
}

try {
    // Step 1: Environment loading with robust error handling
    $envFile = __DIR__ . '/.env';
    if (!file_exists($envFile)) {
        throw new Exception('Environment configuration not found');
    }
    
    $envContent = file_get_contents($envFile);
    if ($envContent === false) {
        throw new Exception('Unable to read environment configuration');
    }
    
    // Parse environment variables with proper quote handling
    $lines = explode("\n", $envContent);
    foreach ($lines as $line) {
        $line = trim($line);
        if (!empty($line) && strpos($line, '=') !== false && !str_starts_with($line, '#')) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            
            // Remove surrounding quotes if present
            if ((str_starts_with($value, '"') && str_ends_with($value, '"')) ||
                (str_starts_with($value, "'") && str_ends_with($value, "'"))) {
                $value = substr($value, 1, -1);
            }
            
            $_ENV[$key] = $value;
            putenv("$key=$value");
        }
    }

    // Step 2: Check maintenance mode
    if (file_exists(__DIR__ . '/storage/framework/maintenance.php')) {
        require __DIR__ . '/storage/framework/maintenance.php';
    }

    // Step 3: Load Composer autoloader
    $autoloaderPath = __DIR__ . '/vendor/autoload.php';
    if (!file_exists($autoloaderPath)) {
        throw new Exception('Application dependencies not found');
    }
    require $autoloaderPath;

    // Step 4: Bootstrap Laravel application
    $bootstrapPath = __DIR__ . '/bootstrap/app.php';
    if (!file_exists($bootstrapPath)) {
        throw new Exception('Application bootstrap not found');
    }
    
    $app = require $bootstrapPath;
    if (!$app instanceof Illuminate\Foundation\Application) {
        throw new Exception('Application failed to initialize properly');
    }

    // Step 5: Handle the request with enhanced error handling
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    
    // Create and handle request
    $request = Illuminate\Http\Request::capture();
    $response = $kernel->handle($request);
    
    // Send response
    $response->send();
    
    // Terminate
    $kernel->terminate($request, $response);

} catch (Throwable $e) {
    // Log error for debugging
    $errorId = substr(md5($e->getMessage() . microtime(true)), 0, 8);
    $logMessage = sprintf(
        "[%s] Laravel Error [%s]: %s in %s:%d",
        date('Y-m-d H:i:s'),
        $errorId,
        $e->getMessage(),
        $e->getFile(),
        $e->getLine()
    );
    
    // Try to log the error
    if (function_exists('error_log')) {
        error_log($logMessage);
    }
    
    // Show maintenance page
    renderMaintenancePage($e->getMessage(), $errorId);
}

?>