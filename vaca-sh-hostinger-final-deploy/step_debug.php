<?php
/**
 * Step-by-Step Debug Script
 * This will help identify exactly where Laravel bootstrap fails
 */

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Step 1: Script started successfully<br>";
flush();

// Test basic PHP
echo "Step 2: PHP version: " . phpversion() . "<br>";
flush();

// Check if files exist
echo "Step 3: Checking required files...<br>";
if (file_exists('vendor/autoload.php')) {
    echo "✓ vendor/autoload.php exists<br>";
} else {
    echo "✗ vendor/autoload.php missing<br>";
    exit;
}

if (file_exists('bootstrap/app.php')) {
    echo "✓ bootstrap/app.php exists<br>";
} else {
    echo "✗ bootstrap/app.php missing<br>";
    exit;
}

if (file_exists('.env')) {
    echo "✓ .env file exists<br>";
} else {
    echo "✗ .env file missing<br>";
}
flush();

echo "Step 4: Attempting to load Composer autoloader...<br>";
flush();

try {
    require_once 'vendor/autoload.php';
    echo "✓ Composer autoloader loaded successfully<br>";
} catch (Exception $e) {
    echo "✗ Composer autoloader failed: " . $e->getMessage() . "<br>";
    exit;
} catch (Error $e) {
    echo "✗ Composer autoloader error: " . $e->getMessage() . "<br>";
    exit;
}
flush();

echo "Step 5: Attempting to load Laravel bootstrap...<br>";
flush();

try {
    $app = require_once 'bootstrap/app.php';
    echo "✓ Laravel bootstrap loaded successfully<br>";
    echo "App class: " . get_class($app) . "<br>";
} catch (Exception $e) {
    echo "✗ Laravel bootstrap failed: " . $e->getMessage() . "<br>";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "<br>";
    exit;
} catch (Error $e) {
    echo "✗ Laravel bootstrap error: " . $e->getMessage() . "<br>";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "<br>";
    exit;
}
flush();

echo "Step 6: Testing basic Laravel services...<br>";
flush();

try {
    // Test if we can make basic services
    $config = $app->make('config');
    echo "✓ Config service created<br>";
    
    $files = $app->make('files');
    echo "✓ Files service created<br>";
    
} catch (Exception $e) {
    echo "✗ Basic services failed: " . $e->getMessage() . "<br>";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "<br>";
} catch (Error $e) {
    echo "✗ Basic services error: " . $e->getMessage() . "<br>";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "<br>";
}
flush();

echo "Step 7: Testing environment loading...<br>";
flush();

try {
    // Try to access environment variables
    $appName = env('APP_NAME', 'DEFAULT');
    echo "APP_NAME: " . $appName . "<br>";
    
    $appEnv = env('APP_ENV', 'DEFAULT');
    echo "APP_ENV: " . $appEnv . "<br>";
    
} catch (Exception $e) {
    echo "✗ Environment loading failed: " . $e->getMessage() . "<br>";
} catch (Error $e) {
    echo "✗ Environment loading error: " . $e->getMessage() . "<br>";
}
flush();

echo "Step 8: Testing HTTP Kernel creation...<br>";
flush();

try {
    $kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
    echo "✓ HTTP Kernel created successfully<br>";
    echo "Kernel class: " . get_class($kernel) . "<br>";
} catch (Exception $e) {
    echo "✗ HTTP Kernel failed: " . $e->getMessage() . "<br>";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "<br>";
} catch (Error $e) {
    echo "✗ HTTP Kernel error: " . $e->getMessage() . "<br>";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "<br>";
}
flush();

echo "Step 9: Testing database connection...<br>";
flush();

try {
    $db = $app->make('db');
    echo "✓ Database manager created<br>";
    
    $connection = $db->connection();
    echo "✓ Database connection obtained<br>";
    
    $result = $db->select('SELECT 1 as test');
    echo "✓ Database query successful<br>";
    
} catch (Exception $e) {
    echo "✗ Database failed: " . $e->getMessage() . "<br>";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "<br>";
} catch (Error $e) {
    echo "✗ Database error: " . $e->getMessage() . "<br>";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "<br>";
}

echo "<br><br>Debug completed. If you see this message, Laravel is working!<br>";
echo "<strong>Next step:</strong> Try to access the main site at <a href='/'>https://vaca.sh/</a><br>";
?> 