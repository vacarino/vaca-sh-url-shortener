<?php

namespace App\Http\Controllers;

use App\Models\ShortUrl;
use App\Models\ClickLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class ShortUrlController extends Controller
{
    /**
     * Display the dashboard with user's URLs with enhanced filtering and search.
     */
    public function index(Request $request)
    {
        try {
            $user = Auth::user();
            
            // Start with base query including user relationship
            $query = ShortUrl::with('user');
            
            // Authorization: Non-admins see only their URLs
            if (!$user->isAdmin()) {
                $query->where('user_id', $user->id);
            } else {
                // Admin user filter
                if ($request->filled('user_id')) {
                    $query->where('user_id', $request->user_id);
                }
            }

            // Search functionality
            if ($request->filled('search')) {
                $searchTerm = $request->search;
                $query->where(function($q) use ($searchTerm) {
                    $q->where('original_url', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('short_code', 'LIKE', "%{$searchTerm}%");
                });
            }

            // Get statistics before pagination (clone the query for stats)
            $statsQuery = clone $query;
            $totalUrls = $statsQuery->count();
            $totalClicks = $statsQuery->sum('clicks') ?? 0;
            $activeUrls = $statsQuery->where('clicks', '>', 0)->count();
            $thisMonthUrls = $statsQuery->where('created_at', '>=', now()->startOfMonth())->count();

            // Handle sorting
            $sort = $request->get('sort', 'created_at');
            $direction = $request->get('direction', 'desc');
            
            // Validate sort parameters
            $allowedSorts = ['created_at', 'clicks', 'short_code', 'original_url'];
            $allowedDirections = ['asc', 'desc'];
            
            if (!in_array($sort, $allowedSorts)) {
                $sort = 'created_at';
            }
            
            if (!in_array($direction, $allowedDirections)) {
                $direction = 'desc';
            }

            // Apply sorting
            $query->orderBy($sort, $direction);
            
            // If sorting by created_at, add secondary sort for consistency
            if ($sort !== 'id') {
                $query->orderBy('id', 'desc');
            }

            // Paginate results
            $shortUrls = $query->paginate(10);

            // Add stats to the paginated collection for easy access in the view
            $shortUrls->totalUrls = $totalUrls;
            $shortUrls->totalClicks = $totalClicks;
            $shortUrls->activeUrls = $activeUrls;
            $shortUrls->thisMonthUrls = $thisMonthUrls;

            // Get all users for admin filter dropdown
            $users = $user->isAdmin() ? User::orderBy('name')->get() : collect();

            return view('urls.index', compact('shortUrls', 'sort', 'direction', 'users'));
            
        } catch (\Exception $e) {
            Log::error('Error in ShortUrlController@index: ' . $e->getMessage() . ' - Trace: ' . $e->getTraceAsString());
            
            // Return with error message and empty data
            $shortUrls = ShortUrl::where('id', 0)->paginate(10); // Empty paginator
            $shortUrls->totalUrls = 0;
            $shortUrls->totalClicks = 0;
            $shortUrls->activeUrls = 0;
            $shortUrls->thisMonthUrls = 0;
            $users = collect();
            $sort = 'created_at';
            $direction = 'desc';
            
            return view('urls.index', compact('shortUrls', 'sort', 'direction', 'users'))
                ->with('error', 'An error occurred while loading your URLs. Please try again.');
        }
    }

    /**
     * Show the form for creating a new short URL.
     */
    public function create()
    {
        return view('urls.create');
    }

    /**
     * Store a newly created short URL.
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'original_url' => 'required|url|max:2000',
                'short_code' => 'nullable|string|max:50|regex:/^[a-zA-Z0-9_-]+$/|unique:short_urls,short_code',
            ], [
                'short_code.unique' => 'This short code is already taken. Please choose another one.',
                'short_code.regex' => 'Short code can only contain letters, numbers, underscores, and hyphens.',
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $shortCode = $request->short_code ?: ShortUrl::generateUniqueCode();

            $shortUrl = ShortUrl::create([
                'user_id' => Auth::id(),
                'original_url' => $request->original_url,
                'short_code' => $shortCode,
            ]);

            $fullShortUrl = $shortUrl->getShortUrl();

            return redirect()->route('urls.create')
                ->with('success', 'Short URL created successfully!')
                ->with('short_url', $fullShortUrl)
                ->with('short_code', $shortCode);

        } catch (\Exception $e) {
            Log::error('Error creating short URL: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'original_url' => $request->original_url,
                'short_code' => $request->short_code,
            ]);

            return redirect()->back()
                ->with('error', 'An error occurred while creating the short URL. Please try again.')
                ->withInput();
        }
    }

    /**
     * Display analytics for a specific short URL.
     */
    public function show(ShortUrl $shortUrl)
    {
        $user = Auth::user();

        // Load the user relationship
        $shortUrl->load('user');

        // Check if user can access this URL
        if (!$user->isAdmin() && $shortUrl->user_id !== $user->id) {
            abort(403, 'Unauthorized access to this short URL.');
        }

        // Debug logging
        Log::info('ShortUrl Show - ID: ' . $shortUrl->id . ', Code: ' . $shortUrl->short_code . ', Clicks: ' . $shortUrl->clicks);

        // Force refresh the model to get latest data
        $shortUrl->refresh();
        
        // Load click logs relationship with fresh data
        $shortUrl->load('clickLogs');

        Log::info('After refresh - Clicks: ' . $shortUrl->clicks . ', ClickLogs Count: ' . $shortUrl->clickLogs->count());

        // Get analytics data with better error handling
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
            'daily_clicks' => $shortUrl->clickLogs()
                ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->where('created_at', '>=', now()->subDays(30))
                ->groupBy('date')
                ->orderBy('date')
                ->get(),
        ];

        Log::info('Analytics Data: ' . json_encode([
            'total_clicks' => $analytics['total_clicks'],
            'unique_countries' => $analytics['unique_countries'],
            'top_countries_count' => $analytics['top_countries']->count(),
            'top_browsers_count' => $analytics['top_browsers']->count(),
            'daily_clicks_count' => $analytics['daily_clicks']->count(),
        ]));

        // Get paginated click logs with proper ordering
        $clickLogs = $shortUrl->clickLogs()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        Log::info('ClickLogs pagination - Total: ' . $clickLogs->total() . ', Per page: ' . $clickLogs->count());

        return view('urls.show', compact('shortUrl', 'clickLogs', 'analytics'));
    }

    /**
     * Display analytics for a specific short URL using short_code.
     */
    public function showAnalytics($code)
    {
        $user = Auth::user();

        // Find the short URL by short_code
        $shortUrl = ShortUrl::where('short_code', $code)->first();

        if (!$shortUrl) {
            abort(404, 'Short URL not found.');
        }

        // Load the user relationship
        $shortUrl->load('user');

        // Check if user can access this URL
        if (!$user->isAdmin() && $shortUrl->user_id !== $user->id) {
            abort(403, 'Unauthorized access to this short URL.');
        }

        // Debug logging
        Log::info('Analytics for short_code: ' . $code . ' - ID: ' . $shortUrl->id . ', Clicks: ' . $shortUrl->clicks);

        // Force refresh the model to get latest data
        $shortUrl->refresh();
        
        // Load click logs relationship with fresh data
        $shortUrl->load('clickLogs');

        Log::info('After refresh - Clicks: ' . $shortUrl->clicks . ', ClickLogs Count: ' . $shortUrl->clickLogs->count());

        // Get analytics data with better error handling
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
            'daily_clicks' => $shortUrl->clickLogs()
                ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->where('created_at', '>=', now()->subDays(30))
                ->groupBy('date')
                ->orderBy('date')
                ->get(),
        ];

        Log::info('Analytics Data: ' . json_encode([
            'total_clicks' => $analytics['total_clicks'],
            'unique_countries' => $analytics['unique_countries'],
            'top_countries_count' => $analytics['top_countries']->count(),
            'top_browsers_count' => $analytics['top_browsers']->count(),
            'daily_clicks_count' => $analytics['daily_clicks']->count(),
        ]));

        // Get paginated click logs with proper ordering
        $clickLogs = $shortUrl->clickLogs()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        Log::info('ClickLogs pagination - Total: ' . $clickLogs->total() . ', Per page: ' . $clickLogs->count());

        return view('urls.analytics', compact('shortUrl', 'clickLogs', 'analytics'));
    }

    /**
     * Remove the specified short URL.
     */
    public function destroy(ShortUrl $shortUrl)
    {
        $user = Auth::user();

        // Check if user can delete this URL
        if (!$user->isAdmin() && $shortUrl->user_id !== $user->id) {
            abort(403, 'Unauthorized to delete this short URL.');
        }

        $shortUrl->delete();

        return redirect()->route('urls.index')
            ->with('success', 'Short URL deleted successfully.');
    }
} 