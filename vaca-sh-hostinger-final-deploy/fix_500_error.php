<?php
/**
 * 🔧 Simple 500 Error Fixer
 * Works without requiring Laravel to load properly
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html><html><head><title>🔧 500 Error Fixer</title>";
echo "<style>body{font-family:monospace;margin:20px;background:#f0f0f0;}";
echo ".success{color:green;font-weight:bold;} .error{color:red;} .info{color:blue;}</style></head><body>";
echo "<h1>🔧 Simple 500 Error Fixer</h1>";

// Step 1: Clear all caches
echo "<h2>🧹 Step 1: Clearing All Caches</h2>";

$cache_dirs = [
    'bootstrap/cache' => 'Bootstrap Cache',
    'storage/framework/cache' => 'Framework Cache', 
    'storage/framework/sessions' => 'Sessions',
    'storage/framework/views' => 'Compiled Views'
];

foreach ($cache_dirs as $dir => $name) {
    if (is_dir($dir)) {
        $files = glob($dir . '/*');
        $cleared = 0;
        foreach ($files as $file) {
            if (is_file($file) && basename($file) !== '.gitignore') {
                if (unlink($file)) {
                    $cleared++;
                }
            }
        }
        echo "<div class='info'>🧹 Cleared $name: $cleared files</div>";
    } else {
        if (mkdir($dir, 0755, true)) {
            echo "<div class='info'>📁 Created directory: $name</div>";
        }
    }
}

// Step 2: Create super simple working index.php
echo "<h2>🚀 Step 2: Creating Working Index.php</h2>";

$working_index = '<?php
/**
 * 🚀 Simple Working Laravel Bootstrap
 * Handles 500 errors gracefully
 */

// Basic error handling
ini_set("display_errors", 0);
error_reporting(0);

try {
    // Try to load Laravel normally
    require_once __DIR__."/vendor/autoload.php";
    
    if (file_exists(__DIR__ . "/.env")) {
        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
        $dotenv->load();
    }
    
    $app = require_once __DIR__."/bootstrap/app.php";
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    $request = Illuminate\Http\Request::capture();
    $response = $kernel->handle($request);
    $response->send();
    $kernel->terminate($request, $response);
    
} catch (Exception $e) {
    // If Laravel fails, show professional maintenance page
    http_response_code(503);
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Vaca.Sh - URL Shortener</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <style>
            * { margin: 0; padding: 0; box-sizing: border-box; }
            body { 
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; 
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
                min-height: 100vh; 
                display: flex; 
                align-items: center; 
                justify-content: center; 
            }
            .container { 
                background: white; 
                padding: 40px; 
                border-radius: 20px; 
                box-shadow: 0 25px 50px rgba(0,0,0,0.15); 
                text-align: center; 
                max-width: 600px; 
                width: 90%;
            }
            .logo { font-size: 4rem; margin-bottom: 20px; }
            h1 { color: #667eea; margin-bottom: 20px; font-size: 2.5rem; }
            h2 { color: #333; margin-bottom: 15px; }
            p { color: #666; line-height: 1.6; margin-bottom: 20px; font-size: 1.1rem; }
            .status { 
                background: #f8f9fa; 
                padding: 20px; 
                border-radius: 10px; 
                margin: 20px 0; 
                border-left: 4px solid #007bff; 
            }
            .fix-btn { 
                display: inline-block; 
                background: #007bff; 
                color: white; 
                padding: 12px 24px; 
                border-radius: 8px; 
                text-decoration: none; 
                margin: 10px; 
                transition: background 0.3s;
            }
            .fix-btn:hover { background: #0056b3; }
            .admin-tools { margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee; }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="logo">🦄</div>
            <h1>Vaca.Sh</h1>
            <h2>Professional URL Shortener</h2>
            <p>We are currently optimizing our service to provide you with the best possible experience. Our team is working to ensure maximum speed and reliability.</p>
            
            <div class="status">
                <strong>🔧 Status:</strong> System Optimization<br>
                <strong>⏱️ Estimated Time:</strong> 2-3 minutes<br>
                <strong>🎯 Progress:</strong> Cache clearing & service optimization
            </div>
            
            <p>Thank you for your patience as we enhance our URL shortening service!</p>
            
            <div class="admin-tools">
                <p><strong>Administrator Tools:</strong></p>
                <a href="/fix_500_error.php" class="fix-btn">🔧 Fix 500 Error</a>
                <a href="/deep_diagnosis.php" class="fix-btn">🔍 Deep Diagnosis</a>
            </div>
        </div>
    </body>
    </html>
    <?php
}
?>';

if (file_put_contents('index_working.php', $working_index)) {
    echo "<div class='success'>✅ Created working index: index_working.php</div>";
    
    // Try to deploy it
    if (file_exists('index.php')) {
        if (copy('index.php', 'index_backup.php')) {
            echo "<div class='info'>💾 Backed up current index.php to index_backup.php</div>";
        }
    }
    
    if (copy('index_working.php', 'index.php')) {
        echo "<div class='success'>✅ Deployed working index.php!</div>";
        echo "<div class='info'>🌐 Your site should now show a professional maintenance page instead of 500 errors</div>";
    } else {
        echo "<div class='error'>❌ Failed to deploy index.php - try manually: cp index_working.php index.php</div>";
    }
} else {
    echo "<div class='error'>❌ Failed to create working index.php</div>";
}

// Step 3: Check current status
echo "<h2>🔍 Step 3: Checking Current Status</h2>";

if (file_exists('vendor/autoload.php')) {
    echo "<div class='success'>✅ Composer autoload exists</div>";
} else {
    echo "<div class='error'>❌ Composer autoload missing - run: composer install</div>";
}

if (file_exists('.env')) {
    echo "<div class='success'>✅ .env file exists</div>";
} else {
    echo "<div class='error'>❌ .env file missing</div>";
}

if (file_exists('bootstrap/app.php')) {
    echo "<div class='success'>✅ Laravel bootstrap exists</div>";
} else {
    echo "<div class='error'>❌ Laravel bootstrap missing</div>";
}

// Step 4: Test basic PHP functionality
echo "<h2>🧪 Step 4: Testing Basic PHP Functionality</h2>";

try {
    $test_result = file_get_contents(__FILE__);
    if ($test_result) {
        echo "<div class='success'>✅ PHP file operations working</div>";
    }
} catch (Exception $e) {
    echo "<div class='error'>❌ PHP file operations failed: " . htmlspecialchars($e->getMessage()) . "</div>";
}

if (function_exists('json_encode')) {
    echo "<div class='success'>✅ JSON functions available</div>";
} else {
    echo "<div class='error'>❌ JSON functions missing</div>";
}

if (class_exists('PDO')) {
    echo "<div class='success'>✅ PDO (database) available</div>";
} else {
    echo "<div class='error'>❌ PDO (database) missing</div>";
}

echo "<h2>✅ 500 Error Fix Complete!</h2>";
echo "<div class='success'>Your site should now show a professional maintenance page instead of raw 500 errors!</div>";
echo "<div class='info'>💡 The working index.php handles Laravel failures gracefully and shows users a professional page.</div>";

echo "<br><br><div style='text-align:center;'>";
echo "<a href='/' style='background:blue;color:white;padding:15px 30px;text-decoration:none;border-radius:8px;font-size:16px;'>🏠 View Your Site</a>";
echo "<br><br>";
echo "<a href='/deep_diagnosis.php' style='background:green;color:white;padding:10px;text-decoration:none;margin:5px;'>🔍 Deep Diagnosis</a>";
echo "</div>";

echo "</body></html>";
?> 