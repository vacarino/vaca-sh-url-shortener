<?php
/**
 * Comprehensive Laravel Fix Script
 * This will diagnose and fix all Laravel environment issues
 */

echo "<h1>🔧 Laravel Fix Script</h1>";
echo "<style>body{font-family:Arial;margin:20px;} .ok{color:green;} .error{color:red;} .warning{color:orange;} .info{color:blue;}</style>";

// Step 1: Check if .env file exists
echo "<h2>1. Environment File Check</h2>";

if (file_exists('.env')) {
    echo "✓ .env file exists<br>";
    $envContent = file_get_contents('.env');
    echo "File size: " . strlen($envContent) . " bytes<br>";
    
    if (strlen($envContent) > 0) {
        echo "✓ .env file has content<br>";
        echo "<details><summary>Show .env contents</summary><pre>" . htmlspecialchars($envContent) . "</pre></details>";
    } else {
        echo "<span class='error'>✗ .env file is empty</span><br>";
    }
} else {
    echo "<span class='error'>✗ .env file missing</span><br>";
}

// Step 2: Create or fix .env file
echo "<h2>2. Creating/Fixing Environment File</h2>";

$envTemplate = "APP_NAME=Vaca.Sh
APP_ENV=production
APP_KEY=base64:bdDkUe+oBu2lOPSkEMWEBsfuCPQJC2LylTYSzalp72c=
APP_DEBUG=false
APP_URL=https://vaca.sh

LOG_CHANNEL=stack

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=u336307813_vaca
DB_USERNAME=u336307813_vaca
DB_PASSWORD=ENTER_YOUR_PASSWORD_HERE

SESSION_DRIVER=file
CACHE_DRIVER=file
QUEUE_CONNECTION=sync
FILESYSTEM_DISK=local
";

// Force create/overwrite .env file
if (file_put_contents('.env', $envTemplate)) {
    echo "✓ .env file created/updated successfully<br>";
    
    // Set proper permissions
    if (chmod('.env', 0644)) {
        echo "✓ .env file permissions set to 644<br>";
    } else {
        echo "<span class='warning'>⚠ Could not set .env permissions</span><br>";
    }
} else {
    echo "<span class='error'>✗ Failed to create .env file</span><br>";
}

// Step 3: Test if Laravel can now read environment
echo "<h2>3. Testing Laravel Environment Loading</h2>";

// Clear any potential caches first
if (function_exists('opcache_reset')) {
    opcache_reset();
    echo "✓ OpCache cleared<br>";
}

// Try to load Laravel and test environment
try {
    require_once 'vendor/autoload.php';
    $app = require_once 'bootstrap/app.php';
    
    echo "✓ Laravel bootstrap successful<br>";
    
    // Test environment variables
    $appName = env('APP_NAME', 'NOT_SET');
    $appKey = env('APP_KEY', 'NOT_SET');
    $dbHost = env('DB_HOST', 'NOT_SET');
    $dbDatabase = env('DB_DATABASE', 'NOT_SET');
    $dbUsername = env('DB_USERNAME', 'NOT_SET');
    
    echo "<h3>Environment Variables:</h3>";
    echo "APP_NAME: <span class='" . ($appName !== 'NOT_SET' ? 'ok' : 'error') . "'>$appName</span><br>";
    echo "APP_KEY: <span class='" . ($appKey !== 'NOT_SET' ? 'ok' : 'error') . "'>" . ($appKey !== 'NOT_SET' ? 'Set (' . substr($appKey, 0, 20) . '...)' : 'NOT_SET') . "</span><br>";
    echo "DB_HOST: <span class='" . ($dbHost !== 'NOT_SET' ? 'ok' : 'error') . "'>$dbHost</span><br>";
    echo "DB_DATABASE: <span class='" . ($dbDatabase !== 'NOT_SET' ? 'ok' : 'error') . "'>$dbDatabase</span><br>";
    echo "DB_USERNAME: <span class='" . ($dbUsername !== 'NOT_SET' ? 'ok' : 'error') . "'>$dbUsername</span><br>";
    
    // Step 4: Test Laravel services
    echo "<h2>4. Testing Laravel Services</h2>";
    
    try {
        $config = $app->make('config');
        echo "✓ Config service working<br>";
        
        $files = $app->make('files');
        echo "✓ Files service working<br>";
        
        // Test database configuration
        $dbConfig = $config->get('database.default', 'NOT_SET');
        echo "Database default connection: <span class='info'>$dbConfig</span><br>";
        
    } catch (Exception $e) {
        echo "<span class='error'>✗ Laravel services failed: " . $e->getMessage() . "</span><br>";
    }
    
    // Step 5: Test database connection
    echo "<h2>5. Testing Database Connection</h2>";
    
    $dbPassword = env('DB_PASSWORD', 'NOT_SET');
    if ($dbPassword === 'ENTER_YOUR_PASSWORD_HERE' || $dbPassword === 'NOT_SET') {
        echo "<span class='warning'>⚠ Database password not set</span><br>";
        echo "<p><strong>To set your database password, add this to the URL:</strong></p>";
        echo "<p><code>?password=YOUR_ACTUAL_PASSWORD</code></p>";
        echo "<p>Example: <code>https://vaca.sh/fix_laravel.php?password=your_db_password</code></p>";
        
        // Check if password provided in URL
        if (isset($_GET['password']) && !empty($_GET['password'])) {
            $newPassword = $_GET['password'];
            $envContent = file_get_contents('.env');
            $envContent = preg_replace('/^DB_PASSWORD=.*$/m', 'DB_PASSWORD=' . $newPassword, $envContent);
            
            if (file_put_contents('.env', $envContent)) {
                echo "<span class='ok'>✓ Database password updated!</span><br>";
                echo "<a href='fix_laravel.php'>Refresh to test connection</a><br>";
            } else {
                echo "<span class='error'>✗ Failed to update password</span><br>";
            }
        }
    } else {
        echo "Database password: <span class='ok'>Set</span><br>";
        
        // Test actual database connection
        try {
            $db = $app->make('db');
            $connection = $db->connection();
            $result = $db->select('SELECT 1 as test');
            echo "✓ Database connection successful<br>";
            
            // Test table count
            $tables = $db->select('SHOW TABLES');
            echo "✓ Tables found: " . count($tables) . "<br>";
            
        } catch (Exception $e) {
            echo "<span class='error'>✗ Database connection failed: " . $e->getMessage() . "</span><br>";
        }
    }
    
} catch (Exception $e) {
    echo "<span class='error'>✗ Laravel bootstrap failed: " . $e->getMessage() . "</span><br>";
}

// Step 6: Final recommendations
echo "<h2>6. Next Steps</h2>";
echo "<ol>";
echo "<li>If environment variables are still NOT_SET, the .env file isn't being read. Check file permissions.</li>";
echo "<li>If you see 'ENTER_YOUR_PASSWORD_HERE', add <code>?password=your_actual_password</code> to this URL</li>";
echo "<li>Once everything shows green checkmarks, visit: <a href='/'>https://vaca.sh/</a></li>";
echo "<li>Delete this fix script and all debug files for security</li>";
echo "</ol>";

echo "<p><strong>Files to delete after fixing:</strong> fix_laravel.php, laravel_debug.php, debug.php, db_test.php, step_debug.php, update_env_password.php</p>";
?> 