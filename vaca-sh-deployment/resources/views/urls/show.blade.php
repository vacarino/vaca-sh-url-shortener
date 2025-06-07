<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Analytics for {{ $shortUrl->short_code }}
                </h2>
                <p class="text-sm text-gray-600 mt-1">
                    {{ $shortUrl->original_url }}
                </p>
            </div>
            <a href="{{ route('urls.index') }}" class="text-sm text-gray-600 hover:text-gray-900">
                ← Back to Dashboard
            </a>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        <!-- URL Info Card -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">URL Details</h3>
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Short URL</dt>
                                <dd class="flex items-center space-x-2">
                                    <code class="px-2 py-1 text-sm font-mono bg-gray-100 rounded">
                                        {{ $shortUrl->short_url }}
                                    </code>
                                    <button 
                                        onclick="copyToClipboard('{{ $shortUrl->short_url }}')"
                                        class="text-blue-600 hover:text-blue-800"
                                        title="Copy short URL"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                        </svg>
                                    </button>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Original URL</dt>
                                <dd class="text-sm text-gray-900 break-all">
                                    <a href="{{ $shortUrl->original_url }}" target="_blank" class="text-blue-600 hover:text-blue-800">
                                        {{ $shortUrl->original_url }}
                                    </a>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Created</dt>
                                <dd class="text-sm text-gray-900">
                                    {{ $shortUrl->created_at ? $shortUrl->created_at->format('M j, Y \a\t g:i A') : 'Unknown' }}
                                </dd>
                            </div>
                            @if($shortUrl->expires_at)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Expires</dt>
                                    <dd class="text-sm text-gray-900">
                                        {{ $shortUrl->expires_at->format('M j, Y \a\t g:i A') }}
                                        @if($shortUrl->isExpired())
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800 ml-2">
                                                Expired
                                            </span>
                                        @endif
                                    </dd>
                                </div>
                            @endif
                        </dl>
                    </div>
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Owner Information</h3>
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Created by</dt>
                                <dd class="text-sm text-gray-900">
                                    {{ $shortUrl->user ? $shortUrl->user->name : 'Unknown User' }}
                                    @if($shortUrl->user && $shortUrl->user->isAdmin())
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800 ml-2">
                                            Admin
                                        </span>
                                    @endif
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Email</dt>
                                <dd class="text-sm text-gray-900">{{ $shortUrl->user ? $shortUrl->user->email : 'Unknown' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">User Role</dt>
                                <dd class="text-sm text-gray-900">
                                    @if($shortUrl->user)
                                        {{ $shortUrl->user->isAdmin() ? 'Administrator' : 'Collaborator' }}
                                    @else
                                        Unknown
                                    @endif
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Analytics Overview -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <!-- Debug Info (temporary) -->
            <div class="bg-gray-50 overflow-hidden shadow-sm rounded-lg md:col-span-4 mb-4 p-4">
                <h4 class="text-sm font-medium text-gray-700 mb-2">Debug Info:</h4>
                <div class="text-xs text-gray-600">
                    <p>Total Clicks: {{ $analytics['total_clicks'] ?? 'undefined' }}</p>
                    <p>Unique Countries: {{ $analytics['unique_countries'] ?? 'undefined' }}</p>
                    <p>Top Countries Count: {{ isset($analytics['top_countries']) ? $analytics['top_countries']->count() : 'undefined' }}</p>
                    <p>Top Browsers Count: {{ isset($analytics['top_browsers']) ? $analytics['top_browsers']->count() : 'undefined' }}</p>
                </div>
            </div>

            <!-- Total Clicks -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-8 w-8 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Clicks</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ number_format($analytics['total_clicks']) }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Unique Countries -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-8 w-8 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Countries</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ number_format($analytics['unique_countries']) }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-8 w-8 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Last 24h</dt>
                                <dd class="text-lg font-medium text-gray-900">
                                    {{ $shortUrl->clickLogs()->where('created_at', '>=', now()->subDay())->count() }}
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Average Daily -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-8 w-8 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Avg/Day</dt>
                                <dd class="text-lg font-medium text-gray-900">
                                    {{ $shortUrl->created_at && $shortUrl->created_at->diffInDays(now()) > 0 ? round($analytics['total_clicks'] / $shortUrl->created_at->diffInDays(now()), 1) : $analytics['total_clicks'] }}
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Countries and Browsers -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Top Countries -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Top Countries</h3>
                </div>
                <div class="p-6">
                    @if($analytics['top_countries']->count() > 0)
                        <div class="space-y-3">
                            @foreach($analytics['top_countries'] as $country)
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $country->country ?: 'Unknown' }}
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <div class="text-sm text-gray-500">{{ number_format($country->count) }}</div>
                                        <div class="w-16 bg-gray-200 rounded-full h-2">
                                            <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $analytics['total_clicks'] > 0 ? ($country->count / $analytics['total_clicks']) * 100 : 0 }}%"></div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-500">No country data available yet.</p>
                    @endif
                </div>
            </div>

            <!-- Top Browsers -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Top Browsers</h3>
                </div>
                <div class="p-6">
                    @if($analytics['top_browsers']->count() > 0)
                        <div class="space-y-3">
                            @foreach($analytics['top_browsers'] as $browser)
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $browser->browser ?: 'Unknown' }}
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <div class="text-sm text-gray-500">{{ number_format($browser->count) }}</div>
                                        <div class="w-16 bg-gray-200 rounded-full h-2">
                                            <div class="bg-green-600 h-2 rounded-full" style="width: {{ $analytics['total_clicks'] > 0 ? ($browser->count / $analytics['total_clicks']) * 100 : 0 }}%"></div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-500">No browser data available yet.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Click Logs -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Recent Clicks</h3>
            </div>
            @if($clickLogs->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Date & Time
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    IP Address
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Country
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Browser
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Platform
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($clickLogs as $log)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <div>{{ $log->created_at ? $log->created_at->format('M j, Y') : 'Unknown' }}</div>
                                        <div class="text-xs text-gray-500">{{ $log->created_at ? $log->created_at->format('g:i A') : '' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $log->ip_address ?: 'Unknown' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $log->country ?: 'Unknown' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $log->browser ?: 'Unknown' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $log->platform ?: 'Unknown' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($clickLogs->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $clickLogs->links() }}
                    </div>
                @endif
            @else
                <div class="p-6 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    <h3 class="mt-4 text-lg font-medium text-gray-900">No clicks yet</h3>
                    <p class="mt-2 text-sm text-gray-500">Share your short URL to start collecting analytics data.</p>
                </div>
            @endif
        </div>
    </div>

    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function() {
                // Show a temporary success message
                const button = event.target.closest('button');
                const originalSVG = button.querySelector('svg').outerHTML;
                button.innerHTML = '<svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>';
                
                setTimeout(() => {
                    button.innerHTML = originalSVG;
                }, 2000);
            }).catch(function(err) {
                console.error('Could not copy text: ', err);
                alert('Failed to copy URL to clipboard');
            });
        }
    </script>
</x-app-layout> 