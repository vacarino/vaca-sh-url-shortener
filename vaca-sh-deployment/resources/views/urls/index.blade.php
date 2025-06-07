<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
            <div>
                <h2 class="font-bold text-2xl text-gray-900 dark:text-white">
                    Dashboard
                    @if(auth()->user()->isAdmin())
                        <span class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 ml-2">
                            Admin View
                        </span>
                    @endif
                </h2>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Manage your short URLs and track performance</p>
            </div>
            <a href="{{ route('urls.create') }}" class="inline-flex items-center bg-gradient-to-r from-blue-600 to-purple-600 text-white px-6 py-3 rounded-xl font-semibold hover:from-blue-700 hover:to-purple-700 transform hover:scale-105 transition-all duration-200 shadow-lg">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Create Short URL
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            <!-- Success/Error Messages -->
            @if(session('error'))
                <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4" x-data="{ show: true }" x-show="show" x-transition>
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-3 flex-1">
                            <p class="text-sm text-red-700 dark:text-red-300">{{ session('error') }}</p>
                        </div>
                        <div class="ml-auto pl-3">
                            <button @click="show = false" class="text-red-400 hover:text-red-600">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            @endif

            @if(session('success'))
                <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl p-4" x-data="{ show: true }" x-show="show" x-transition>
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <div class="ml-3 flex-1">
                            <p class="text-sm text-green-700 dark:text-green-300">{{ session('success') }}</p>
                        </div>
                        <div class="ml-auto pl-3">
                            <button @click="show = false" class="text-green-400 hover:text-green-600">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Overview Stats -->
            @if($shortUrls->total() > 0)
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl flex items-center justify-center">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total URLs</p>
                                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($shortUrls->totalUrls ?? $shortUrls->total()) }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-blue-600 rounded-xl flex items-center justify-center">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Clicks</p>
                                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($shortUrls->totalClicks ?? 0) }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-pink-600 rounded-xl flex items-center justify-center">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Active URLs</p>
                                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($shortUrls->activeUrls ?? 0) }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-gradient-to-br from-yellow-500 to-orange-600 rounded-xl flex items-center justify-center">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">This Month</p>
                                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($shortUrls->thisMonthUrls ?? 0) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Filters and Search -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center mb-6">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-blue-600 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Search & Filter</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Find and organize your short URLs</p>
                    </div>
                </div>

                <form method="GET" action="{{ route('urls.index') }}" class="space-y-6">
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">
                        <!-- Search -->
                        <div class="lg:col-span-4">
                            <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Search URLs
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                                <input type="text" 
                                       id="search" 
                                       name="search" 
                                       value="{{ request('search') }}"
                                       placeholder="Search by URL or short code..."
                                       class="block w-full pl-10 pr-3 py-3 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                            </div>
                        </div>

                        @if(auth()->user()->isAdmin())
                            <!-- User Filter (Admin Only) -->
                            <div class="lg:col-span-3">
                                <label for="user_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Filter by User
                                </label>
                                <select id="user_id" 
                                        name="user_id" 
                                        class="block w-full py-3 px-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 rounded-xl shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-900 dark:text-white transition-all duration-200">
                                    <option value="">All Users</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <!-- Sort By -->
                        <div class="lg:col-span-2">
                            <label for="sort" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Sort By
                            </label>
                            <select id="sort" 
                                    name="sort" 
                                    class="block w-full py-3 px-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 rounded-xl shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-900 dark:text-white transition-all duration-200">
                                <option value="created_at" {{ request('sort') == 'created_at' ? 'selected' : '' }}>Date Created</option>
                                <option value="clicks" {{ request('sort') == 'clicks' ? 'selected' : '' }}>Click Count</option>
                                <option value="short_code" {{ request('sort') == 'short_code' ? 'selected' : '' }}>Short Code</option>
                                <option value="original_url" {{ request('sort') == 'original_url' ? 'selected' : '' }}>Original URL</option>
                            </select>
                        </div>

                        <!-- Sort Direction -->
                        <div class="lg:col-span-2">
                            <label for="direction" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Order
                            </label>
                            <select id="direction" 
                                    name="direction" 
                                    class="block w-full py-3 px-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 rounded-xl shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-900 dark:text-white transition-all duration-200">
                                <option value="desc" {{ request('direction') == 'desc' ? 'selected' : '' }}>Descending</option>
                                <option value="asc" {{ request('direction') == 'asc' ? 'selected' : '' }}>Ascending</option>
                            </select>
                        </div>

                        <!-- Actions -->
                        <div class="lg:col-span-1">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">&nbsp;</label>
                            <div class="flex space-x-2">
                                <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-xl font-semibold hover:from-blue-700 hover:to-purple-700 transition-all duration-200">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                                    </svg>
                                    Filter
                                </button>
                            </div>
                        </div>
                    </div>

                    @if(request()->hasAny(['search', 'user_id', 'sort', 'direction']))
                        <div class="flex items-center justify-between pt-4 border-t border-gray-200 dark:border-gray-700">
                            <div class="text-sm text-gray-600 dark:text-gray-400">
                                Active filters applied
                            </div>
                            <a href="{{ route('urls.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-all duration-200">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Clear All
                            </a>
                        </div>
                    @endif
                </form>
            </div>

            <!-- Results Summary -->
            @if($shortUrls->total() > 0)
                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-4">
                    <div class="flex items-center">
                        <svg class="h-5 w-5 text-blue-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="text-sm text-blue-800 dark:text-blue-200">
                            Showing {{ $shortUrls->firstItem() }} to {{ $shortUrls->lastItem() }} of {{ number_format($shortUrls->total()) }} URLs
                            @if(request()->filled('search'))
                                matching <strong>"{{ request('search') }}"</strong>
                            @endif
                            @if(auth()->user()->isAdmin() && request()->filled('user_id'))
                                for <strong>{{ $users->firstWhere('id', request('user_id'))->name ?? 'Unknown User' }}</strong>
                            @endif
                        </span>
                    </div>
                </div>
            @endif

            <!-- URLs Grid/List -->
            @if($shortUrls->count() > 0)
                <div class="space-y-4">
                    @foreach($shortUrls as $shortUrl)
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-all duration-300">
                            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0">
                                <!-- URL Info -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start space-x-4">
                                        <!-- URL Icon -->
                                        <div class="flex-shrink-0">
                                            <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl flex items-center justify-center">
                                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                                                </svg>
                                            </div>
                                        </div>

                                        <div class="flex-1 min-w-0">
                                            <!-- Short URL -->
                                            <div class="flex items-center space-x-3 mb-2">
                                                <code class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-mono bg-gray-100 dark:bg-gray-700 text-blue-600 dark:text-blue-400 font-semibold">
                                                    {{ $shortUrl->short_code }}
                                                </code>
                                                <button 
                                                    onclick="copyToClipboard('{{ $shortUrl->getShortUrl() }}')"
                                                    class="p-2 text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-all duration-200"
                                                    title="Copy short URL"
                                                >
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                                    </svg>
                                                </button>
                                                
                                                <!-- Status Badge -->
                                                @if($shortUrl->clicks > 0)
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">
                                                        <div class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1.5"></div>
                                                        Active
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400">
                                                        <div class="w-1.5 h-1.5 bg-gray-400 rounded-full mr-1.5"></div>
                                                        Pending
                                                    </span>
                                                @endif
                                            </div>

                                            <!-- Original URL -->
                                            <div class="mb-3">
                                                <a href="{{ $shortUrl->original_url }}" target="_blank" class="text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-200 break-all">
                                                    {{ strlen($shortUrl->original_url) > 80 ? substr($shortUrl->original_url, 0, 80) . '...' : $shortUrl->original_url }}
                                                </a>
                                            </div>

                                            <!-- Meta Info -->
                                            <div class="flex flex-wrap items-center gap-4 text-sm text-gray-500 dark:text-gray-400">
                                                <div class="flex items-center">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                    </svg>
                                                    <strong class="text-gray-900 dark:text-white">{{ number_format($shortUrl->clicks) }}</strong> clicks
                                                </div>
                                                
                                                <div class="flex items-center">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    {{ $shortUrl->created_at ? $shortUrl->created_at->format('M j, Y') : 'Unknown' }}
                                                </div>

                                                @if(auth()->user()->isAdmin())
                                                    <div class="flex items-center">
                                                        <div class="w-5 h-5 bg-gradient-to-br from-gray-500 to-gray-600 rounded-full flex items-center justify-center mr-2">
                                                            <span class="text-xs font-medium text-white">
                                                                {{ substr($shortUrl->user->name ?? 'U', 0, 1) }}
                                                            </span>
                                                        </div>
                                                        {{ $shortUrl->user->name ?? 'Unknown User' }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="flex items-center space-x-3 lg:ml-6">
                                    <!-- Quick Stats -->
                                    @if($shortUrl->clicks > 0)
                                        <div class="hidden lg:block text-right">
                                            <div class="text-lg font-bold text-gray-900 dark:text-white">{{ number_format($shortUrl->clicks) }}</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">Total Clicks</div>
                                        </div>
                                    @endif

                                    <!-- Action Buttons -->
                                    <div class="flex items-center space-x-2">
                                        <a href="{{ route('analytics.show', $shortUrl->short_code) }}" 
                                           class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all duration-200 font-medium"
                                           title="View Analytics">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                            </svg>
                                            <span class="hidden sm:inline">Analytics</span>
                                        </a>
                                        
                                        <form method="POST" action="{{ route('short-urls.destroy', $shortUrl) }}" class="inline" onsubmit="return confirmDelete('{{ $shortUrl->short_code }}', '{{ $shortUrl->original_url }}')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-all duration-200 font-medium"
                                                    title="Delete URL">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                                <span class="hidden sm:inline">Delete</span>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                @if($shortUrls->hasPages())
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 px-6 py-4">
                        {{ $shortUrls->appends(request()->query())->links() }}
                    </div>
                @endif
            @else
                <!-- Empty State -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-12 text-center">
                    @if(request()->hasAny(['search', 'user_id']))
                        <!-- No Results Found -->
                        <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-2xl flex items-center justify-center mx-auto mb-6">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">No URLs found</h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-6">
                            No short URLs match your current filters. Try adjusting your search criteria.
                        </p>
                        <a href="{{ route('urls.index') }}" class="inline-flex items-center bg-gradient-to-r from-blue-600 to-purple-600 text-white px-6 py-3 rounded-xl font-semibold hover:from-blue-700 hover:to-purple-700 transition-all duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Clear All Filters
                        </a>
                    @else
                        <!-- No URLs Created Yet -->
                        <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-purple-600 rounded-2xl flex items-center justify-center mx-auto mb-6">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">No short URLs yet</h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-6">
                            Get started by creating your first short URL and start tracking your links!
                        </p>
                        <a href="{{ route('urls.create') }}" class="inline-flex items-center bg-gradient-to-r from-blue-600 to-purple-600 text-white px-6 py-3 rounded-xl font-semibold hover:from-blue-700 hover:to-purple-700 transform hover:scale-105 transition-all duration-200 shadow-lg">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Create Your First Short URL
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>

    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function() {
                window.dispatchEvent(new CustomEvent('toast', {
                    detail: { message: 'Short URL copied to clipboard!', type: 'success' }
                }));
            }).catch(function(err) {
                console.error('Could not copy text: ', err);
                window.dispatchEvent(new CustomEvent('toast', {
                    detail: { message: 'Failed to copy URL to clipboard', type: 'error' }
                }));
            });
        }

        function confirmDelete(shortCode, originalUrl) {
            return confirm(`Are you sure you want to delete the short URL "${shortCode}"?\n\nOriginal URL: ${originalUrl.substring(0, 100)}${originalUrl.length > 100 ? '...' : ''}\n\nThis action cannot be undone and all analytics data will be lost.`);
        }

        // Auto-submit form when filters change
        document.addEventListener('DOMContentLoaded', function() {
            const autoSubmitElements = ['#user_id', '#sort', '#direction'];
            autoSubmitElements.forEach(selector => {
                const element = document.querySelector(selector);
                if (element) {
                    element.addEventListener('change', function() {
                        this.closest('form').submit();
                    });
                }
            });
        });
    </script>
</x-app-layout> 