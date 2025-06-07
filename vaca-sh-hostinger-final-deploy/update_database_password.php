<?php
/**
 * Update Database Password in .env file
 * Safe way to update the database credentials
 */

echo "<h1>🔐 Database Password Update</h1>";
echo "<style>body{font-family:Arial;margin:20px;} .ok{color:green;} .error{color:red;} .warning{color:orange;} .info{color:blue;}</style>";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['db_password'])) {
    $newPassword = $_POST['db_password'];
    
    if (empty($newPassword)) {
        echo "<span class='error'>✗ Password cannot be empty</span><br>";
    } else {
        $envPath = '.env';
        if (file_exists($envPath)) {
            $envContent = file_get_contents($envPath);
            $originalContent = $envContent;
            
            // Update database password
            $envContent = preg_replace('/^DB_PASSWORD=.*$/m', "DB_PASSWORD=$newPassword", $envContent);
            
            // Write back to .env file
            if (file_put_contents($envPath, $envContent)) {
                echo "<span class='ok'>✅ Database password updated successfully!</span><br>";
                
                // Test the database connection
                echo "<h3>Testing Database Connection...</h3>";
                
                try {
                    // Load environment variables
                    $lines = explode("\n", $envContent);
                    foreach ($lines as $line) {
                        $line = trim($line);
                        if (!empty($line) && strpos($line, "=") !== false && !str_starts_with($line, "#")) {
                            list($key, $value) = explode("=", $line, 2);
                            $_ENV[trim($key)] = trim($value);
                            putenv(trim($key) . "=" . trim($value));
                        }
                    }
                    
                    $host = $_ENV['DB_HOST'] ?? 'localhost';
                    $database = $_ENV['DB_DATABASE'] ?? '';
                    $username = $_ENV['DB_USERNAME'] ?? '';
                    $password = $_ENV['DB_PASSWORD'] ?? '';
                    
                    echo "Connecting to: $host as $username to database $database<br>";
                    
                    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
                    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    
                    echo "<span class='ok'>✅ Database connection successful!</span><br>";
                    
                    // Test a simple query
                    $stmt = $pdo->query("SELECT COUNT(*) as count FROM information_schema.tables WHERE table_schema = '$database'");
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                    echo "Database has " . $result['count'] . " tables<br>";
                    
                } catch (Exception $e) {
                    echo "<span class='error'>✗ Database connection failed: " . $e->getMessage() . "</span><br>";
                    
                    // Restore original .env content
                    file_put_contents($envPath, $originalContent);
                    echo "<span class='warning'>⚠ Restored original .env file due to connection failure</span><br>";
                }
            } else {
                echo "<span class='error'>✗ Failed to update .env file</span><br>";
            }
        } else {
            echo "<span class='error'>✗ .env file not found</span><br>";
        }
    }
} else {
    // Show current database configuration
    echo "<h2>Current Database Configuration:</h2>";
    
    if (file_exists('.env')) {
        $envContent = file_get_contents('.env');
        $lines = explode("\n", $envContent);
        
        $dbConfig = [];
        foreach ($lines as $line) {
            $line = trim($line);
            if (strpos($line, 'DB_') === 0 && strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                $dbConfig[trim($key)] = trim($value);
            }
        }
        
        echo "<table border='1' style='border-collapse:collapse; width:100%; margin:20px 0;'>";
        echo "<tr><th style='padding:10px; background:#f5f5f5;'>Setting</th><th style='padding:10px; background:#f5f5f5;'>Value</th></tr>";
        
        foreach ($dbConfig as $key => $value) {
            $displayValue = $key === 'DB_PASSWORD' ? (empty($value) || $value === 'your_db_pass' ? '<span class="error">Not Set</span>' : '<span class="ok">Set (hidden)</span>') : $value;
            echo "<tr><td style='padding:10px;'>$key</td><td style='padding:10px;'>$displayValue</td></tr>";
        }
        echo "</table>";
        
        // Check if password needs to be updated
        if (isset($dbConfig['DB_PASSWORD']) && $dbConfig['DB_PASSWORD'] === 'your_db_pass') {
            echo "<div style='background:#fff3cd; padding:15px; border-left:4px solid orange; margin:20px 0;'>";
            echo "<h3>⚠ Database Password Needs Update</h3>";
            echo "<p>The database password is still set to the placeholder value 'your_db_pass'.</p>";
            echo "<p>Please enter your actual database password below:</p>";
            echo "</div>";
        }
    }
    
    // Show password update form
    echo "<h2>Update Database Password:</h2>";
    echo "<form method='POST' style='background:#f9f9f9; padding:20px; border-radius:5px; margin:20px 0;'>";
    echo "<p><label for='db_password'>Database Password:</label></p>";
    echo "<p><input type='password' name='db_password' id='db_password' style='width:300px; padding:8px; font-size:14px;' placeholder='Enter your database password' required></p>";
    echo "<p><input type='submit' value='Update Password' style='background:#007cba; color:white; padding:10px 20px; border:none; border-radius:3px; cursor:pointer;'></p>";
    echo "<p style='font-size:12px; color:#666;'>Note: This will test the database connection and update the .env file only if the connection is successful.</p>";
    echo "</form>";
    
    echo "<div style='background:#d1ecf1; padding:15px; border-left:4px solid blue; margin:20px 0;'>";
    echo "<h3>💡 Alternative Method:</h3>";
    echo "<p>You can also update the database password manually by editing the .env file:</p>";
    echo "<ol>";
    echo "<li>Open the .env file in a text editor</li>";
    echo "<li>Find the line that starts with <code>DB_PASSWORD=</code></li>";
    echo "<li>Replace 'your_db_pass' with your actual database password</li>";
    echo "<li>Save the file</li>";
    echo "</ol>";
    echo "</div>";
}

?> 