<?php
/**
 * 🔧 Advanced Database Authentication Fixer
 * Tests multiple password formats and updates .env automatically
 */

$passwords_to_test = [
    'Durimi,.123',
    'Durimi\,.123', 
    'Durimi\,\.123',
    '"Durimi,.123"',
    "'Durimi,.123'",
    urlencode('Durimi,.123'),
    base64_encode('Durimi,.123'),
    str_replace(',', '%2C', 'Durimi,.123'),
    str_replace('.', '%2E', 'Durimi,.123'),
    'Durimi%2C%2E123'
];

$db_config = [
    'host' => 'localhost',
    'port' => '3306', 
    'database' => 'u336307813_vaca',
    'username' => 'u336307813_vaca'
];

echo "<!DOCTYPE html><html><head><title>🔧 Database Auth Fixer</title>";
echo "<style>body{font-family:monospace;margin:20px;background:#f0f0f0;}";
echo ".success{color:green;font-weight:bold;} .error{color:red;} .info{color:blue;}</style></head><body>";
echo "<h1>🔧 Advanced Database Authentication Fixer</h1>";

echo "<h2>🔍 Testing Database Connection with Multiple Password Formats</h2>";

$working_password = null;

foreach ($passwords_to_test as $index => $password) {
    echo "<div><strong>Test " . ($index + 1) . ":</strong> ";
    
    try {
        $dsn = "mysql:host={$db_config['host']};port={$db_config['port']};dbname={$db_config['database']};charset=utf8mb4";
        $pdo = new PDO($dsn, $db_config['username'], $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_TIMEOUT => 5
        ]);
        
        // Test the connection with a simple query
        $stmt = $pdo->query("SELECT 1 as test");
        $result = $stmt->fetch();
        
        if ($result && $result['test'] == 1) {
            echo "<span class='success'>✅ SUCCESS!</span> Password format: " . htmlspecialchars($password);
            $working_password = $password;
            break;
        }
        
    } catch (PDOException $e) {
        echo "<span class='error'>❌ FAILED</span> - " . htmlspecialchars($e->getMessage());
    }
    echo "</div>";
}

if ($working_password) {
    echo "<h2>🎉 Database Connection Successful!</h2>";
    echo "<div class='success'>✅ Working password found: " . htmlspecialchars($working_password) . "</div>";
    
    // Update .env file with working password
    $env_content = file_get_contents('.env');
    if ($env_content !== false) {
        // Update or add DB_PASSWORD
        if (strpos($env_content, 'DB_PASSWORD=') !== false) {
            $env_content = preg_replace('/DB_PASSWORD=.*/', 'DB_PASSWORD=' . $working_password, $env_content);
        } else {
            $env_content .= "\nDB_PASSWORD=" . $working_password;
        }
        
        if (file_put_contents('.env', $env_content)) {
            echo "<div class='success'>✅ .env file updated with working password!</div>";
        } else {
            echo "<div class='error'>❌ Failed to update .env file</div>";
        }
    }
    
    // Test Laravel database configuration
    echo "<h3>🧪 Testing Laravel Database Configuration</h3>";
    try {
        require_once 'vendor/autoload.php';
        
        $app = require_once 'bootstrap/app.php';
        $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
        
        // Load environment 
        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
        $dotenv->load();
        
        $config = [
            'driver' => 'mysql',
            'host' => env('DB_HOST', 'localhost'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE'),
            'username' => env('DB_USERNAME'),
            'password' => env('DB_PASSWORD'),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
        ];
        
        $capsule = new \Illuminate\Database\Capsule\Manager;
        $capsule->addConnection($config);
        $capsule->setAsGlobal();
        $capsule->bootEloquent();
        
        $result = $capsule->select('SELECT 1 as test');
        
        if ($result && $result[0]->test == 1) {
            echo "<div class='success'>✅ Laravel database connection working!</div>";
            
            // Clear Laravel caches
            echo "<h3>🧹 Clearing Laravel Caches</h3>";
            $cache_dirs = [
                'bootstrap/cache',
                'storage/framework/cache/data',
                'storage/framework/sessions',
                'storage/framework/views'
            ];
            
            foreach ($cache_dirs as $dir) {
                if (is_dir($dir)) {
                    $files = glob($dir . '/*');
                    foreach ($files as $file) {
                        if (is_file($file)) {
                            unlink($file);
                        }
                    }
                    echo "<div class='info'>🧹 Cleared: $dir</div>";
                }
            }
            
            echo "<div class='success'>✅ All caches cleared!</div>";
            
        } else {
            echo "<div class='error'>❌ Laravel database connection failed</div>";
        }
        
    } catch (Exception $e) {
        echo "<div class='error'>❌ Laravel test failed: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
    
} else {
    echo "<h2>❌ Database Connection Failed</h2>";
    echo "<div class='error'>None of the password formats worked. Please check:</div>";
    echo "<ul>";
    echo "<li>Database credentials in Hostinger control panel</li>";
    echo "<li>Database user permissions</li>";
    echo "<li>Database server status</li>";
    echo "</ul>";
    
    echo "<h3>💡 Manual Fix Instructions:</h3>";
    echo "<ol>";
    echo "<li>Login to Hostinger control panel</li>";
    echo "<li>Go to MySQL Databases</li>";
    echo "<li>Check the exact database name, username, and password</li>";
    echo "<li>Copy the exact password (including special characters)</li>";
    echo "<li>Update the .env file with the correct password</li>";
    echo "</ol>";
}

echo "<br><br><a href='/' style='background:blue;color:white;padding:10px;text-decoration:none;'>🏠 Back to Site</a>";
echo " <a href='/deep_diagnosis.php' style='background:green;color:white;padding:10px;text-decoration:none;margin-left:10px;'>🔍 Deep Diagnosis</a>";
echo "</body></html>";
?> 