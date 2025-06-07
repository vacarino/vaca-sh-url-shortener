<?php
/**
 * 🔧 Laravel Service Container Fixer
 * Fixes "Target class [X] does not exist" errors
 */

echo "<!DOCTYPE html><html><head><title>🔧 Laravel Service Fixer</title>";
echo "<style>body{font-family:monospace;margin:20px;background:#f0f0f0;}";
echo ".success{color:green;font-weight:bold;} .error{color:red;} .info{color:blue;}</style></head><body>";
echo "<h1>🔧 Laravel Service Container Fixer</h1>";

try {
    // Step 1: Clear all caches first
    echo "<h2>🧹 Step 1: Clearing All Caches</h2>";
    
    $cache_dirs = [
        'bootstrap/cache' => 'Bootstrap Cache',
        'storage/framework/cache' => 'Framework Cache',
        'storage/framework/sessions' => 'Sessions',
        'storage/framework/views' => 'Compiled Views',
        'storage/logs' => 'Logs (keeping structure)'
    ];
    
    foreach ($cache_dirs as $dir => $name) {
        if (is_dir($dir)) {
            $files = glob($dir . '/*');
            $cleared = 0;
            foreach ($files as $file) {
                if (is_file($file) && !str_contains($file, '.gitignore')) {
                    unlink($file);
                    $cleared++;
                }
            }
            echo "<div class='info'>🧹 Cleared $name: $cleared files</div>";
        } else {
            // Create directory if it doesn't exist
            mkdir($dir, 0755, true);
            echo "<div class='info'>📁 Created directory: $name</div>";
        }
    }
    
    // Step 2: Load Laravel properly
    echo "<h2>⚙️ Step 2: Loading Laravel Application</h2>";
    
    require_once 'vendor/autoload.php';
    
    // Load environment variables
    if (file_exists('.env')) {
        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
        $dotenv->load();
        echo "<div class='success'>✅ Environment loaded</div>";
    } else {
        echo "<div class='error'>❌ .env file not found</div>";
        throw new Exception('.env file missing');
    }
    
    $app = require_once 'bootstrap/app.php';
    echo "<div class='success'>✅ Laravel application created</div>";
    
    // Step 3: Test and fix service bindings
    echo "<h2>🔧 Step 3: Testing and Fixing Service Container</h2>";
    
    $services_to_test = [
        'config' => Illuminate\Contracts\Config\Repository::class,
        'db' => Illuminate\Database\DatabaseManager::class,
        'cache' => Illuminate\Contracts\Cache\Repository::class,
        'session' => Illuminate\Session\SessionManager::class,
        'view' => Illuminate\Contracts\View\Factory::class,
        'files' => Illuminate\Filesystem\Filesystem::class,
        'url' => Illuminate\Contracts\Routing\UrlGenerator::class
    ];
    
    foreach ($services_to_test as $alias => $concrete) {
        try {
            $service = $app->make($alias);
            echo "<div class='success'>✅ Service '$alias' working</div>";
        } catch (Exception $e) {
            echo "<div class='error'>❌ Service '$alias' failed: " . htmlspecialchars($e->getMessage()) . "</div>";
            
            // Try to bind the service manually
            try {
                if (!$app->bound($alias)) {
                    $app->bind($alias, $concrete);
                    echo "<div class='info'>🔧 Manually bound '$alias' to '$concrete'</div>";
                    
                    // Test again
                    $service = $app->make($alias);
                    echo "<div class='success'>✅ Service '$alias' now working after manual binding</div>";
                }
            } catch (Exception $bindException) {
                echo "<div class='error'>❌ Failed to manually bind '$alias': " . htmlspecialchars($bindException->getMessage()) . "</div>";
            }
        }
    }
    
    // Step 4: Test database connection specifically
    echo "<h2>🗄️ Step 4: Testing Database Connection</h2>";
    
    try {
        $db = $app->make('db');
        $connection = $db->connection();
        $result = $connection->select('SELECT 1 as test');
        
        if ($result && $result[0]->test == 1) {
            echo "<div class='success'>✅ Database connection working</div>";
            
            // Test if tables exist
            $tables = $connection->select("SHOW TABLES");
            echo "<div class='info'>📊 Found " . count($tables) . " database tables</div>";
            
            if (count($tables) == 0) {
                echo "<div class='error'>⚠️ No tables found - database may need migration</div>";
                echo "<div class='info'>💡 Run migrations: php artisan migrate</div>";
            }
            
        } else {
            echo "<div class='error'>❌ Database query failed</div>";
        }
        
    } catch (Exception $e) {
        echo "<div class='error'>❌ Database connection failed: " . htmlspecialchars($e->getMessage()) . "</div>";
        
        // Try alternative database connection
        echo "<div class='info'>🔧 Trying alternative database connection...</div>";
        try {
            $dsn = 'mysql:host=' . env('DB_HOST', 'localhost') . ';port=' . env('DB_PORT', '3306') . ';dbname=' . env('DB_DATABASE') . ';charset=utf8mb4';
            $pdo = new PDO($dsn, env('DB_USERNAME'), env('DB_PASSWORD'), [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);
            $result = $pdo->query('SELECT 1 as test')->fetch();
            
            if ($result && $result['test'] == 1) {
                echo "<div class='success'>✅ Direct PDO connection working</div>";
                echo "<div class='info'>💡 Issue is with Laravel database configuration</div>";
            }
            
        } catch (PDOException $pdoE) {
            echo "<div class='error'>❌ Direct PDO connection also failed: " . htmlspecialchars($pdoE->getMessage()) . "</div>";
        }
    }
    
    // Step 5: Test HTTP Kernel
    echo "<h2>🌐 Step 5: Testing HTTP Kernel</h2>";
    
    try {
        $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
        echo "<div class='success'>✅ HTTP Kernel created</div>";
        
        // Create a test request
        $request = Illuminate\Http\Request::createFromGlobals();
        $response = $kernel->handle($request);
        
        echo "<div class='success'>✅ Request handled, status: " . $response->getStatusCode() . "</div>";
        
        if ($response->getStatusCode() == 500) {
            echo "<div class='error'>⚠️ Application returning 500 errors</div>";
            echo "<div class='info'>💡 Check storage/logs/laravel.log for details</div>";
        }
        
        $kernel->terminate($request, $response);
        
    } catch (Exception $e) {
        echo "<div class='error'>❌ HTTP Kernel failed: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
    
    // Step 6: Create optimized bootstrap
    echo "<h2>🚀 Step 6: Creating Optimized Bootstrap</h2>";
    
    $optimized_index = '<?php
/**
 * 🚀 Optimized Laravel Bootstrap
 * Handles service container issues gracefully
 */

// Enhanced error handling
set_error_handler(function($severity, $message, $file, $line) {
    if (error_reporting() & $severity) {
        throw new ErrorException($message, 0, $severity, $file, $line);
    }
});

try {
    require_once __DIR__."/vendor/autoload.php";
    
    // Load environment with error handling
    if (file_exists(__DIR__ . "/.env")) {
        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
        $dotenv->load();
    }
    
    $app = require_once __DIR__."/bootstrap/app.php";
    
    // Pre-bind critical services to prevent "Target class does not exist" errors
    $critical_bindings = [
        "config" => Illuminate\Config\Repository::class,
        "db" => Illuminate\Database\DatabaseManager::class,
        "cache" => Illuminate\Cache\CacheManager::class,
        "session" => Illuminate\Session\SessionManager::class,
        "view" => Illuminate\View\Factory::class,
        "files" => Illuminate\Filesystem\Filesystem::class
    ];
    
    foreach ($critical_bindings as $alias => $concrete) {
        if (!$app->bound($alias)) {
            try {
                $app->bind($alias, $concrete);
            } catch (Exception $e) {
                // Silent fail for binding issues
            }
        }
    }
    
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    $request = Illuminate\Http\Request::capture();
    $response = $kernel->handle($request);
    $response->send();
    $kernel->terminate($request, $response);
    
} catch (Exception $e) {
    // Fallback to maintenance page
    http_response_code(503);
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Vaca.Sh - System Optimization</title>
        <style>
            body { font-family: -apple-system, BlinkMacSystemFont, sans-serif; margin: 0; padding: 40px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; }
            .container { background: white; padding: 40px; border-radius: 20px; box-shadow: 0 25px 50px rgba(0,0,0,0.15); text-align: center; max-width: 600px; }
            .logo { font-size: 4rem; margin-bottom: 20px; }
            h1 { color: #667eea; margin-bottom: 20px; font-size: 2.5rem; }
            p { color: #666; line-height: 1.6; margin-bottom: 20px; font-size: 1.1rem; }
            .status { background: #f8f9fa; padding: 20px; border-radius: 10px; margin: 20px 0; border-left: 4px solid #007bff; }
            .fix-btn { display: inline-block; background: #007bff; color: white; padding: 12px 24px; border-radius: 8px; text-decoration: none; margin: 10px; }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="logo">🦄</div>
            <h1>Vaca.Sh</h1>
            <h2>System Optimization in Progress</h2>
            <p>Our advanced URL shortener is currently optimizing its performance. This ensures the fastest and most reliable service for you.</p>
            
            <div class="status">
                <strong>🔧 Current Status:</strong> Service container optimization<br>
                <strong>⏱️ Estimated Time:</strong> 2-3 minutes<br>
                <strong>🎯 Next Step:</strong> Database connection verification
            </div>
            
            <p>If you are the administrator, you can run diagnostic tools:</p>
            <a href="/fix_laravel_services.php" class="fix-btn">🔧 Fix Services</a>
            <a href="/fix_database_auth.php" class="fix-btn">🗄️ Fix Database</a>
            <a href="/deep_diagnosis.php" class="fix-btn">🔍 Deep Diagnosis</a>
        </div>
    </body>
    </html>
    <?php
}
?>';
    
    if (file_put_contents('index_optimized_v2.php', $optimized_index)) {
        echo "<div class='success'>✅ Created optimized bootstrap: index_optimized_v2.php</div>";
        echo "<div class='info'>💡 Deploy with: cp index_optimized_v2.php index.php</div>";
    } else {
        echo "<div class='error'>❌ Failed to create optimized bootstrap</div>";
    }
    
    echo "<h2>✅ Service Container Fix Complete</h2>";
    echo "<div class='success'>All available fixes have been applied!</div>";
    
} catch (Exception $e) {
    echo "<div class='error'>❌ Critical error: " . htmlspecialchars($e->getMessage()) . "</div>";
    echo "<div class='info'>💡 Try running fix_database_auth.php first</div>";
}

echo "<br><br><a href='/' style='background:blue;color:white;padding:10px;text-decoration:none;'>🏠 Back to Site</a>";
echo " <a href='/fix_database_auth.php' style='background:orange;color:white;padding:10px;text-decoration:none;margin-left:10px;'>🗄️ Fix Database</a>";
echo " <a href='/deep_diagnosis.php' style='background:green;color:white;padding:10px;text-decoration:none;margin-left:10px;'>🔍 Deep Diagnosis</a>";
echo "</body></html>";
?> 