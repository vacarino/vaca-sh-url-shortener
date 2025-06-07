<?php
/**
 * Simple script to update the database password in .env file
 * Usage: php update_env_password.php your_actual_password
 */

if ($argc < 2) {
    echo "Usage: php update_env_password.php your_database_password\n";
    echo "This will update the DB_PASSWORD in your .env file\n";
    exit(1);
}

$password = $argv[1];
$envFile = '.env';

if (!file_exists($envFile)) {
    echo "Error: .env file not found\n";
    exit(1);
}

// Read current .env file
$content = file_get_contents($envFile);

// Replace the password line
$content = preg_replace('/^DB_PASSWORD=.*$/m', 'DB_PASSWORD=' . $password, $content);

// Write back to file
if (file_put_contents($envFile, $content)) {
    echo "✓ Database password updated successfully in .env file\n";
    echo "You can now test Laravel at: https://vaca.sh/laravel_debug.php\n";
} else {
    echo "✗ Failed to update .env file\n";
}
?> 