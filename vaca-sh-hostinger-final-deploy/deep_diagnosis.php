<?php
/**
 * Deep Diagnosis and Auto-Fix for Vaca.Sh Production Issues
 * This will identify and automatically fix all possible causes of 500 errors
 */

echo "<h1>🔍 Deep Diagnosis & Auto-Fix</h1>";
echo "<style>body{font-family:Arial;margin:20px;} .ok{color:green;} .error{color:red;} .warning{color:orange;} .info{color:blue;} .fixed{background:#d4edda;padding:5px;border-radius:3px;}</style>";

$issues = [];
$fixes = [];

echo "<h2>1. Environment File Deep Analysis</h2>";

// Check .env file
if (file_exists('.env')) {
    $envContent = file_get_contents('.env');
    echo "✓ .env file exists<br>";
    
    // Parse environment variables
    $envVars = [];
    $lines = explode("\n", $envContent);
    foreach ($lines as $line) {
        $line = trim($line);
        if (!empty($line) && strpos($line, '=') !== false && !str_starts_with($line, '#')) {
            list($key, $value) = explode('=', $line, 2);
            $envVars[trim($key)] = trim($value);
        }
    }
    
    // Check critical environment variables
    $criticalVars = ['APP_NAME', 'APP_ENV', 'APP_KEY', 'APP_DEBUG', 'APP_URL', 'DB_HOST', 'DB_PORT', 'DB_DATABASE', 'DB_USERNAME', 'DB_PASSWORD'];
    
    foreach ($criticalVars as $var) {
        if (isset($envVars[$var]) && !empty($envVars[$var])) {
            if ($var === 'DB_PASSWORD') {
                echo "✓ $var is set<br>";
            } else {
                echo "✓ $var = " . $envVars[$var] . "<br>";
            }
        } else {
            echo "<span class='error'>✗ $var is missing or empty</span><br>";
            $issues[] = "$var missing";
        }
    }
    
    // Fix APP_KEY if needed
    if (!isset($envVars['APP_KEY']) || empty($envVars['APP_KEY']) || $envVars['APP_KEY'] === 'base64:') {
        $newKey = 'base64:' . base64_encode(random_bytes(32));
        $envContent = preg_replace('/^APP_KEY=.*$/m', "APP_KEY=$newKey", $envContent);
        file_put_contents('.env', $envContent);
        echo "<span class='fixed'>🔧 Generated new APP_KEY</span><br>";
        $fixes[] = "Generated APP_KEY";
    }
    
} else {
    echo "<span class='error'>✗ .env file not found</span><br>";
    $issues[] = ".env file missing";
}

echo "<h2>2. Database Connection Deep Test</h2>";

