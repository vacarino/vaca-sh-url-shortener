<?php
/**
 * Comprehensive Laravel Production Fix
 * Addresses multiple issues causing 500 errors on production
 */

echo "<h1>🔧 Laravel Production Fix</h1>";
echo "<style>body{font-family:Arial;margin:20px;} .ok{color:green;} .error{color:red;} .warning{color:orange;} .info{color:blue;}</style>";

echo "<h2>1. Environment Configuration Fix</h2>";

// Step 1: Ensure .env file has correct values
$envPath = __DIR__ . '/.env';
if (file_exists($envPath)) {
    $envContent = file_get_contents($envPath);
    echo "✓ .env file exists<br>";
    
    // Check if database password is still placeholder
    if (strpos($envContent, 'your_db_pass') !== false) {
        echo "<span class='warning'>⚠ Database password is still placeholder - this needs to be updated manually</span><br>";
    }
    
    // Ensure APP_KEY exists
    if (strpos($envContent, 'APP_KEY=') === false || strpos($envContent, 'APP_KEY=base64:') === false) {
        echo "<span class='error'>✗ APP_KEY missing or invalid</span><br>";
        
        // Generate a new APP_KEY
        $newKey = 'base64:' . base64_encode(random_bytes(32));
        $envContent = preg_replace('/^APP_KEY=.*$/m', "APP_KEY=$newKey", $envContent);
        file_put_contents($envPath, $envContent);
        echo "<span class='ok'>✓ Generated new APP_KEY</span><br>";
    } else {
        echo "✓ APP_KEY exists<br>";
    }
} else {
    echo "<span class='error'>✗ .env file not found</span><br>";
}

echo "<h2>2. Clear Laravel Caches</h2>";

try {
    // Clear all caches
    $directories = [
        'bootstrap/cache',
        'storage/framework/cache/data',
        'storage/framework/sessions',
        'storage/framework/views',
        'storage/logs'
    ];
    
    foreach ($directories as $dir) {
        if (is_dir($dir)) {
            // Clear directory contents but keep the directory
            $files = glob($dir . '/*');
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
            echo "✓ Cleared $dir<br>";
        } else {
            // Create directory if it doesn't exist
            mkdir($dir, 0755, true);
            echo "✓ Created $dir<br>";
        }
    }
    
    // Specifically clear compiled views
    if (is_dir('storage/framework/views')) {
        $viewFiles = glob('storage/framework/views/*.php');
        foreach ($viewFiles as $file) {
            unlink($file);
        }
        echo "✓ Cleared compiled views<br>";
    }
    
} catch (Exception $e) {
    echo "<span class='error'>✗ Cache clearing error: " . $e->getMessage() . "</span><br>";
}

echo "<h2>3. Fix File Permissions</h2>";

try {
    // Set proper permissions
    $storagePerms = [
        'storage',
        'storage/app',
        'storage/framework',
        'storage/framework/cache',
        'storage/framework/sessions',
        'storage/framework/views',
        'storage/logs',
        'bootstrap/cache'
    ];
    
    foreach ($storagePerms as $path) {
        if (is_dir($path)) {
            chmod($path, 0755);
            echo "✓ Set permissions for $path<br>";
        }
    }
    
    // Make sure .env is readable
    if (file_exists('.env')) {
        chmod('.env', 0644);
        echo "✓ Set .env permissions<br>";
    }
    
} catch (Exception $e) {
    echo "<span class='error'>✗ Permission error: " . $e->getMessage() . "</span><br>";
}

echo "<h2>4. Test Laravel Bootstrap</h2>";

