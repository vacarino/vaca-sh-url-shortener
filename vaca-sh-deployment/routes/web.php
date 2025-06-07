<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ShortUrlController;
use App\Http\Controllers\RedirectController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\InviteCodeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/test', function () {
    return response()->json(['status' => 'success', 'message' => 'Laravel is working']);
});

// Analytics test route
Route::get('/test-analytics', function () {
    $shortUrl = \App\Models\ShortUrl::with('clickLogs')->first();
    if (!$shortUrl) {
        return response()->json(['error' => 'No short URLs found']);
    }
    
    return response()->json([
        'short_url' => [
            'id' => $shortUrl->id,
            'short_code' => $shortUrl->short_code,
            'clicks' => $shortUrl->clicks,
            'click_logs_count' => $shortUrl->clickLogs->count(),
        ],
        'recent_logs' => $shortUrl->clickLogs()->latest()->limit(5)->get(),
        'analytics' => [
            'total_clicks' => $shortUrl->clicks,
            'unique_countries' => $shortUrl->clickLogs()->distinct('country')->count('country'),
            'top_countries' => $shortUrl->clickLogs()
                ->selectRaw('country, COUNT(*) as count')
                ->groupBy('country')
                ->orderBy('count', 'desc')
                ->limit(3)
                ->get(),
        ]
    ]);
});

// Test click route (for testing analytics)
Route::get('/test-click/{code}', function ($code) {
    $shortUrl = \App\Models\ShortUrl::where('short_code', $code)->first();
    if (!$shortUrl) {
        return response()->json(['error' => 'Short URL not found']);
    }
    
    // Create a test click log
    $clickLog = \App\Models\ClickLog::create([
        'short_url_id' => $shortUrl->id,
        'ip_address' => '192.168.1.' . rand(1, 254),
        'user_agent' => 'Test Browser Agent',
        'country' => ['USA', 'Canada', 'UK', 'France', 'Germany'][rand(0, 4)],
        'browser' => ['Chrome', 'Firefox', 'Safari', 'Edge'][rand(0, 3)],
        'platform' => ['Windows', 'macOS', 'Linux', 'Android'][rand(0, 3)],
    ]);
    
    // Increment click count
    $shortUrl->incrementClicks();
    $shortUrl->refresh();
    
    return response()->json([
        'success' => true,
        'message' => 'Test click logged',
        'click_log' => $clickLog,
        'new_click_count' => $shortUrl->clicks
    ]);
});

// Analytics debug route (test analytics display)
Route::get('/debug-analytics/{shortCode}', function ($shortCode) {
    $shortUrl = \App\Models\ShortUrl::where('short_code', $shortCode)->with('clickLogs')->first();
    if (!$shortUrl) {
        return response()->json(['error' => 'Short URL not found']);
    }
    
    // Simulate the controller logic
    $analytics = [
        'total_clicks' => $shortUrl->clicks ?? 0,
        'unique_countries' => $shortUrl->clickLogs()->distinct('country')->count('country') ?? 0,
        'top_countries' => $shortUrl->clickLogs()
            ->selectRaw('country, COUNT(*) as count')
            ->where('country', '!=', '')
            ->whereNotNull('country')
            ->where('country', '!=', 'Unknown')
            ->groupBy('country')
            ->orderBy('count', 'desc')
            ->limit(5)
            ->get(),
        'top_browsers' => $shortUrl->clickLogs()
            ->selectRaw('browser, COUNT(*) as count')
            ->where('browser', '!=', '')
            ->whereNotNull('browser')
            ->where('browser', '!=', 'Unknown')
            ->groupBy('browser')
            ->orderBy('count', 'desc')
            ->limit(5)
            ->get(),
    ];
    
    $clickLogs = $shortUrl->clickLogs()
        ->orderBy('created_at', 'desc')
        ->limit(10)
        ->get();

    return response()->json([
        'short_url' => [
            'id' => $shortUrl->id,
            'short_code' => $shortUrl->short_code,
            'original_url' => $shortUrl->original_url,
            'clicks' => $shortUrl->clicks,
        ],
        'analytics' => $analytics,
        'click_logs_sample' => $clickLogs->map(function($log) {
            return [
                'created_at' => $log->created_at->format('Y-m-d H:i:s'),
                'ip_address' => $log->ip_address,
                'country' => $log->country,
                'browser' => $log->browser,
                'platform' => $log->platform,
            ];
        }),
        'status' => 'Analytics data loaded successfully'
    ]);
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // URL Shortener routes
    Route::resource('urls', ShortUrlController::class)->except(['edit', 'update', 'destroy']);
    
    // Custom delete route for short URLs
    Route::delete('/short-urls/{shortUrl}', [ShortUrlController::class, 'destroy'])->name('short-urls.destroy');
    
    // Dedicated analytics route using short_code
    Route::get('/analytics/{code}', [ShortUrlController::class, 'showAnalytics'])->name('analytics.show');
    
    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // User Management
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::patch('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::post('/users/{user}/promote', [UserController::class, 'promote'])->name('users.promote');
    Route::post('/users/{user}/demote', [UserController::class, 'demote'])->name('users.demote');
    Route::post('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    Route::get('/users/export', [UserController::class, 'export'])->name('users.export');
    
    // Invite Code Management
    Route::get('/invite-codes', [InviteCodeController::class, 'index'])->name('invite-codes.index');
    Route::get('/invite-codes/create', [InviteCodeController::class, 'create'])->name('invite-codes.create');
    Route::post('/invite-codes', [InviteCodeController::class, 'store'])->name('invite-codes.store');
    Route::get('/invite-codes/{inviteCode}/edit', [InviteCodeController::class, 'edit'])->name('invite-codes.edit');
    Route::patch('/invite-codes/{inviteCode}', [InviteCodeController::class, 'update'])->name('invite-codes.update');
    Route::post('/invite-codes/{inviteCode}/toggle-status', [InviteCodeController::class, 'toggleStatus'])->name('invite-codes.toggle-status');
    Route::delete('/invite-codes/{inviteCode}', [InviteCodeController::class, 'destroy'])->name('invite-codes.destroy');
    Route::post('/invite-codes/generate-bulk', [InviteCodeController::class, 'generateBulk'])->name('invite-codes.generate-bulk');
    Route::get('/invite-codes/export', [InviteCodeController::class, 'export'])->name('invite-codes.export');
});

require __DIR__.'/auth.php';

// URL Shortener redirect route (public access) - MUST BE LAST
Route::get('/{code}', [RedirectController::class, 'handle'])->where('code', '[a-zA-Z0-9_-]+');
