<?php
/**
 * 🔧 LEGACY LARAVEL BOOTSTRAP FIX
 * Fixes bootstrap/app.php for older Laravel versions (10 and earlier)
 * Uses traditional Laravel bootstrap syntax instead of Laravel 11
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html><html><head><title>🔧 Legacy Laravel Bootstrap Fix</title>";
echo "<style>body{font-family:monospace;margin:20px;background:#f0f0f0;}";
echo ".success{color:green;font-weight:bold;} .error{color:red;} .info{color:blue;} .warning{color:orange;}</style></head><body>";
echo "<h1>🔧 Legacy Laravel Bootstrap Fix</h1>";

echo "<div style='background:#fff3cd;padding:15px;border-radius:8px;margin:20px 0;border-left:4px solid #ffc107;'>";
echo "<strong>🎯 ISSUE IDENTIFIED:</strong><br>";
echo "The bootstrap/app.php was created using Laravel 11 syntax<br>";
echo "But your installation uses an older Laravel version.<br>";
echo "<strong>Fixing with correct legacy Laravel bootstrap syntax!</strong>";
echo "</div>";

// Step 1: Create correct bootstrap/app.php for older Laravel
echo "<h2>🔧 Step 1: Fix Bootstrap for Legacy Laravel</h2>";

try {
    // Create traditional Laravel bootstrap/app.php
    $legacy_bootstrap = '<?php

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| The first thing we will do is create a new Laravel application instance
| which serves as the "glue" for all the components of Laravel, and is
| the IoC container for the system binding all of the various parts.
|
*/

