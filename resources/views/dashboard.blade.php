<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Dashboard - URL Shortener</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-gray-100">
        <div class="min-h-screen">
            <nav class="bg-white shadow">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex items-center">
                            <h1 class="text-xl font-semibold">URL Shortener</h1>
                        </div>
                        <div class="flex items-center space-x-4">
                            <span class="text-gray-700">{{ Auth::user()->name }}</span>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700">
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </nav>

            <div class="py-12">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900">
                            <h2 class="text-2xl font-bold mb-4">Welcome to your Dashboard!</h2>
                            <p class="text-gray-600">You're successfully logged in to the URL Shortener application.</p>
                            
                            <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div class="bg-blue-100 p-4 rounded-lg">
                                    <h3 class="font-semibold text-blue-800">Total URLs</h3>
                                    <p class="text-2xl font-bold text-blue-600">{{ Auth::user()->shortUrls()->count() }}</p>
                                </div>
                                <div class="bg-green-100 p-4 rounded-lg">
                                    <h3 class="font-semibold text-green-800">Total Clicks</h3>
                                    <p class="text-2xl font-bold text-green-600">{{ Auth::user()->shortUrls()->sum('clicks') }}</p>
                                </div>
                                <div class="bg-purple-100 p-4 rounded-lg">
                                    <h3 class="font-semibold text-purple-800">Active URLs</h3>
                                    <p class="text-2xl font-bold text-purple-600">{{ Auth::user()->shortUrls()->active()->count() }}</p>
                                </div>
                            </div>

                            <div class="mt-8 space-x-4">
                                <a href="{{ route('urls.index') }}" class="bg-blue-600 text-white px-6 py-3 rounded-md hover:bg-blue-700 transition duration-200">
                                    View My URLs
                                </a>
                                <a href="{{ route('urls.create') }}" class="bg-green-600 text-white px-6 py-3 rounded-md hover:bg-green-700 transition duration-200">
                                    Create New Short URL
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
