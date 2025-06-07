<x-app-layout>
    <!-- Hero Section -->
    <div class="relative bg-gradient-to-br from-blue-50 via-white to-purple-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 overflow-hidden">
        <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%23e5e7eb" fill-opacity="0.4"%3E%3Cpath d="m36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')] dark:opacity-10"></div>
        
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-20 pb-32">
            <div class="text-center">
                <!-- Main Heading -->
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold text-gray-900 dark:text-white mb-6">
                    <span class="block">Short Links,</span>
                    <span class="block bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                        Big Insights
                    </span>
                </h1>
                
                <!-- Subtitle -->
                <p class="max-w-3xl mx-auto text-xl text-gray-600 dark:text-gray-300 mb-10 leading-relaxed">
                    Create powerful short URLs with detailed analytics, custom links, and professional tracking. 
                    Monitor every click, understand your audience, and grow your reach.
                </p>

                <!-- CTA Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                    @auth
                        <a href="{{ route('urls.create') }}" class="w-full sm:w-auto bg-gradient-to-r from-blue-600 to-purple-600 text-white px-8 py-4 rounded-xl text-lg font-semibold hover:from-blue-700 hover:to-purple-700 transform hover:scale-105 transition-all duration-200 shadow-lg inline-flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                            </svg>
                            Create Short URL
                        </a>
                        <a href="{{ route('urls.index') }}" class="w-full sm:w-auto bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-8 py-4 rounded-xl text-lg font-semibold border-2 border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600 transition-all duration-200 inline-flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            View Dashboard
                        </a>
                    @else
                        <a href="{{ route('register') }}" class="w-full sm:w-auto bg-gradient-to-r from-blue-600 to-purple-600 text-white px-8 py-4 rounded-xl text-lg font-semibold hover:from-blue-700 hover:to-purple-700 transform hover:scale-105 transition-all duration-200 shadow-lg inline-flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                            Get Started Free
                        </a>
                        <a href="{{ route('login') }}" class="w-full sm:w-auto bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-8 py-4 rounded-xl text-lg font-semibold border-2 border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600 transition-all duration-200 inline-flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                            Sign In
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div class="py-24 bg-white dark:bg-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Section Header -->
            <div class="text-center mb-20">
                <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 dark:text-white mb-4">
                    Powerful Features for Modern Marketers
                </h2>
                <p class="max-w-2xl mx-auto text-xl text-gray-600 dark:text-gray-300">
                    Everything you need to create, manage, and track short URLs that drive results.
                </p>
            </div>

            <!-- Features Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Custom Short Codes -->
                <div class="bg-gray-50 dark:bg-gray-700 rounded-2xl p-8 hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">Custom Short Codes</h3>
                    <p class="text-gray-600 dark:text-gray-300">
                        Create branded short URLs with custom codes that reflect your brand and are easy to remember.
                    </p>
                </div>

                <!-- Real-time Analytics -->
                <div class="bg-gray-50 dark:bg-gray-700 rounded-2xl p-8 hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
                    <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-blue-600 rounded-xl flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">Real-time Analytics</h3>
                    <p class="text-gray-600 dark:text-gray-300">
                        Track clicks, countries, browsers, and devices with detailed analytics and beautiful charts.
                    </p>
                </div>

                <!-- Global Tracking -->
                <div class="bg-gray-50 dark:bg-gray-700 rounded-2xl p-8 hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
                    <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-pink-600 rounded-xl flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">Global Tracking</h3>
                    <p class="text-gray-600 dark:text-gray-300">
                        See where your clicks come from with detailed geographic data and visitor insights.
                    </p>
                </div>

                <!-- Fast & Reliable -->
                <div class="bg-gray-50 dark:bg-gray-700 rounded-2xl p-8 hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
                    <div class="w-12 h-12 bg-gradient-to-br from-yellow-500 to-orange-600 rounded-xl flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">Fast & Reliable</h3>
                    <p class="text-gray-600 dark:text-gray-300">
                        Lightning-fast redirects with 99.9% uptime guarantee. Your links work when you need them.
                    </p>
                </div>

                <!-- Team Management -->
                <div class="bg-gray-50 dark:bg-gray-700 rounded-2xl p-8 hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
                    <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">Team Management</h3>
                    <p class="text-gray-600 dark:text-gray-300">
                        Collaborate with team members, manage permissions, and organize your links efficiently.
                    </p>
                </div>

                <!-- API Access -->
                <div class="bg-gray-50 dark:bg-gray-700 rounded-2xl p-8 hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
                    <div class="w-12 h-12 bg-gradient-to-br from-red-500 to-pink-600 rounded-xl flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">Developer API</h3>
                    <p class="text-gray-600 dark:text-gray-300">
                        Integrate Vaca.Sh into your applications with our powerful REST API and webhooks.
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>