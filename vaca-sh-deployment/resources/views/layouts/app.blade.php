<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }" x-bind:class="{ 'dark': darkMode }">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Vaca.Sh') }} - Professional URL Shortener</title>
        <meta name="description" content="Vaca.Sh - Create short, trackable URLs with detailed analytics. Professional URL shortening service with click tracking and insights.">
        
        <!-- Open Graph Meta Tags -->
        <meta property="og:title" content="Vaca.Sh - Professional URL Shortener">
        <meta property="og:description" content="Create short, trackable URLs with detailed analytics. Professional URL shortening service.">
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ request()->url() }}">
        <meta property="og:image" content="{{ asset('images/vaca-sh-og.png') }}">
        
        <!-- Twitter Card Meta Tags -->
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="Vaca.Sh - Professional URL Shortener">
        <meta name="twitter:description" content="Create short, trackable URLs with detailed analytics.">
        <meta name="twitter:image" content="{{ asset('images/vaca-sh-og.png') }}">

        <!-- Favicon -->
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
        <link rel="apple-touch-icon" href="{{ asset('images/apple-touch-icon.png') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <!-- Alpine.js -->
        <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
        
        <!-- Chart.js -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns/dist/chartjs-adapter-date-fns.bundle.min.js"></script>

        <style>
            [x-cloak] { display: none !important; }
            .font-inter { font-family: 'Inter', sans-serif; }
        </style>
    </head>
    <body class="font-inter antialiased bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 transition-colors duration-300" x-cloak>
        <div class="min-h-screen">
            <!-- Navigation -->
            <nav class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 shadow-sm sticky top-0 z-50">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <!-- Logo and Navigation -->
                        <div class="flex items-center">
                            <!-- Logo -->
                            <div class="flex-shrink-0 flex items-center">
                                <a href="{{ route('home') }}" class="flex items-center space-x-2">
                                    <!-- Vaca.Sh Logo -->
                                    <div class="flex items-center justify-center w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="text-xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                                            Vaca.Sh
                                        </div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400 -mt-1">
                                            v2.0
                                        </div>
                                    </div>
                                </a>
                            </div>

                            <!-- Navigation Links -->
                            @auth
                                <div class="hidden md:ml-8 md:flex md:space-x-8">
                                    <a href="{{ route('urls.index') }}" class="inline-flex items-center px-1 pt-1 text-sm font-medium {{ request()->routeIs('urls.*') ? 'text-blue-600 dark:text-blue-400 border-b-2 border-blue-600 dark:border-blue-400' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300' }} transition-colors duration-200">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                        </svg>
                                        Dashboard
                                    </a>
                                    <a href="{{ route('urls.create') }}" class="inline-flex items-center px-1 pt-1 text-sm font-medium {{ request()->routeIs('urls.create') ? 'text-blue-600 dark:text-blue-400 border-b-2 border-blue-600 dark:border-blue-400' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300' }} transition-colors duration-200">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                        </svg>
                                        Create
                                    </a>
                                    
                                    @if(Auth::user()->isAdmin())
                                        <a href="{{ route('admin.users.index') }}" class="inline-flex items-center px-1 pt-1 text-sm font-medium {{ request()->routeIs('admin.users.*') ? 'text-purple-600 dark:text-purple-400 border-b-2 border-purple-600 dark:border-purple-400' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300' }} transition-colors duration-200">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"></path>
                                            </svg>
                                            Admin Users
                                        </a>
                                        <a href="{{ route('admin.invite-codes.index') }}" class="inline-flex items-center px-1 pt-1 text-sm font-medium {{ request()->routeIs('admin.invite-codes.*') ? 'text-purple-600 dark:text-purple-400 border-b-2 border-purple-600 dark:border-purple-400' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300' }} transition-colors duration-200">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1721 9z"></path>
                                            </svg>
                                            Invite Codes
                                        </a>
                                    @endif
                                </div>
                            @endauth
                        </div>

                        <!-- Right side -->
                        <div class="flex items-center space-x-4">
                            <!-- Dark Mode Toggle -->
                            <button @click="darkMode = !darkMode; localStorage.setItem('darkMode', darkMode)" 
                                    class="p-2 rounded-lg text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200">
                                <svg x-show="!darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                                </svg>
                                <svg x-show="darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                </svg>
                            </button>

                            @auth
                                <!-- User Menu -->
                                <div class="relative" x-data="{ open: false }">
                                    <button @click="open = !open" class="flex items-center space-x-2 text-sm rounded-lg p-2 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200">
                                        <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                                            <span class="text-white text-sm font-medium">{{ substr(Auth::user()->name, 0, 1) }}</span>
                                        </div>
                                        <span class="text-gray-700 dark:text-gray-300 font-medium">{{ Auth::user()->name }}</span>
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </button>

                                    <!-- Dropdown -->
                                    <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 py-1 z-50">
                                        <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                                            <p class="text-sm text-gray-900 dark:text-gray-100 font-medium">{{ Auth::user()->name }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ Auth::user()->email }}</p>
                                            @if(Auth::user()->isAdmin())
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 mt-1">
                                                    Admin
                                                </span>
                                            @endif
                                        </div>
                                        <a href="{{ route('profile.edit') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                            </svg>
                                            Profile Settings
                                        </a>
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="flex items-center w-full px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                                <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                                </svg>
                                                Sign Out
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @else
                                <!-- Guest Navigation -->
                                <div class="flex items-center space-x-4">
                                    <a href="{{ route('login') }}" class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 text-sm font-medium transition-colors duration-200">
                                        Sign In
                                    </a>
                                    <a href="{{ route('register') }}" class="bg-gradient-to-r from-blue-600 to-purple-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:from-blue-700 hover:to-purple-700 transition-all duration-200 shadow-sm">
                                        Get Started
                                    </a>
                                </div>
                            @endauth
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main class="flex-1">
                {{ $slot }}
            </main>

            <!-- Footer -->
            <footer class="bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 mt-auto">
                <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                        <!-- Brand -->
                        <div class="col-span-1 md:col-span-2">
                            <div class="flex items-center space-x-2 mb-4">
                                <div class="flex items-center justify-center w-8 h-8 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                                    </svg>
                                </div>
                                <span class="text-lg font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                                    Vaca.Sh
                                </span>
                            </div>
                            <p class="text-gray-600 dark:text-gray-400 text-sm max-w-md">
                                Professional URL shortening service with detailed analytics, custom links, and advanced tracking capabilities.
                            </p>
                        </div>

                        <!-- Links -->
                        <div>
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100 uppercase tracking-wider mb-4">Product</h3>
                            <ul class="space-y-2">
                                <li><a href="{{ route('home') }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 text-sm transition-colors duration-200">Features</a></li>
                                <li><a href="{{ route('urls.create') }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 text-sm transition-colors duration-200">Create Link</a></li>
                                @auth
                                    <li><a href="{{ route('urls.index') }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 text-sm transition-colors duration-200">Dashboard</a></li>
                                @endauth
                            </ul>
                        </div>

                        <!-- Support -->
                        <div>
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100 uppercase tracking-wider mb-4">Support</h3>
                            <ul class="space-y-2">
                                <li><span class="text-gray-600 dark:text-gray-400 text-sm">Help Center</span></li>
                                <li><span class="text-gray-600 dark:text-gray-400 text-sm">API Docs</span></li>
                                <li><span class="text-gray-600 dark:text-gray-400 text-sm">Contact</span></li>
                            </ul>
                        </div>
                    </div>

                    <!-- Bottom -->
                    <div class="mt-8 pt-8 border-t border-gray-200 dark:border-gray-700 flex flex-col md:flex-row justify-between items-center">
                        <p class="text-gray-500 dark:text-gray-400 text-sm">
                            Vaca.Sh — v2.0 © {{ date('Y') }}. All rights reserved.
                        </p>
                        <div class="flex items-center space-x-4 mt-4 md:mt-0">
                            <span class="text-gray-500 dark:text-gray-400 text-xs">
                                Built with Laravel & Tailwind CSS
                            </span>
                        </div>
                    </div>
                </div>
            </footer>
        </div>

        <!-- Toast Notifications -->
        <div x-data="{ show: false, message: '', type: 'success' }" 
             x-show="show" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform translate-y-2"
             x-transition:enter-end="opacity-100 transform translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform translate-y-0"
             x-transition:leave-end="opacity-0 transform translate-y-2"
             @toast.window="show = true; message = $event.detail.message; type = $event.detail.type || 'success'; setTimeout(() => show = false, 3000)"
             class="fixed bottom-4 right-4 z-50">
            <div class="max-w-sm w-full bg-white dark:bg-gray-800 shadow-lg rounded-lg pointer-events-auto border border-gray-200 dark:border-gray-700">
                <div class="p-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg x-show="type === 'success'" class="h-6 w-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <svg x-show="type === 'error'" class="h-6 w-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </div>
                        <div class="ml-3 w-0 flex-1 pt-0.5">
                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100" x-text="message"></p>
                        </div>
                        <div class="ml-4 flex-shrink-0 flex">
                            <button @click="show = false" class="rounded-md text-gray-400 hover:text-gray-500 focus:outline-none">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
