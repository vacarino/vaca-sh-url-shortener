<?php
/**
 * Laravel-Specific Debug Script
 * This tests Laravel's actual configuration and database connection
 */

echo "<h1>🔍 Laravel-Specific Debug</h1>";
echo "<style>body{font-family:Arial;margin:20px;} .ok{color:green;} .error{color:red;} .warning{color:orange;} .info{color:blue;}</style>";

// Step 1: Test basic Laravel bootstrap
echo "<h2>1. Laravel Bootstrap Test</h2>";
try {
    require_once 'vendor/autoload.php';
    $app = require_once 'bootstrap/app.php';
    echo "✓ Laravel app created successfully<br>";
    
    // Step 2: Test Laravel's environment loading
    echo "<h2>2. Laravel Environment Test</h2>";
    
    // Boot the application to load environment
    $kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
    echo "✓ HTTP Kernel created<br>";
    
    // Test if Laravel can read .env
    $appName = env('APP_NAME', 'NOT_SET');
    $appKey = env('APP_KEY', 'NOT_SET');
    $dbConnection = env('DB_CONNECTION', 'NOT_SET');
    $dbHost = env('DB_HOST', 'NOT_SET');
    $dbDatabase = env('DB_DATABASE', 'NOT_SET');
    $dbUsername = env('DB_USERNAME', 'NOT_SET');
    
    echo "APP_NAME: <span class='info'>$appName</span><br>";
    echo "APP_KEY: ";
    if ($appKey !== 'NOT_SET' && !empty($appKey)) {
        echo "<span class='ok'>✓ Set (" . substr($appKey, 0, 20) . "...)</span><br>";
    } else {
        echo "<span class='error'>✗ Missing or empty</span><br>";
    }
    
    echo "DB_CONNECTION: <span class='info'>$dbConnection</span><br>";
    echo "DB_HOST: <span class='info'>$dbHost</span><br>";
    echo "DB_DATABASE: <span class='info'>$dbDatabase</span><br>";
    echo "DB_USERNAME: <span class='info'>$dbUsername</span><br>";
    
    // Step 3: Test Laravel's database connection
    echo "<h2>3. Laravel Database Connection Test</h2>";
    
    try {
        // Try to get database manager
        $db = $app->make('db');
        echo "✓ Database manager created<br>";
        
        // Test actual connection
        $connection = $db->connection();
        echo "✓ Database connection obtained<br>";
        
        // Test simple query
        $result = $db->select('SELECT 1 as test');
        echo "✓ Simple query executed successfully<br>";
        
        // Test table count
        $tables = $db->select('SHOW TABLES');
        echo "✓ Tables found: " . count($tables) . "<br>";
        
        // Test specific Laravel tables
        $migrations = $db->select("SHOW TABLES LIKE 'migrations'");
        if (!empty($migrations)) {
            echo "✓ Migrations table exists<br>";
        } else {
            echo "<span class='warning'>⚠ Migrations table missing</span><br>";
        }
        
        $users = $db->select("SHOW TABLES LIKE 'users'");
        if (!empty($users)) {
            echo "✓ Users table exists<br>";
            
            // Test user count
            $userCount = $db->select('SELECT COUNT(*) as count FROM users')[0]->count;
            echo "✓ Users in database: $userCount<br>";
        } else {
            echo "<span class='error'>✗ Users table missing</span><br>";
        }
        
    } catch (Exception $e) {
        echo "<span class='error'>✗ Laravel database connection failed</span><br>";
        echo "Error: " . $e->getMessage() . "<br>";
        echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "<br>";
    }
    
    // Step 4: Test Laravel configuration
    echo "<h2>4. Laravel Configuration Test</h2>";
    
    try {
        $config = $app->make('config');
        echo "✓ Config manager created<br>";
        
        // Check database config
        $dbConfig = $config->get('database.default');
        echo "Default DB connection: <span class='info'>$dbConfig</span><br>";
        
        $mysqlConfig = $config->get('database.connections.mysql');
        if ($mysqlConfig) {
            echo "MySQL config:<br>";
            echo "  Host: <span class='info'>" . $mysqlConfig['host'] . "</span><br>";
            echo "  Database: <span class='info'>" . $mysqlConfig['database'] . "</span><br>";
            echo "  Username: <span class='info'>" . $mysqlConfig['username'] . "</span><br>";
            echo "  Port: <span class='info'>" . $mysqlConfig['port'] . "</span><br>";
        }
        
        // Check app config
        $appUrl = $config->get('app.url');
        $appEnv = $config->get('app.env');
        $appDebug = $config->get('app.debug');
        
        echo "App URL: <span class='info'>$appUrl</span><br>";
        echo "App Environment: <span class='info'>$appEnv</span><br>";
        echo "App Debug: <span class='info'>" . ($appDebug ? 'true' : 'false') . "</span><br>";
        
    } catch (Exception $e) {
        echo "<span class='error'>✗ Laravel configuration failed</span><br>";
        echo "Error: " . $e->getMessage() . "<br>";
    }
    
    // Step 5: Test Laravel routing
    echo "<h2>5. Laravel Routing Test</h2>";
    
    try {
        $router = $app->make('router');
        echo "✓ Router created<br>";
        
        // Get all routes
        $routes = $router->getRoutes();
        echo "✓ Total routes registered: " . count($routes) . "<br>";
        
        // Check for specific routes
        $homeRoute = $routes->getByName('home');
        if ($homeRoute) {
            echo "✓ Home route exists<br>";
        } else {
            echo "<span class='warning'>⚠ Home route not found</span><br>";
        }
        
    } catch (Exception $e) {
        echo "<span class='error'>✗ Laravel routing failed</span><br>";
        echo "Error: " . $e->getMessage() . "<br>";
    }
    
    // Step 6: Test session and cache
    echo "<h2>6. Laravel Services Test</h2>";
    
    try {
        // Test session
        $session = $app->make('session');
        echo "✓ Session manager created<br>";
        
        // Test cache
        $cache = $app->make('cache');
        echo "✓ Cache manager created<br>";
        
        // Test view
        $view = $app->make('view');
        echo "✓ View manager created<br>";
        
    } catch (Exception $e) {
        echo "<span class='error'>✗ Laravel services failed</span><br>";
        echo "Error: " . $e->getMessage() . "<br>";
    }
    
} catch (Exception $e) {
    echo "<span class='error'>✗ Laravel bootstrap failed</span><br>";
    echo "Error: " . $e->getMessage() . "<br>";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "<br>";
    echo "Stack trace:<br><pre>" . $e->getTraceAsString() . "</pre>";
}