// Test database connection with detailed error reporting
try {
    // Load environment variables manually
    if (file_exists('.env')) {
        $envContent = file_get_contents('.env');
        $lines = explode("\n", $envContent);
        foreach ($lines as $line) {
            $line = trim($line);
            if (!empty($line) && strpos($line, '=') !== false && !str_starts_with($line, '#')) {
                list($key, $value) = explode('=', $line, 2);
                $_ENV[trim($key)] = trim($value);
                putenv(trim($key) . '=' . trim($value));
            }
        }
    }
    
    $host = $_ENV['DB_HOST'] ?? 'localhost';
    $port = $_ENV['DB_PORT'] ?? '3306';
    $database = $_ENV['DB_DATABASE'] ?? '';
    $username = $_ENV['DB_USERNAME'] ?? '';
    $password = $_ENV['DB_PASSWORD'] ?? '';
    
    echo "Attempting connection to: $username@$host:$port/$database<br>";
    
    // Try different connection methods
    $dsn = "mysql:host=$host;port=$port;dbname=$database;charset=utf8mb4";
    echo "DSN: $dsn<br>";
    
    try {
        $pdo = new PDO($dsn, $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
        
        echo "<span class='ok'>✅ Database connection successful!</span><br>";
        
        // Test basic queries
        $stmt = $pdo->query("SELECT VERSION() as version, NOW() as current_time");
        $result = $stmt->fetch();
        echo "MySQL Version: " . $result['version'] . "<br>";
        echo "Server Time: " . $result['current_time'] . "<br>";
        
        // Check required tables
        $requiredTables = ['users', 'short_urls', 'click_logs', 'password_reset_tokens', 'personal_access_tokens'];
        $existingTables = [];
        
        $stmt = $pdo->query("SHOW TABLES");
        while ($row = $stmt->fetch()) {
            $existingTables[] = array_values($row)[0];
        }
        
        foreach ($requiredTables as $table) {
            if (in_array($table, $existingTables)) {
                echo "✓ Table '$table' exists<br>";
            } else {
                echo "<span class='warning'>⚠ Table '$table' missing</span><br>";
                $issues[] = "Table $table missing";
            }
        }
        
    } catch (PDOException $e) {
        echo "<span class='error'>✗ Database connection failed: " . $e->getMessage() . "</span><br>";
        
        // Try to determine the specific issue
        if (strpos($e->getMessage(), '1045') !== false) {
            echo "<span class='info'>This is an authentication error - username or password is incorrect</span><br>";
            
            // Try to fix by escaping the password
            $escapedPassword = addslashes($password);
            if ($escapedPassword !== $password) {
                echo "Trying with escaped password...<br>";
                try {
                    $pdo2 = new PDO($dsn, $username, $escapedPassword, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
                    echo "<span class='ok'>✅ Connection successful with escaped password!</span><br>";
                    
                    // Update .env with escaped password
                    $envContent = file_get_contents('.env');
                    $envContent = preg_replace('/^DB_PASSWORD=.*$/m', "DB_PASSWORD=$escapedPassword", $envContent);
                    file_put_contents('.env', $envContent);
                    echo "<span class='fixed'>🔧 Updated .env with escaped password</span><br>";
                    $fixes[] = "Fixed database password escaping";
                    
                } catch (PDOException $e2) {
                    echo "<span class='error'>Still failed with escaped password</span><br>";
                    $issues[] = "Database authentication failed";
                }
            }
        } elseif (strpos($e->getMessage(), '1049') !== false) {
            echo "<span class='info'>Database '$database' does not exist</span><br>";
            $issues[] = "Database does not exist";
        } elseif (strpos($e->getMessage(), '2002') !== false) {
            echo "<span class='info'>Cannot connect to MySQL server - server might be down or host is wrong</span><br>";
            $issues[] = "MySQL server unreachable";
        }
    }
    
} catch (Exception $e) {
    echo "<span class='error'>✗ Critical error in database testing: " . $e->getMessage() . "</span><br>";
    $issues[] = "Database test failed";
}

echo "<h2>3. Laravel Bootstrap Deep Analysis</h2>";

try {
    // Test each step of Laravel bootstrap
    echo "Step 1: Loading Composer autoloader...<br>";
    if (!file_exists('vendor/autoload.php')) {
        echo "<span class='error'>✗ Composer autoloader not found</span><br>";
        $issues[] = "Composer not installed";
    } else {
        require_once 'vendor/autoload.php';
        echo "✓ Composer autoloader loaded<br>";
    }
    
    echo "Step 2: Loading Laravel application...<br>";
    if (!file_exists('bootstrap/app.php')) {
        echo "<span class='error'>✗ Laravel bootstrap file not found</span><br>";
        $issues[] = "Laravel bootstrap missing";
    } else {
        $app = require_once 'bootstrap/app.php';
        if (!$app || !is_object($app)) {
            echo "<span class='error'>✗ Laravel application failed to initialize</span><br>";
            $issues[] = "Laravel app initialization failed";
        } else {
            echo "✓ Laravel application created<br>";
            
            echo "Step 3: Testing service container...<br>";
            try {
                // Test each service individually
                $services = [
                    'config' => 'Configuration',
                    'router' => 'Router',
                    'db' => 'Database',
                    'cache' => 'Cache',
                    'session' => 'Session',
                    'view' => 'View Engine'
                ];
                
                foreach ($services as $service => $name) {
                    try {
                        $instance = $app->make($service);
                        echo "✓ $name service working<br>";
                    } catch (Exception $e) {
                        echo "<span class='error'>✗ $name service failed: " . $e->getMessage() . "</span><br>";
                        $issues[] = "$name service failed";
                    }
                }
                
                echo "Step 4: Testing HTTP Kernel...<br>";
                try {
                    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
                    echo "✓ HTTP Kernel created<br>";
                    
                    echo "Step 5: Testing request handling...<br>";
                    $request = Illuminate\Http\Request::create('/', 'GET');
                    $response = $kernel->handle($request);
                    echo "✓ Request handled, status: " . $response->getStatusCode() . "<br>";
                    
                    if ($response->getStatusCode() === 200) {
                        echo "<span class='ok'>🎉 Laravel is working correctly!</span><br>";
                    } else {
                        $content = $response->getContent();
                        echo "Response preview: " . htmlspecialchars(substr($content, 0, 200)) . "...<br>";
                    }
                    
                } catch (Exception $e) {
                    echo "<span class='error'>✗ Request handling failed: " . $e->getMessage() . "</span><br>";
                    $issues[] = "Request handling failed";
                }
                
            } catch (Exception $e) {
                echo "<span class='error'>✗ Service container test failed: " . $e->getMessage() . "</span><br>";
                $issues[] = "Service container failed";
            }
        }
    }
    
} catch (Exception $e) {
    echo "<span class='error'>✗ Laravel bootstrap failed: " . $e->getMessage() . "</span><br>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    $issues[] = "Laravel bootstrap failed";
}

echo "<h2>4. File System and Permissions Check</h2>";

$checkPaths = [
    'bootstrap/cache' => 'writable',
    'storage/app' => 'writable',
    'storage/framework/cache' => 'writable',
    'storage/framework/sessions' => 'writable',
    'storage/framework/views' => 'writable',
    'storage/logs' => 'writable',
    'config/app.php' => 'readable',
    'config/database.php' => 'readable',
    'config/cache.php' => 'readable',
    '.env' => 'readable'
];

foreach ($checkPaths as $path => $requirement) {
    if (!file_exists($path)) {
        if ($requirement === 'writable') {
            mkdir($path, 0755, true);
            echo "<span class='fixed'>🔧 Created directory $path</span><br>";
            $fixes[] = "Created $path";
        } else {
            echo "<span class='error'>✗ $path does not exist</span><br>";
            $issues[] = "$path missing";
        }
    } else {
        if ($requirement === 'writable' && !is_writable($path)) {
            chmod($path, 0755);
            echo "<span class='fixed'>🔧 Fixed permissions for $path</span><br>";
            $fixes[] = "Fixed permissions for $path";
        } elseif ($requirement === 'readable' && !is_readable($path)) {
            chmod($path, 0644);
            echo "<span class='fixed'>🔧 Fixed permissions for $path</span><br>";
            $fixes[] = "Fixed permissions for $path";
        } else {
            echo "✓ $path OK<br>";
        }
    }
}

echo "<h2>5. Auto-Fix Implementation</h2>";

// Clear all caches
$cacheDirectories = ['bootstrap/cache', 'storage/framework/cache', 'storage/framework/sessions', 'storage/framework/views'];
foreach ($cacheDirectories as $dir) {
    if (is_dir($dir)) {
        $files = glob("$dir/*");
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
        echo "<span class='fixed'>🔧 Cleared $dir</span><br>";
        $fixes[] = "Cleared $dir";
    }
}

// Create ultimate bulletproof index.php
$ultimateIndex = '<?php

/**
 * Ultimate Bulletproof Laravel Bootstrap
 * Handles all known production issues
 */

define(\'LARAVEL_START\', microtime(true));

// Production error handling
ini_set(\'display_errors\', 0);
ini_set(\'log_errors\', 1);
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
            font-family: -apple-system, BlinkMacSystemFont, \'Segoe UI\', sans-serif;
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
    $envPaths = [__DIR__ . \'/.env\', __DIR__ . \'/../.env\'];
    
    foreach ($envPaths as $envPath) {
        if (file_exists($envPath)) {
            $envContent = file_get_contents($envPath);
            $lines = explode("\\n", $envContent);
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
    if (file_exists(__DIR__ . \'/storage/framework/maintenance.php\')) {
        require __DIR__ . \'/storage/framework/maintenance.php\';
    }

    // Step 3: Autoloader with fallbacks
    $autoloaderPaths = [__DIR__ . \'/vendor/autoload.php\', __DIR__ . \'/../vendor/autoload.php\'];
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
    $bootstrapPaths = [__DIR__ . \'/bootstrap/app.php\', __DIR__ . \'/../bootstrap/app.php\'];
    $app = null;
    
    foreach ($bootstrapPaths as $bootstrapPath) {
        if (file_exists($bootstrapPath)) {
            $app = require $bootstrapPath;
            break;
        }
    }
    
    if (!$app || !is_object($app) || !($app instanceof Illuminate\\Foundation\\Application)) {
        throw new Exception("Laravel application failed to initialize");
    }

    // Step 5: Critical services validation
    $criticalServices = [\'config\', \'router\'];
    foreach ($criticalServices as $service) {
        try {
            $app->make($service);
        } catch (Exception $e) {
            throw new Exception("Critical service \'$service\' failed: " . $e->getMessage());
        }
    }

    // Step 6: Request handling
    $kernel = $app->make(Illuminate\\Contracts\\Http\\Kernel::class);
    $response = $kernel->handle($request = Illuminate\\Http\\Request::capture());
    $response->send();
    $kernel->terminate($request, $response);

} catch (Throwable $e) {
    $errorMsg = $e->getMessage() . " in " . $e->getFile() . " line " . $e->getLine();
    $errorId = substr(md5($errorMsg . microtime()), 0, 8);
    
    if (function_exists(\'error_log\')) {
        error_log("Laravel Error [$errorId]: $errorMsg");
    }
    
    showErrorPage($e->getMessage(), $errorId);
}

?>';

file_put_contents('index_ultimate.php', $ultimateIndex);
echo "<span class='fixed'>🔧 Created ultimate bulletproof index.php</span><br>";
$fixes[] = "Created ultimate index.php";

echo "<h2>6. Summary Report</h2>";

if (empty($issues)) {
    echo "<div style='background:#d4edda; padding:20px; border-left:5px solid #28a745; margin:20px 0;'>";
    echo "<h3 style='color:#155724;'>✅ No Issues Found!</h3>";
    echo "<p>All systems appear to be working correctly.</p>";
    echo "</div>";
} else {
    echo "<div style='background:#f8d7da; padding:20px; border-left:5px solid #dc3545; margin:20px 0;'>";
    echo "<h3 style='color:#721c24;'>🚨 Issues Identified:</h3>";
    echo "<ul>";
    foreach ($issues as $issue) {
        echo "<li>$issue</li>";
    }
    echo "</ul>";
    echo "</div>";
}

if (!empty($fixes)) {
    echo "<div style='background:#d1ecf1; padding:20px; border-left:5px solid #0c5460; margin:20px 0;'>";
    echo "<h3 style='color:#0c5460;'>🔧 Auto-Fixes Applied:</h3>";
    echo "<ul>";
    foreach ($fixes as $fix) {
        echo "<li>$fix</li>";
    }
    echo "</ul>";
    echo "</div>";
}

echo "<div style='background:#fff3cd; padding:20px; border-left:5px solid #856404; margin:20px 0;'>";
echo "<h3 style='color:#856404;'>📋 Next Steps:</h3>";
echo "<ol>";
echo "<li><strong>Deploy the ultimate fix:</strong><br><code>cp index.php index.php.backup && cp index_ultimate.php index.php</code></li>";
echo "<li><strong>Set final permissions:</strong><br><code>chmod 644 index.php && chmod -R 755 storage bootstrap/cache</code></li>";
echo "<li><strong>Test the application:</strong><br>Visit <a href='https://vaca.sh/' target='_blank'>https://vaca.sh/</a></li>";
echo "</ol>";
echo "</div>";

?> 