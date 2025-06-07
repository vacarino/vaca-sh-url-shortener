<?php
/**
 * Laravel Debug Script for Hostinger Shared Hosting
 * Upload this file to your public_html/ directory and visit it in browser
 * Delete after debugging is complete
 */

echo "<h1>Vaca.Sh Debug Information</h1>";
echo "<style>body{font-family:Arial;margin:20px;} .ok{color:green;} .error{color:red;} .warning{color:orange;}</style>";

// Check PHP version
echo "<h2>1. PHP Version</h2>";
$phpVersion = phpversion();
echo "PHP Version: <strong>$phpVersion</strong>";
if (version_compare($phpVersion, '8.1.0', '>=')) {
    echo " <span class='ok'>✓ Compatible</span>";
} else {
    echo " <span class='error'>✗ Requires PHP 8.1+</span>";
}

// Check required PHP extensions
echo "<h2>2. PHP Extensions</h2>";
$required = ['pdo', 'pdo_mysql', 'mbstring', 'openssl', 'tokenizer', 'xml', 'ctype', 'json', 'bcmath'];
foreach ($required as $ext) {
    echo "Extension $ext: ";
    if (extension_loaded($ext)) {
        echo "<span class='ok'>✓ Loaded</span><br>";
    } else {
        echo "<span class='error'>✗ Missing</span><br>";
    }
}

// Check file structure
echo "<h2>3. File Structure</h2>";
$files = [
    '.env' => 'Environment configuration (.env)',
    'vendor/autoload.php' => 'Composer autoloader (vendor/autoload.php)',
    'bootstrap/app.php' => 'Laravel bootstrap (bootstrap/app.php)',
    '.htaccess' => 'URL rewrite rules (.htaccess)',
    'index.php' => 'Entry point (index.php)'
];

foreach ($files as $file => $desc) {
    echo "$desc: ";
    if (file_exists($file)) {
        echo "<span class='ok'>✓ Exists</span><br>";
    } else {
        echo "<span class='error'>✗ Missing</span><br>";
    }
}

// Read and parse .env file
$config = [];
if (file_exists('.env')) {
    $env_lines = file('.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($env_lines as $line) {
        $line = trim($line);
        if (empty($line) || strpos($line, '#') === 0) {
            continue;
        }
        
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $config[trim($key)] = trim($value);
        }
    }
}

// Check environment configuration
echo "<h2>4. Environment Configuration</h2>";
echo "APP_KEY: ";
if (!empty($config['APP_KEY'])) {
    echo "<span class='ok'>✓ Set</span><br>";
} else {
    echo "<span class='error'>✗ Missing</span><br>";
}

echo "Database Name: ";
if (!empty($config['DB_DATABASE'])) {
    echo "<span class='ok'>✓ Set</span><br>";
} else {
    echo "<span class='error'>✗ Missing</span><br>";
}

// Check directory permissions
echo "<h2>5. Directory Permissions</h2>";
$dirs = ['storage', 'storage/logs', 'storage/framework', 'bootstrap/cache'];
foreach ($dirs as $dir) {
    if (is_dir($dir)) {
        $perms = substr(sprintf('%o', fileperms($dir)), -3);
        echo "$dir: $perms ";
        if (is_writable($dir)) {
            echo "<span class='ok'>✓ Writable</span><br>";
        } else {
            echo "<span class='error'>✗ Not writable</span><br>";
        }
    } else {
        echo "$dir: <span class='error'>✗ Missing</span><br>";
    }
}

// Test database connection using same method as db_test.php
echo "<h2>6. Database Connection</h2>";

// Check if all required database config is present
$required_db_config = ['DB_CONNECTION', 'DB_HOST', 'DB_DATABASE', 'DB_USERNAME', 'DB_PASSWORD'];
$missing_config = [];

foreach ($required_db_config as $key) {
    if (empty($config[$key])) {
        $missing_config[] = $key;
    }
}

if (!empty($missing_config)) {
    echo "Database Connection: <span class='error'>✗ Credentials not configured</span><br>";
    echo "Missing: " . implode(', ', $missing_config) . "<br>";
} else {
    // Test actual database connection
    try {
        $host = $config['DB_HOST'] ?? 'localhost';
        $port = $config['DB_PORT'] ?? '3306';
        $dbname = $config['DB_DATABASE'];
        $username = $config['DB_USERNAME'];
        $password = $config['DB_PASSWORD'];
        
        $dsn = "mysql:host=$host;port=$port;dbname=$dbname";
        $pdo = new PDO($dsn, $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        
        echo "Database Connection: <span class='ok'>✓ Connected successfully</span><br>";
        
        // Quick table count
        $stmt = $pdo->query("SHOW TABLES");
        $tables = $stmt->fetchAll();
        echo "Tables: <span class='ok'>" . count($tables) . " found</span><br>";
        
    } catch (PDOException $e) {
        echo "Database Connection: <span class='error'>✗ Connection failed</span><br>";
        echo "Error: " . $e->getMessage() . "<br>";
    }
}

// Test Laravel bootstrap
echo "<h2>7. Laravel Bootstrap Test</h2>";
try {
    if (file_exists('vendor/autoload.php') && file_exists('bootstrap/app.php')) {
        require_once 'vendor/autoload.php';
        $app = require_once 'bootstrap/app.php';
        echo "Laravel Bootstrap: <span class='ok'>✓ Loads successfully</span><br>";
    } else {
        echo "Laravel Bootstrap: <span class='error'>✗ Missing files</span><br>";
    }
} catch (Exception $e) {
    echo "Laravel Bootstrap: <span class='error'>✗ Failed</span><br>";
    echo "Error: " . $e->getMessage() . "<br>";
}

// Common fixes
echo "<h2>8. Common Fixes</h2>";
echo "<ol>";
echo "<li><strong>If .env is missing:</strong> Copy .env.template to .env</li>";
echo "<li><strong>If APP_KEY is missing:</strong> Generate with `php artisan key:generate --show` locally</li>";
echo "<li><strong>If permissions wrong:</strong> Set directories to 755, files to 644</li>";
echo "<li><strong>If database fails:</strong> Check credentials and import vaca_sh_database.sql</li>";
echo "<li><strong>If still failing:</strong> Check Hostinger error logs in control panel</li>";
echo "</ol>";

echo "<br><p><strong>After fixing issues, delete this debug.php file for security.</strong></p>";
?> 