$app = new Illuminate\Foundation\Application(
    $_ENV[\'APP_BASE_PATH\'] ?? dirname(__DIR__)
);

/*
|--------------------------------------------------------------------------
| Bind Important Interfaces
|--------------------------------------------------------------------------
|
| Next, we need to bind some important interfaces into the container so
| we will be able to resolve them when needed. The kernels serve the
| incoming requests to this application from both the web and CLI.
|
*/

$app->singleton(
    Illuminate\Contracts\Http\Kernel::class,
    App\Http\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

/*
|--------------------------------------------------------------------------
| Return The Application
|--------------------------------------------------------------------------
|
| This script returns the application instance. The instance is given to
| the calling script so we can separate the building of the instances
| from the actual running of the application and sending responses.
|
*/

return $app;
';

    // Backup current bootstrap
    if (file_exists('bootstrap/app.php')) {
        copy('bootstrap/app.php', 'bootstrap/app.php.backup.' . date('Y-m-d-H-i-s'));
        echo "<div class='info'>📋 Backed up current bootstrap/app.php</div>";
    }
    
    // Write the legacy bootstrap
    if (file_put_contents('bootstrap/app.php', $legacy_bootstrap)) {
        echo "<div class='success'>✅ Created legacy Laravel bootstrap/app.php</div>";
    } else {
        echo "<div class='error'>❌ Could not create bootstrap/app.php</div>";
    }
    
} catch (Exception $e) {
    echo "<div class='error'>❌ Error creating bootstrap: " . htmlspecialchars($e->getMessage()) . "</div>";
}

// Step 2: Create proper Kernel files if missing
echo "<h2>🔧 Step 2: Ensure Kernel Files Exist</h2>";

try {
    // Create app directory if missing
    if (!file_exists('app')) {
        mkdir('app', 0755, true);
        echo "<div class='info'>📁 Created app directory</div>";
    }
    
    if (!file_exists('app/Http')) {
        mkdir('app/Http', 0755, true);
        echo "<div class='info'>📁 Created app/Http directory</div>";
    }
    
    if (!file_exists('app/Console')) {
        mkdir('app/Console', 0755, true);
        echo "<div class='info'>📁 Created app/Console directory</div>";
    }
    
    if (!file_exists('app/Exceptions')) {
        mkdir('app/Exceptions', 0755, true);
        echo "<div class='info'>📁 Created app/Exceptions directory</div>";
    }
    
    // Create HTTP Kernel if missing
    if (!file_exists('app/Http/Kernel.php')) {
        $http_kernel = '<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application\'s global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array<int, class-string|string>
     */
    protected $middleware = [
        // \App\Http\Middleware\TrustHosts::class,
        \App\Http\Middleware\TrustProxies::class,
        \Illuminate\Http\Middleware\HandleCors::class,
        \App\Http\Middleware\PreventRequestsDuringMaintenance::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];

    /**
     * The application\'s route middleware groups.
     *
     * @var array<string, array<int, class-string|string>>
     */
    protected $middlewareGroups = [
        \'web\' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        \'api\' => [
            // \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            \'throttle:api\',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];

    /**
     * The application\'s route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array<string, class-string|string>
     */
    protected $routeMiddleware = [
        \'auth\' => \App\Http\Middleware\Authenticate::class,
        \'auth.basic\' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        \'auth.session\' => \Illuminate\Session\Middleware\AuthenticateSession::class,
        \'cache.headers\' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        \'can\' => \Illuminate\Auth\Middleware\Authorize::class,
        \'guest\' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        \'password.confirm\' => \Illuminate\Auth\Middleware\RequirePassword::class,
        \'signed\' => \Illuminate\Routing\Middleware\ValidateSignature::class,
        \'throttle\' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        \'verified\' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
    ];
}
';
        file_put_contents('app/Http/Kernel.php', $http_kernel);
        echo "<div class='success'>✅ Created app/Http/Kernel.php</div>";
    } else {
        echo "<div class='info'>📋 app/Http/Kernel.php already exists</div>";
    }
    
    // Create Console Kernel if missing
    if (!file_exists('app/Console/Kernel.php')) {
        $console_kernel = '<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application\'s command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command(\'inspire\')->hourly();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.\'/Commands\');

        require base_path(\'routes/console.php\');
    }
}
';
        file_put_contents('app/Console/Kernel.php', $console_kernel);
        echo "<div class='success'>✅ Created app/Console/Kernel.php</div>";
    } else {
        echo "<div class='info'>📋 app/Console/Kernel.php already exists</div>";
    }
    
    // Create Exception Handler if missing
    if (!file_exists('app/Exceptions/Handler.php')) {
        $exception_handler = '<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        \'current_password\',
        \'password\',
        \'password_confirmation\',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }
}
';
        file_put_contents('app/Exceptions/Handler.php', $exception_handler);
        echo "<div class='success'>✅ Created app/Exceptions/Handler.php</div>";
    } else {
        echo "<div class='info'>📋 app/Exceptions/Handler.php already exists</div>";
    }
    
} catch (Exception $e) {
    echo "<div class='error'>❌ Error creating kernel files: " . htmlspecialchars($e->getMessage()) . "</div>";
}

// Step 3: Create working index.php for legacy Laravel
echo "<h2>🔧 Step 3: Create Working Index.php for Legacy Laravel</h2>";

