<x-app-layout>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <!-- Header Section -->
        <div class="bg-gradient-to-r from-purple-600 to-blue-700 dark:from-gray-800 dark:to-gray-900">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-white">Edit Invite Code</h1>
                        <p class="text-purple-100 dark:text-gray-300 mt-2">Modify the invite code: {{ $inviteCode->code }}</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('admin.invite-codes.index') }}" 
                           class="bg-white/10 hover:bg-white/20 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Back to Invite Codes
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Form Card -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="p-8">
                    <form method="POST" action="{{ route('admin.invite-codes.update', $inviteCode) }}" class="space-y-6">
                        @csrf
                        @method('PATCH')

                        <!-- Code Display (Read-only) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Invite Code
                            </label>
                            <div class="flex items-center p-3 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg">
                                <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1721 9z"></path>
                                </svg>
                                <span class="text-lg font-mono font-semibold text-gray-900 dark:text-white">{{ $inviteCode->code }}</span>
                                @if($inviteCode->used_by)
                                    <span class="ml-auto inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                        Used by {{ $inviteCode->user->name ?? 'Unknown User' }}
                                    </span>
                                @endif
                            </div>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                The invite code cannot be changed after creation.
                            </p>
                        </div>

                        <!-- Description Field -->
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Description
                                <span class="text-gray-500 dark:text-gray-400 font-normal">(Optional)</span>
                            </label>
                            <input type="text" 
                                   name="description" 
                                   id="description" 
                                   value="{{ old('description', $inviteCode->description) }}"
                                   placeholder="Description for this invite code"
                                   class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500 @error('description') border-red-500 @enderror">
                            @error('description')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                Optional description to help you remember what this code is for.
                            </p>
                        </div>

                        <!-- Status Toggle -->
                        <div>
                            <div class="flex items-center">
                                <input type="checkbox" 
                                       name="is_active" 
                                       id="is_active" 
                                       value="1"
                                       {{ old('is_active', $inviteCode->is_active) ? 'checked' : '' }}
                                       class="rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-blue-600 focus:border-blue-500 focus:ring-blue-500">
                                <label for="is_active" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                                    Active code
                                </label>
                            </div>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                If unchecked, this code will be deactivated and cannot be used for registration.
                            </p>
                        </div>

                        <!-- Single Use Toggle -->
                        <div>
                            <div class="flex items-center">
                                <input type="checkbox" 
                                       name="is_single_use" 
                                       id="is_single_use" 
                                       value="1"
                                       {{ old('is_single_use', $inviteCode->is_single_use) ? 'checked' : '' }}
                                       {{ $inviteCode->used_by ? 'disabled' : '' }}
                                       class="rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-blue-600 focus:border-blue-500 focus:ring-blue-500 {{ $inviteCode->used_by ? 'opacity-50' : '' }}">
                                <label for="is_single_use" class="ml-2 block text-sm text-gray-700 dark:text-gray-300 {{ $inviteCode->used_by ? 'opacity-50' : '' }}">
                                    Single-use code
                                </label>
                            </div>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                {{ $inviteCode->used_by 
                                    ? 'This setting cannot be changed because the code has already been used.' 
                                    : 'If checked, this code can only be used once. If unchecked, the code can be used multiple times.' }}
                            </p>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center justify-between pt-6 border-t border-gray-200 dark:border-gray-700">
                            <div class="flex items-center space-x-4">
                                <button type="submit" 
                                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200 flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Update Invite Code
                                </button>
                                <a href="{{ route('admin.invite-codes.index') }}" 
                                   class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200">
                                    Cancel
                                </a>
                            </div>

                            <!-- Code Stats -->
                            <div class="text-right">
                                <div class="text-sm text-gray-600 dark:text-gray-400">
                                    <p>Created: {{ $inviteCode->created_at->format('M d, Y') }}</p>
                                    <p>By: {{ $inviteCode->creator->name ?? 'Unknown' }}</p>
                                    @if($inviteCode->used_at)
                                        <p>Used: {{ $inviteCode->used_at->format('M d, Y') }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Code Details Card -->
            <div class="mt-8 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                        Code Details
                    </h3>
                    
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                        <!-- Code Status -->
                        <div class="bg-gradient-to-r from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 rounded-lg p-4">
                            <div class="flex items-center">
                                <svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-green-600 dark:text-green-400">Status</p>
                                    <p class="text-lg font-bold text-green-900 dark:text-green-100">
                                        {{ $inviteCode->is_active ? 'Active' : 'Inactive' }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Usage Status -->
                        <div class="bg-gradient-to-r from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 rounded-lg p-4">
                            <div class="flex items-center">
                                <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"></path>
                                </svg>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-blue-600 dark:text-blue-400">Usage</p>
                                    <p class="text-lg font-bold text-blue-900 dark:text-blue-100">
                                        {{ $inviteCode->used_by ? 'Used' : 'Unused' }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Code Type -->
                        <div class="bg-gradient-to-r from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/20 rounded-lg p-4">
                            <div class="flex items-center">
                                <svg class="w-8 h-8 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1721 9z"></path>
                                </svg>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-purple-600 dark:text-purple-400">Type</p>
                                    <p class="text-lg font-bold text-purple-900 dark:text-purple-100">
                                        {{ $inviteCode->is_single_use ? 'Single Use' : 'Multi Use' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($inviteCode->used_by)
                        <!-- Usage Details -->
                        <div class="mt-6 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                            <h4 class="text-sm font-medium text-blue-800 dark:text-blue-200 mb-2">Usage Details</h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm text-blue-700 dark:text-blue-300">
                                <div>
                                    <span class="font-medium">Used by:</span> {{ $inviteCode->user->name ?? 'Unknown User' }}
                                </div>
                                <div>
                                    <span class="font-medium">Used at:</span> {{ $inviteCode->used_at ? $inviteCode->used_at->format('M d, Y g:i A') : 'Unknown' }}
                                </div>
                                @if($inviteCode->user->email)
                                    <div class="sm:col-span-2">
                                        <span class="font-medium">User email:</span> {{ $inviteCode->user->email }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Registration URL Card -->
            <div class="mt-6 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-6">
                <div class="flex">
                    <svg class="w-5 h-5 text-blue-400 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                    <div class="ml-3 flex-1">
                        <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200">
                            Registration URL
                        </h3>
                        <div class="mt-2">
                            <div class="flex items-center space-x-2">
                                <input type="text" 
                                       readonly 
                                       value="{{ route('register', ['code' => $inviteCode->code]) }}"
                                       class="flex-1 text-sm bg-white dark:bg-gray-700 border border-blue-300 dark:border-blue-600 rounded px-3 py-2 text-blue-700 dark:text-blue-300 font-mono">
                                <button type="button" 
                                        onclick="copyToClipboard('{{ route('register', ['code' => $inviteCode->code]) }}')"
                                        class="px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded font-medium transition-colors">
                                    Copy
                                </button>
                            </div>
                            <p class="mt-2 text-sm text-blue-700 dark:text-blue-300">
                                Share this URL with users to allow them to register using this invite code.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function() {
                // Show a brief success message
                const button = event.target;
                const originalText = button.textContent;
                button.textContent = 'Copied!';
                button.classList.add('bg-green-600', 'hover:bg-green-700');
                button.classList.remove('bg-blue-600', 'hover:bg-blue-700');
                
                setTimeout(() => {
                    button.textContent = originalText;
                    button.classList.remove('bg-green-600', 'hover:bg-green-700');
                    button.classList.add('bg-blue-600', 'hover:bg-blue-700');
                }, 2000);
            });
        }
    </script>
</x-app-layout> 