<?php

namespace App\Http\Controllers;

use App\Models\ShortUrl;
use App\Models\ClickLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RedirectController extends Controller
{
    /**
     * Handle the redirect for short URLs at root path.
     */
    public function handle(Request $request, $code)
    {
        // Log the incoming request
        Log::info("Redirect attempt for code: {$code}");

        // Find the short URL
        $shortUrl = ShortUrl::where('short_code', $code)->first();

        if (!$shortUrl) {
            Log::warning("Short URL not found for code: {$code}");
            abort(404, 'Short URL not found.');
        }

        Log::info("Found Short URL - ID: {$shortUrl->id}, Original: {$shortUrl->original_url}");

        // Check if URL has expired
        if ($shortUrl->isExpired()) {
            Log::warning("Short URL {$code} has expired");
            abort(410, 'This short URL has expired.');
        }

        // Get visitor information
        $ipAddress = $this->getClientIpAddress($request);
        $userAgent = $request->header('User-Agent', '');
        $country = $this->getCountryFromIp($ipAddress);
        $userAgentInfo = ClickLog::parseUserAgent($userAgent);

        Log::info("Visitor Info - IP: {$ipAddress}, Country: {$country}, Browser: {$userAgentInfo['browser']}");

        try {
            // Log the click
            $clickLog = ClickLog::create([
                'short_url_id' => $shortUrl->id,
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent,
                'country' => $country,
                'browser' => $userAgentInfo['browser'],
                'platform' => $userAgentInfo['platform'],
            ]);

            Log::info("Click logged successfully - ID: {$clickLog->id}");

            // Increment click count
            $shortUrl->incrementClicks();
            $shortUrl->refresh();

            Log::info("Click count incremented - New count: {$shortUrl->clicks}");

        } catch (\Exception $e) {
            Log::error('Error logging click: ' . $e->getMessage() . ' - Trace: ' . $e->getTraceAsString());
            // Continue with redirect even if logging fails
        }

        // Log the redirect
        Log::info("Redirecting to: {$shortUrl->original_url}");

        // Redirect to original URL
        return redirect($shortUrl->original_url);
    }

    /**
     * Get the real client IP address.
     */
    private function getClientIpAddress(Request $request): string
    {
        $ipKeys = [
            'HTTP_CF_CONNECTING_IP',     // Cloudflare
            'HTTP_CLIENT_IP',            // Proxy
            'HTTP_X_FORWARDED_FOR',      // Load balancer/proxy
            'HTTP_X_FORWARDED',          // Proxy
            'HTTP_X_CLUSTER_CLIENT_IP',  // Cluster
            'HTTP_FORWARDED_FOR',        // Proxy
            'HTTP_FORWARDED',            // Proxy
            'REMOTE_ADDR'                // Standard
        ];

        foreach ($ipKeys as $key) {
            $ip = $request->server($key);
            if (!empty($ip)) {
                // Handle comma-separated IPs (X-Forwarded-For can contain multiple IPs)
                $ips = explode(',', $ip);
                $ip = trim($ips[0]);
                
                // Validate IP address
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return $ip;
                }
            }
        }

        return $request->ip() ?? 'unknown';
    }

    /**
     * Get country from IP address using a simple GeoIP service.
     * In production, you would use a proper GeoIP database like MaxMind.
     */
    private function getCountryFromIp(string $ip): string
    {
        // Skip private/local IPs
        if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
            return 'Unknown';
        }

        try {
            // Using a free GeoIP API (in production, use MaxMind or similar)
            $response = @file_get_contents("http://ip-api.com/json/{$ip}?fields=country");
            
            if ($response) {
                $data = json_decode($response, true);
                return $data['country'] ?? 'Unknown';
            }
        } catch (\Exception $e) {
            Log::warning('GeoIP lookup failed: ' . $e->getMessage());
        }

        return 'Unknown';
    }
} 