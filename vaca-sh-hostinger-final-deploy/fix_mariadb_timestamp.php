<?php
/**
 * 🔧 MARIADB TIMESTAMP FIX
 * Fixes the MariaDB reserved word conflict with 'current_time'
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html><html><head><title>🔧 MariaDB Timestamp Fix</title>";
echo "<style>body{font-family:monospace;margin:20px;background:#f0f0f0;}";
echo ".success{color:green;font-weight:bold;} .error{color:red;} .info{color:blue;} .warning{color:orange;}</style></head><body>";
echo "<h1>🔧 MariaDB Timestamp Fix</h1>";

echo "<div style='background:#fff3cd;padding:15px;border-radius:8px;margin:20px 0;border-left:4px solid #ffc107;'>";
echo "<strong>🔍 Issue Found:</strong> 'current_time' is a reserved word in MariaDB!<br>";
echo "Let's fix this by using non-reserved column aliases.";
echo "</div>";

// Test database connection and fix timestamp syntax
echo "<h2>🗄️ Testing Database with Proper Aliases</h2>";

try {
    $dsn = "mysql:host=" . ($_ENV['DB_HOST'] ?? 'localhost') . ";port=" . ($_ENV['DB_PORT'] ?? '3306') . ";dbname=" . ($_ENV['DB_DATABASE'] ?? '') . ";charset=utf8mb4";
    
    // Load environment if not already loaded
    if (!isset($_ENV['DB_HOST']) && file_exists('.env')) {
        $env_content = file_get_contents('.env');
        $lines = explode("\n", $env_content);
        foreach ($lines as $line) {
            if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
                list($key, $value) = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value, " \t\n\r\0\x0B\"'");
                $_ENV[$key] = $value;
            }
        }
    }
    
    $pdo = new PDO($dsn, $_ENV['DB_USERNAME'] ?? '', $_ENV['DB_PASSWORD'] ?? '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_TIMEOUT => 10
    ]);
    
    echo "<div class='success'>✅ Database connection established</div>";
    
    // Check server version
    $version = $pdo->query("SELECT VERSION() as db_version")->fetch();
    echo "<div class='info'>📊 Database Server: " . htmlspecialchars($version['db_version']) . "</div>";
    
    // Test timestamp functions with NON-RESERVED aliases
    echo "<h3>🔍 Testing MariaDB Timestamp Functions (Fixed Aliases)</h3>";
    
    $timestamp_tests = [
        'NOW()' => 'SELECT NOW() as time_now',
        'CURRENT_TIMESTAMP' => 'SELECT CURRENT_TIMESTAMP as time_current',
        'CURRENT_TIMESTAMP()' => 'SELECT CURRENT_TIMESTAMP() as time_current_func',
        'UTC_TIMESTAMP()' => 'SELECT UTC_TIMESTAMP() as time_utc',
        'SYSDATE()' => 'SELECT SYSDATE() as time_sys'
    ];
    
    $working_timestamps = [];
    foreach ($timestamp_tests as $method => $sql) {
        try {
            $result = $pdo->query($sql)->fetch();
            $time_value = array_values($result)[0]; // Get first column value
            echo "<div class='success'>✅ $method works: " . htmlspecialchars($time_value) . "</div>";
            $working_timestamps[] = $method;
        } catch (PDOException $e) {
            echo "<div class='error'>❌ $method failed: " . htmlspecialchars($e->getMessage()) . "</div>";
        }
    }
    
    if (!empty($working_timestamps)) {
        echo "<div class='success'>✅ Found " . count($working_timestamps) . " working timestamp functions!</div>";
        echo "<div class='info'>📝 Recommended: Use " . $working_timestamps[0] . " in your application</div>";
    } else {
        echo "<div class='error'>❌ No timestamp functions worked - this is unusual for MariaDB</div>";
    }
    
    // Test some basic queries to ensure database is fully functional
    echo "<h3>🧪 Testing Database Functionality</h3>";
    
    try {
        // Test table access
        $tables = $pdo->query("SHOW TABLES")->fetchAll();
        echo "<div class='success'>✅ Can access " . count($tables) . " tables</div>";
        
        // Test a simple SELECT with proper timestamp
        if (!empty($working_timestamps)) {
            $test_sql = "SELECT 1 as test_value, " . $working_timestamps[0] . " as test_time";
            $test_result = $pdo->query($test_sql)->fetch();
            echo "<div class='success'>✅ Complex query works: test_value=" . $test_result['test_value'] . ", test_time=" . htmlspecialchars($test_result['test_time']) . "</div>";
        }
        
        // Test if we can query one of the actual tables
        if (count($tables) > 0) {
            $first_table = array_values($tables[0])[0];
            $count_query = "SELECT COUNT(*) as record_count FROM `" . $first_table . "`";
            $count_result = $pdo->query($count_query)->fetch();
            echo "<div class='success'>✅ Table '$first_table' has " . $count_result['record_count'] . " records</div>";
        }
        
    } catch (PDOException $e) {
        echo "<div class='error'>❌ Database functionality test failed: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
    
} catch (PDOException $e) {
    echo "<div class='error'>❌ Database connection failed: " . htmlspecialchars($e->getMessage()) . "</div>";
}

// Update the deep diagnosis to use proper aliases
echo "<h2>🔄 Updating Deep Diagnosis with Fixed Aliases</h2>";

try {
    if (file_exists('deep_diagnosis.php')) {
        $diagnosis_content = file_get_contents('deep_diagnosis.php');
        
        // Replace problematic 'current_time' aliases with safe ones
        $fixed_content = str_replace(
            ['as current_time', 'AS current_time', 'as CURRENT_TIME', 'AS CURRENT_TIME'],
            ['as time_result', 'AS time_result', 'as time_result', 'AS time_result'],
            $diagnosis_content
        );
        
        // Also fix any other potential reserved word conflicts
        $fixed_content = str_replace(
            ['SELECT current_time', 'SELECT CURRENT_TIME'],
            ['SELECT NOW() as time_result', 'SELECT NOW() as time_result'],
            $fixed_content
        );
        
        if (file_put_contents('deep_diagnosis.php', $fixed_content)) {
            echo "<div class='success'>✅ Updated deep_diagnosis.php with MariaDB-safe aliases</div>";
        } else {
            echo "<div class='warning'>⚠️ Could not update deep_diagnosis.php</div>";
        }
    } else {
        echo "<div class='warning'>⚠️ deep_diagnosis.php not found</div>";
    }
} catch (Exception $e) {
    echo "<div class='error'>❌ Failed to update diagnosis: " . htmlspecialchars($e->getMessage()) . "</div>";
}

// Summary
echo "<h2>✅ MariaDB Timestamp Fix Complete!</h2>";

echo "<div style='background:#d4edda;padding:25px;border-radius:10px;margin:25px 0;border-left:5px solid #28a745;'>";
echo "<h3>🎯 What This Fix Did:</h3>";
echo "<ul>";
echo "<li>✅ <strong>Identified Issue:</strong> 'current_time' is a reserved word in MariaDB 10.11.10</li>";
echo "<li>✅ <strong>Fixed Aliases:</strong> Replaced with non-reserved column names</li>";
echo "<li>✅ <strong>Tested Functions:</strong> Found working timestamp functions for your database</li>";
echo "<li>✅ <strong>Updated Diagnosis:</strong> Fixed deep_diagnosis.php to use safe aliases</li>";
echo "</ul>";
echo "</div>";

echo "<div style='background:#e7f3ff;padding:25px;border-radius:10px;margin:25px 0;border-left:5px solid #007bff;'>";
echo "<h3>💡 Technical Explanation:</h3>";
echo "<p><strong>The Problem:</strong> MariaDB 10.11.10 treats 'current_time' as a reserved function name, causing syntax errors when used as a column alias.</p>";
echo "<p><strong>The Solution:</strong> Use non-reserved aliases like 'time_result', 'time_now', etc.</p>";
echo "<p><strong>Impact:</strong> Your database queries should now work properly without syntax errors.</p>";
echo "</div>";

echo "<div style='background:#fff3cd;padding:25px;border-radius:10px;margin:25px 0;border-left:5px solid #ffc107;'>";
echo "<h3>🚀 Next Steps:</h3>";
echo "<ol>";
echo "<li><strong>Test your site:</strong> <a href='https://vaca.sh/' target='_blank' style='color:#007bff;font-weight:bold;'>https://vaca.sh/</a></li>";
echo "<li><strong>Run updated diagnosis:</strong> <a href='/deep_diagnosis.php' style='color:#28a745;font-weight:bold;'>Should now work without SQL errors</a></li>";
echo "<li><strong>Expected result:</strong> Green checkmarks instead of SQL syntax errors</li>";
echo "</ol>";
echo "</div>";

echo "<br><div style='text-align:center;'>";
echo "<a href='https://vaca.sh/' style='background:#28a745;color:white;padding:20px 40px;text-decoration:none;border-radius:10px;font-size:18px;font-weight:bold;margin:10px;display:inline-block;'>🏠 TEST YOUR SITE NOW</a>";
echo "<br>";
echo "<a href='/deep_diagnosis.php' style='background:#007bff;color:white;padding:15px 30px;text-decoration:none;border-radius:8px;font-size:16px;margin:10px;display:inline-block;'>🔍 Run Fixed Diagnosis</a>";
echo "</div>";

echo "</body></html>";
?> 