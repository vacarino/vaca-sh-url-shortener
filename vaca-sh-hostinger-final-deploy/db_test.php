<?php
/**
 * Database Connection Test Script for Hostinger
 * This will help diagnose database connection issues
 * Upload this file and visit it in browser to test database connectivity
 */

echo "<h1>🔍 Database Connection Diagnostics</h1>";
echo "<style>body{font-family:Arial;margin:20px;} .ok{color:green;} .error{color:red;} .warning{color:orange;} .info{color:blue;} pre{background:#f5f5f5;padding:10px;border-radius:5px;}</style>";

echo "<h2>1. Environment File Check</h2>";

// Check if .env file exists
if (!file_exists('.env')) {
    echo "<span class='error'>✗ .env file not found!</span><br>";
    echo "<strong>Fix:</strong> Copy .env.template to .env<br><br>";
    exit;
}

echo "<span class='ok'>✓ .env file found</span><br><br>";

// Read and parse .env file
echo "<h2>2. Environment Configuration</h2>";
$env_lines = file('.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$config = [];

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

// Display database configuration (hide password)
$db_keys = ['DB_CONNECTION', 'DB_HOST', 'DB_PORT', 'DB_DATABASE', 'DB_USERNAME', 'DB_PASSWORD'];
foreach ($db_keys as $key) {
    $value = $config[$key] ?? 'NOT SET';
    if ($key === 'DB_PASSWORD') {
        $display_value = !empty($value) ? str_repeat('*', strlen($value)) : 'NOT SET';
    } else {
        $display_value = $value;
    }
    
    echo "<strong>$key:</strong> ";
    if ($value === 'NOT SET' || empty($value)) {
        echo "<span class='error'>$display_value</span><br>";
    } else {
        echo "<span class='ok'>$display_value</span><br>";
    }
}

echo "<br>";

// Check if all required database config is present
$required_db_config = ['DB_CONNECTION', 'DB_HOST', 'DB_DATABASE', 'DB_USERNAME', 'DB_PASSWORD'];
$missing_config = [];

foreach ($required_db_config as $key) {
    if (empty($config[$key])) {
        $missing_config[] = $key;
    }
}

if (!empty($missing_config)) {
    echo "<h2>❌ Missing Database Configuration</h2>";
    echo "<span class='error'>Missing: " . implode(', ', $missing_config) . "</span><br>";
    echo "<strong>Please add these to your .env file:</strong><br>";
    foreach ($missing_config as $key) {
        echo "<code>$key=your_value_here</code><br>";
    }
    echo "<br>";
    exit;
}

echo "<h2>3. Database Connection Tests</h2>";

// Test database connection
$host = $config['DB_HOST'] ?? 'localhost';
$port = $config['DB_PORT'] ?? '3306';
$dbname = $config['DB_DATABASE'];
$username = $config['DB_USERNAME'];
$password = $config['DB_PASSWORD'];

echo "<strong>Testing connection with:</strong><br>";
echo "Host: <code>$host:$port</code><br>";
echo "Database: <code>$dbname</code><br>";
echo "Username: <code>$username</code><br><br>";

// Test 1: Basic PDO connection
echo "<h3>Test 1: Basic Connection</h3>";
try {
    $dsn = "mysql:host=$host;port=$port;dbname=$dbname";
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    
    echo "<span class='ok'>✓ Connection successful!</span><br>";
    
    // Test database queries
    $stmt = $pdo->query("SELECT VERSION() as version");
    $result = $stmt->fetch();
    echo "MySQL Version: <span class='info'>" . $result['version'] . "</span><br>";
    
    // Check tables
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll();
    echo "Tables found: <span class='info'>" . count($tables) . "</span><br>";
    
    if (count($tables) > 0) {
        echo "<span class='ok'>✓ Database has tables</span><br>";
        echo "<strong>Tables:</strong><br>";
        foreach ($tables as $table) {
            $tableName = array_values($table)[0];
            echo "- $tableName<br>";
        }
    } else {
        echo "<span class='warning'>⚠ Database is empty - need to import SQL file</span><br>";
    }
    
} catch (PDOException $e) {
    echo "<span class='error'>✗ Connection failed!</span><br>";
    echo "<strong>Error:</strong> " . $e->getMessage() . "<br>";
    
    // Try alternative hosts for Hostinger
    echo "<br><h3>Test 2: Alternative Hostinger Hosts</h3>";
    
    $alternative_hosts = [
        'mysql.hostinger.com',
        'mysql.hostinger.co.uk', 
        'mysql.hostinger.in',
        '127.0.0.1',
        'localhost'
    ];
    
    foreach ($alternative_hosts as $alt_host) {
        if ($alt_host === $host) continue; // Skip already tested host
        
        echo "Trying host: <code>$alt_host</code> ... ";
        try {
            $alt_dsn = "mysql:host=$alt_host;port=$port;dbname=$dbname";
            $alt_pdo = new PDO($alt_dsn, $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_TIMEOUT => 5
            ]);
            echo "<span class='ok'>✓ Success!</span><br>";
            echo "<strong>Use this host in your .env:</strong> <code>DB_HOST=$alt_host</code><br>";
            break;
        } catch (PDOException $e) {
            echo "<span class='error'>✗ Failed</span><br>";
        }
    }
}

echo "<br><h2>4. Hostinger Specific Tips</h2>";
echo "<ul>";
echo "<li><strong>Database Host:</strong> Try 'localhost' first, then 'mysql.hostinger.com'</li>";
echo "<li><strong>Database Name:</strong> Usually includes your user ID prefix (e.g., u123456_dbname)</li>";
echo "<li><strong>Username:</strong> Usually includes your user ID prefix (e.g., u123456_username)</li>";
echo "<li><strong>Port:</strong> Usually 3306 (default)</li>";
echo "</ul>";

echo "<br><h2>5. Sample .env Configuration for Hostinger</h2>";
echo "<pre>";
echo "DB_CONNECTION=mysql\n";
echo "DB_HOST=localhost\n";
echo "DB_PORT=3306\n";
echo "DB_DATABASE=u123456_vaca_sh\n";
echo "DB_USERNAME=u123456_dbuser\n";
echo "DB_PASSWORD=your_database_password\n";
echo "</pre>";

echo "<br><h2>6. Next Steps</h2>";
echo "<ol>";
echo "<li>If connection failed, check your database credentials in Hostinger hPanel</li>";
echo "<li>Make sure database exists and user has proper privileges</li>";
echo "<li>Try different host values shown above</li>";
echo "<li>Import vaca_sh_database.sql via phpMyAdmin if database is empty</li>";
echo "<li>Once working, delete this db_test.php file</li>";
echo "</ol>";

echo "<br><p style='color:#666;'><em>After fixing database issues, delete this file for security.</em></p>";
?> 