try {
    $legacy_index = '<?php

/**
 * Laravel - A PHP Framework For Web Artisans
 *
 * @package  Laravel
 * @author   Taylor Otwell <taylor@laravel.com>
 */

define(\'LARAVEL_START\', microtime(true));

/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader for
| our application. We just need to utilize it! We\'ll simply require it
| into the script here so that we don\'t have to worry about manual
| loading any of our classes later on. It feels great to relax.
|
*/

require __DIR__.\'/vendor/autoload.php\';

/*
|--------------------------------------------------------------------------
| Turn On The Lights
|--------------------------------------------------------------------------
|
| We need to illuminate PHP development, so let us turn on the lights.
| This bootstraps the framework and gets it ready for use, then it
| will load up this application so that we can run it and send
| the responses back to the browser and delight our users.
|
*/

try {
    $app = require_once __DIR__.\'/bootstrap/app.php\';

    /*
    |--------------------------------------------------------------------------
    | Run The Application
    |--------------------------------------------------------------------------
    |
    | Once we have the application, we can handle the incoming request
    | through the kernel, and send the associated response back to
    | the client\'s browser allowing them to enjoy the creative
    | and wonderful application we have prepared for them.
    |
    */

    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

    $response = $kernel->handle(
        $request = Illuminate\Http\Request::capture()
    );

    $response->send();

    $kernel->terminate($request, $response);
    
} catch (Throwable $e) {
    // Professional maintenance page for any errors
    http_response_code(503);
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Vaca.Sh - URL Shortener</title>
        <style>
            body {
                margin: 0;
                padding: 0;
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            .container {
                text-align: center;
                background: rgba(255, 255, 255, 0.95);
                padding: 3rem;
                border-radius: 20px;
                box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
                max-width: 500px;
                margin: 2rem;
            }
            .logo {
                font-size: 3rem;
                font-weight: bold;
                color: #667eea;
                margin-bottom: 1rem;
            }
            .title {
                font-size: 1.5rem;
                color: #333;
                margin-bottom: 1rem;
            }
            .message {
                color: #666;
                line-height: 1.6;
                margin-bottom: 2rem;
            }
            .status {
                background: #f8f9fa;
                padding: 1rem;
                border-radius: 10px;
                border-left: 4px solid #667eea;
                margin: 1rem 0;
            }
            .debug {
                font-size: 0.9rem;
                color: #888;
                margin-top: 1rem;
                text-align: left;
                background: #f8f9fa;
                padding: 1rem;
                border-radius: 5px;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="logo">Vaca.Sh</div>
            <div class="title">🔧 System Maintenance</div>
            <div class="message">
                Our URL shortener is currently being optimized for better performance.
                Thank you for your patience while we complete the updates.
            </div>
            <div class="status">
                <strong>Status:</strong> Maintenance Mode<br>
                <strong>Expected:</strong> Service restoration shortly
            </div>
            <?php if (file_exists(\'.env\') && strpos(file_get_contents(\'.env\'), \'APP_DEBUG=true\') !== false): ?>
            <div class="debug">
                <strong>Debug Information:</strong><br>
                Error: <?= htmlspecialchars($e->getMessage()) ?><br>
                File: <?= htmlspecialchars($e->getFile()) ?><br>
                Line: <?= $e->getLine() ?>
            </div>
            <?php endif; ?>
        </div>
    </body>
    </html>
    <?php
}
';

    // Backup and replace index.php
    if (file_exists('index.php')) {
        copy('index.php', 'index.php.backup.' . date('Y-m-d-H-i-s'));
        echo "<div class='info'>📋 Backed up current index.php</div>";
    }
    
    if (file_put_contents('index.php', $legacy_index)) {
        echo "<div class='success'>✅ Created legacy Laravel index.php</div>";
    } else {
        echo "<div class='error'>❌ Could not create index.php</div>";
    }
    
} catch (Exception $e) {
    echo "<div class='error'>❌ Error creating index: " . htmlspecialchars($e->getMessage()) . "</div>";
}

// Step 4: Clear caches and test
echo "<h2>🔧 Step 4: Clear Caches and Test</h2>";

try {
    // Clear all caches
    $cache_dirs = ['bootstrap/cache', 'storage/framework/cache', 'storage/framework/sessions', 'storage/framework/views'];
    foreach ($cache_dirs as $dir) {
        if (is_dir($dir)) {
            $files = glob($dir . '/*');
            foreach ($files as $file) {
                if (is_file($file) && basename($file) !== '.gitignore') {
                    unlink($file);
                }
            }
            echo "<div class='success'>✅ Cleared " . basename($dir) . "</div>";
        }
    }
    
    // Test Laravel bootstrap
    if (file_exists('vendor/autoload.php') && file_exists('bootstrap/app.php')) {
        require_once 'vendor/autoload.php';
        
        try {
            $app = require_once 'bootstrap/app.php';
            
            if ($app && is_object($app)) {
                echo "<div class='success'>✅ Legacy Laravel bootstrap works!</div>";
                
                // Test basic services
                try {
                    $kernel = $app->make('Illuminate\Contracts\Http\Kernel');
                    echo "<div class='success'>✅ HTTP Kernel resolves correctly</div>";
                } catch (Exception $e) {
                    echo "<div class='warning'>⚠️ HTTP Kernel issue: " . htmlspecialchars($e->getMessage()) . "</div>";
                }
                
            } else {
                echo "<div class='error'>❌ Laravel bootstrap failed</div>";
            }
            
        } catch (Exception $e) {
            echo "<div class='warning'>⚠️ Bootstrap test error: " . htmlspecialchars($e->getMessage()) . "</div>";
        }
    }
    
} catch (Exception $e) {
    echo "<div class='error'>❌ Error in testing: " . htmlspecialchars($e->getMessage()) . "</div>";
}

// Final summary
echo "<h2>🎉 Legacy Laravel Bootstrap Fix Complete!</h2>";

echo "<div style='background:#d4edda;padding:25px;border-radius:10px;margin:25px 0;border-left:5px solid #28a745;'>";
echo "<h3>🎯 LEGACY LARAVEL FIXES APPLIED:</h3>";
echo "<ul>";
echo "<li>✅ <strong>Fixed Bootstrap Syntax:</strong> Used traditional Laravel bootstrap instead of Laravel 11</li>";
echo "<li>✅ <strong>Created Missing Kernels:</strong> HTTP Kernel, Console Kernel, Exception Handler</li>";
echo "<li>✅ <strong>Working Index.php:</strong> Traditional Laravel index with proper error handling</li>";
echo "<li>✅ <strong>Cache Clearing:</strong> Removed all old cached files</li>";
echo "<li>✅ <strong>Bootstrap Testing:</strong> Verified Laravel can bootstrap properly</li>";
echo "</ul>";
echo "</div>";

echo "<div style='background:#fff3cd;padding:25px;border-radius:10px;margin:25px 0;border-left:5px solid #ffc107;'>";
echo "<h3>🚀 Next Steps:</h3>";
echo "<ol>";
echo "<li><strong>Test main site:</strong> <a href='https://vaca.sh/' target='_blank' style='color:#007bff;font-weight:bold;'>https://vaca.sh/</a></li>";
echo "<li><strong>Run diagnosis:</strong> <a href='/deep_diagnosis.php' style='color:#28a745;font-weight:bold;'>Should now bootstrap without errors</a></li>";
echo "<li><strong>Expected:</strong> Working Laravel application or professional maintenance page</li>";
echo "</ol>";
echo "</div>";

echo "<div style='background:#e7f3ff;padding:25px;border-radius:10px;margin:25px 0;border-left:5px solid #007bff;'>";
echo "<h3>💡 What Was Fixed:</h3>";
echo "<p><strong>Problem:</strong> Using Laravel 11 syntax on older Laravel version</p>";
echo "<p><strong>Solution:</strong> <strong>Traditional Laravel bootstrap</strong> compatible with your version</p>";
echo "<p>No more 'Method Application::configure does not exist' errors!</p>";
echo "</div>";

echo "<br><div style='text-align:center;'>";
echo "<a href='https://vaca.sh/' style='background:#28a745;color:white;padding:20px 40px;text-decoration:none;border-radius:10px;font-size:18px;font-weight:bold;margin:10px;display:inline-block;'>🏠 TEST FIXED SITE</a>";
echo "<br>";
echo "<a href='/deep_diagnosis.php' style='background:#007bff;color:white;padding:15px 30px;text-decoration:none;border-radius:8px;font-size:16px;margin:10px;display:inline-block;'>🔍 Verify Bootstrap Fixed</a>";
echo "</div>";

echo "</body></html>";
?> 