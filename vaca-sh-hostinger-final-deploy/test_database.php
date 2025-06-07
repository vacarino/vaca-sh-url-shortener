<?php

/**
 * Standalone Database Connection Test
 */

echo "<h1>🗄️ Database Connection Test</h1>";
echo "<style>body{font-family:Arial;margin:20px;} .ok{color:green;} .error{color:red;}</style>";

// Load environment variables
if (file_exists(".env")) {
    $envContent = file_get_contents(".env");
    $lines = explode("\n", $envContent);
    foreach ($lines as $line) {
        $line = trim($line);
        if (!empty($line) && strpos($line, "=") !== false && !str_starts_with($line, "#")) {
            list($key, $value) = explode("=", $line, 2);
            $_ENV[trim($key)] = trim($value);
        }
    }
}

$host = $_ENV["DB_HOST"] ?? "localhost";
$database = $_ENV["DB_DATABASE"] ?? "";
$username = $_ENV["DB_USERNAME"] ?? "";
$password = $_ENV["DB_PASSWORD"] ?? "";

echo "<h2>Connection Details:</h2>";
echo "<ul>";
echo "<li>Host: $host</li>";
echo "<li>Database: $database</li>";
echo "<li>Username: $username</li>";
echo "<li>Password: " . (empty($password) || $password === "your_db_pass" ? "<span class=\"error\">Not Set</span>" : "<span class=\"ok\">Set</span>") . "</li>";
echo "</ul>";

if (empty($password) || $password === "your_db_pass") {
    echo "<div style=\"background:#fff3cd; padding:15px; border-left:4px solid orange; margin:20px 0;\">";
    echo "<h3>⚠ Database Password Issue</h3>";
    echo "<p>The database password appears to be missing or set to the default placeholder.</p>";
    echo "<p>Please update the DB_PASSWORD value in the .env file with your actual database password.</p>";
    echo "</div>";
} else {
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        echo "<span class=\"ok\">✅ Database connection successful!</span><br>";
        
        // Test basic queries
        $stmt = $pdo->query("SELECT VERSION() as version");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "MySQL Version: " . $result["version"] . "<br>";
        
        // Check if required tables exist
        $tables = ["users", "short_urls", "click_logs"];
        foreach ($tables as $table) {
            $stmt = $pdo->prepare("SHOW TABLES LIKE ?");
            $stmt->execute([$table]);
            if ($stmt->rowCount() > 0) {
                echo "✓ Table \"$table\" exists<br>";
            } else {
                echo "<span class=\"error\">✗ Table \"$table\" missing</span><br>";
            }
        }
        
    } catch (Exception $e) {
        echo "<span class=\"error\">✗ Database connection failed: " . $e->getMessage() . "</span><br>";
    }
}

?>