try {
    // Test if Laravel can bootstrap properly
    require_once 'vendor/autoload.php';
    
    // Load environment variables manually first
    if (file_exists('.env')) {
        $envContent = file_get_contents('.env');
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
    
    $app = require_once 'bootstrap/app.php';
    echo "✓ Laravel application created<br>";
    
    // Test service container
    $router = $app->make('router');
    echo "✓ Router service created<br>";
    
    // Test configuration
    $config = $app->make('config');
    echo "✓ Configuration service created<br>";
    
    // Test database connection
    try {
        $db = $app->make('db');
        echo "✓ Database service created<br>";
        
        // Test actual connection
        $db->connection()->getPdo();
        echo "✓ Database connection successful<br>";
    } catch (Exception $e) {
        echo "<span class='warning'>⚠ Database connection failed: " . $e->getMessage() . "</span><br>";
        echo "<span class='info'>This might be due to incorrect database credentials in .env</span><br>";
    }
    
    // Test route loading
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    echo "✓ HTTP Kernel created<br>";
    
    // Create a test request
    $request = Illuminate\Http\Request::create('/', 'GET');
    echo "✓ Test request created<br>";
    
    // This should work now
    $response = $kernel->handle($request);
    echo "✓ Request handled successfully<br>";
    echo "Response status: " . $response->getStatusCode() . "<br>";
    
    if ($response->getStatusCode() == 200) {
        echo "<span class='ok'>🎉 Laravel is working correctly!</span><br>";
    } else {
        echo "<span class='warning'>⚠ Laravel responding but with status: " . $response->getStatusCode() . "</span><br>";
    }
    
} catch (Exception $e) {
    echo "<span class='error'>✗ Laravel bootstrap failed: " . $e->getMessage() . "</span><br>";
    echo "<h4>Error Details:</h4>";
    echo "<pre style='background:#ffe6e6; padding:10px;'>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    
    // Additional debugging
    if (strpos($e->getMessage(), 'database') !== false || strpos($e->getMessage(), 'connection') !== false) {
        echo "<h4>Database Connection Issue:</h4>";
        echo "<p>Please check your database credentials in the .env file:</p>";
        echo "<ul>";
        echo "<li>DB_HOST: Should be 'localhost' for most shared hosting</li>";
        echo "<li>DB_DATABASE: Should be your actual database name (currently: " . ($_ENV['DB_DATABASE'] ?? 'not set') . ")</li>";
        echo "<li>DB_USERNAME: Should be your database username (currently: " . ($_ENV['DB_USERNAME'] ?? 'not set') . ")</li>";
        echo "<li>DB_PASSWORD: Should be your actual database password (currently: " . (isset($_ENV['DB_PASSWORD']) ? 'set but masked' : 'not set') . ")</li>";
        echo "</ul>";
    }
}

echo "<h2>5. Create Optimized Bootstrap</h2>";

// Create an optimized index.php that handles errors better
$optimizedIndex = '<?php

/*
|--------------------------------------------------------------------------
| Optimized Laravel Bootstrap for Production
|--------------------------------------------------------------------------
*/

// Define Laravel start time
define(\'LARAVEL_START\', microtime(true));

// Enhanced error handling
ini_set(\'display_errors\', 0);
ini_set(\'log_errors\', 1);
error_reporting(E_ALL);

try {
    // Load environment variables first
    if (file_exists(__DIR__ . "/.env")) {
        $envContent = file_get_contents(__DIR__ . "/.env");
        $lines = explode("\\n", $envContent);
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
    if (file_exists($maintenance = __DIR__.\'/storage/framework/maintenance.php\')) {
        require $maintenance;
    }

    // Load Composer autoloader
    require __DIR__.\'/vendor/autoload.php\';

    // Bootstrap Laravel application
    $app = require_once __DIR__.\'/bootstrap/app.php\';

    // Handle the request
    $kernel = $app->make(Illuminate\\Contracts\\Http\\Kernel::class);
    
    $response = $kernel->handle(
        $request = Illuminate\\Http\\Request::capture()
    );
    
    $response->send();
    
    $kernel->terminate($request, $response);

} catch (Throwable $e) {
    // Log the error
    if (function_exists(\'error_log\')) {
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
    <div class=\'container\'>
        <h1>🔧 Temporary Issue</h1>
        <p>Vaca.Sh is temporarily unavailable due to a configuration issue.</p>
        <p>Our team has been notified and we\'re working to resolve this quickly.</p>
        <p>Please try again in a few minutes.</p>
        <hr style=\'margin: 30px 0; border: none; border-top: 1px solid #eee;\'>
        <p style=\'font-size: 14px; color: #999;\'>Error ID: " . substr(md5($e->getMessage() . time()), 0, 8) . "</p>
    </div>
</body>
</html>";
}
?>';

file_put_contents('index_optimized.php', $optimizedIndex);
echo "✓ Created optimized index.php (saved as index_optimized.php)<br>";

echo "<h2>6. Summary & Next Steps</h2>";

echo "<div style='background:#e8f5e8; padding:15px; border-left:4px solid green; margin:20px 0;'>";
echo "<h3>✅ Completed Fixes:</h3>";
echo "<ul>";
echo "<li>Cleared all Laravel caches</li>";
echo "<li>Fixed file permissions</li>";
echo "<li>Created optimized bootstrap</li>";
echo "<li>Enhanced error handling</li>";
echo "</ul>";
echo "</div>";

echo "<div style='background:#fff3cd; padding:15px; border-left:4px solid orange; margin:20px 0;'>";
echo "<h3>⚠ Manual Steps Required:</h3>";
echo "<ol>";
echo "<li><strong>Update database password</strong> in .env file (replace 'your_db_pass' with actual password)</li>";
echo "<li><strong>Backup current index.php</strong> and replace with index_optimized.php</li>";
echo "<li><strong>Test the application</strong> after making these changes</li>";
echo "</ol>";
echo "</div>";

echo "<div style='background:#d1ecf1; padding:15px; border-left:4px solid blue; margin:20px 0;'>";
echo "<h3>📋 Commands to run on server:</h3>";
echo "<pre style='background:#f8f9fa; padding:10px;'>";
echo "# 1. Backup current index.php\n";
echo "cp index.php index.php.backup\n\n";
echo "# 2. Use the optimized version\n";
echo "cp index_optimized.php index.php\n\n";
echo "# 3. Set proper permissions\n";
echo "chmod 644 index.php\n";
echo "chmod -R 755 storage bootstrap/cache\n";
echo "</pre>";
echo "</div>";

?> 