// Step 7: Check for cached files that might cause issues
echo "<h2>7. Laravel Cache Files Check</h2>";

$cacheFiles = [
    'bootstrap/cache/config.php' => 'Configuration cache',
    'bootstrap/cache/routes.php' => 'Routes cache',
    'bootstrap/cache/services.php' => 'Services cache',
    'bootstrap/cache/packages.php' => 'Packages cache'
];

foreach ($cacheFiles as $file => $desc) {
    if (file_exists($file)) {
        echo "<span class='warning'>⚠ $desc exists</span> - <a href='?clear_cache=1'>Clear it</a><br>";
    } else {
        echo "✓ $desc not cached<br>";
    }
}

// Clear cache if requested
if (isset($_GET['clear_cache'])) {
    foreach ($cacheFiles as $file => $desc) {
        if (file_exists($file)) {
            unlink($file);
            echo "<span class='ok'>✓ Cleared $desc</span><br>";
        }
    }
    echo "<br><a href='laravel_debug.php'>Run test again</a><br>";
}

// Step 8: Recommendations
echo "<h2>8. Recommendations</h2>";
echo "<ol>";
echo "<li><strong>If Laravel bootstrap fails:</strong> Check composer autoloader and bootstrap/app.php</li>";
echo "<li><strong>If database connection fails in Laravel:</strong> The issue is with Laravel's config, not the database itself</li>";
echo "<li><strong>If APP_KEY is wrong format:</strong> Generate with 'php artisan key:generate --show' locally</li>";
echo "<li><strong>If cache files exist:</strong> Click the clear cache links above</li>";
echo "<li><strong>If all passes but site still fails:</strong> Check Hostinger error logs</li>";
echo "</ol>";

echo "<br><p><strong>Delete this file after debugging for security.</strong></p>";
?> 