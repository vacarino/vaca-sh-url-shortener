<x-app-layout>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                            Edit User: {{ $user->name }}
                        </h2>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            Update user details, role, and password
                        </p>
                    </div>
                    <a href="{{ route('admin.users.index') }}" 
                       class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to Users
                    </a>
                </div>
            </div>

            <!-- Edit Form -->
            <div class="bg-white dark:bg-gray-800 shadow-xl rounded-2xl border border-gray-200 dark:border-gray-700">
                <div class="p-8">
                    <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-6">
                        @csrf
                        @method('PATCH')

                        <!-- User Info Section -->
                        <div class="border-b border-gray-200 dark:border-gray-700 pb-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                                User Information
                            </h3>
                            
                            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                                <!-- Name -->
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Full Name <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                            </svg>
                                        </div>
                                        <input id="name" name="name" type="text" autocomplete="name" required 
                                               value="{{ old('name', $user->name) }}"
                                               class="block w-full pl-10 pr-3 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 @error('name') border-red-300 dark:border-red-600 @enderror"
                                               placeholder="John Doe">
                                    </div>
                                    @error('name')
                                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Email -->
                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Email Address <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                                            </svg>
                                        </div>
                                        <input id="email" name="email" type="email" autocomplete="email" required 
                                               value="{{ old('email', preg_replace('/_deactivated_\d+/', '', $user->email)) }}"
                                               class="block w-full pl-10 pr-3 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 @error('email') border-red-300 dark:border-red-600 @enderror"
                                               placeholder="user@example.com">
                                    </div>
                                    @error('email')
                                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Role Section -->
                        <div class="border-b border-gray-200 dark:border-gray-700 pb-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                                User Role
                            </h3>
                            
                            <div>
                                <label for="role" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Role <span class="text-red-500">*</span>
                                </label>
                                <select id="role" name="role" required
                                        class="block w-full px-3 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 @error('role') border-red-300 dark:border-red-600 @enderror"
                                        {{ $user->id === auth()->id() ? 'disabled' : '' }}>
                                    <option value="collaborator" {{ old('role', $user->role) === 'collaborator' ? 'selected' : '' }}>
                                        Collaborator
                                    </option>
                                    <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>
                                        Administrator
                                    </option>
                                </select>
                                @if($user->id === auth()->id())
                                    <input type="hidden" name="role" value="{{ $user->role }}">
                                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                        You cannot change your own role.
                                    </p>
                                @endif
                                @error('role')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                                <div class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                    <div class="space-y-1">
                                        <p><strong>Collaborator:</strong> Can create and manage their own short URLs</p>
                                        <p><strong>Administrator:</strong> Full access to all features including user management</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Password Section -->
                        <div class="pb-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                                Change Password
                            </h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                                Leave password fields empty to keep the current password unchanged.
                            </p>
                            
                            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                                <!-- Password -->
                                <div>
                                    <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        New Password
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                            </svg>
                                        </div>
                                        <input id="password" name="password" type="password" autocomplete="new-password"
                                               class="block w-full pl-10 pr-3 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 @error('password') border-red-300 dark:border-red-600 @enderror"
                                               placeholder="Enter new password">
                                    </div>
                                    @error('password')
                                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Confirm Password -->
                                <div>
                                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Confirm New Password
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </div>
                                        <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password"
                                               class="block w-full pl-10 pr-3 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 @error('password_confirmation') border-red-300 dark:border-red-600 @enderror"
                                               placeholder="Confirm new password">
                                    </div>
                                    @error('password_confirmation')
                                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center justify-between pt-6 border-t border-gray-200 dark:border-gray-700">
                            <div class="flex items-center space-x-4">
                                <button type="submit" 
                                        class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 border border-transparent rounded-lg font-semibold text-sm text-white uppercase tracking-widest hover:from-blue-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 active:from-blue-800 active:to-purple-800 transition-all duration-200 transform hover:scale-105">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Update User
                                </button>

                                <a href="{{ route('admin.users.index') }}" 
                                   class="inline-flex items-center px-6 py-3 bg-gray-300 dark:bg-gray-600 border border-transparent rounded-lg font-semibold text-sm text-gray-700 dark:text-gray-300 uppercase tracking-widest hover:bg-gray-400 dark:hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition-all duration-200">
                                    Cancel
                                </a>
                            </div>

                            <!-- User Stats -->
                            <div class="text-right">
                                <div class="text-sm text-gray-600 dark:text-gray-400">
                                    <p>Member since: {{ $user->created_at->format('M d, Y') }}</p>
                                    <p>
                                        {{ $user->shortUrls()->count() }} URLs created • 
                                        {{ $user->shortUrls()->sum('clicks') }} total clicks
                                    </p>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- User Activity Summary -->
            <div class="mt-8 bg-white dark:bg-gray-800 shadow-xl rounded-2xl border border-gray-200 dark:border-gray-700">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                        User Activity Summary
                    </h3>
                    
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                        <!-- URLs Created -->
                        <div class="bg-gradient-to-r from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 rounded-lg p-4">
                            <div class="flex items-center">
                                <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                                </svg>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-blue-600 dark:text-blue-400">URLs Created</p>
                                    <p class="text-2xl font-bold text-blue-900 dark:text-blue-100">
                                        {{ $user->shortUrls()->count() }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Total Clicks -->
                        <div class="bg-gradient-to-r from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/20 rounded-lg p-4">
                            <div class="flex items-center">
                                <svg class="w-8 h-8 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-purple-600 dark:text-purple-400">Total Clicks</p>
                                    <p class="text-2xl font-bold text-purple-900 dark:text-purple-100">
                                        {{ number_format($user->shortUrls()->sum('clicks')) }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Average Clicks -->
                        <div class="bg-gradient-to-r from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 rounded-lg p-4">
                            <div class="flex items-center">
                                <svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-green-600 dark:text-green-400">Avg Clicks/URL</p>
                                    <p class="text-2xl font-bold text-green-900 dark:text-green-100">
                                        {{ $user->shortUrls()->count() > 0 ? number_format($user->shortUrls()->sum('clicks') / $user->shortUrls()->count(), 1) : '0' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 