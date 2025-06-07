<x-app-layout>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <!-- Header Section -->
        <div class="bg-gradient-to-r from-purple-600 to-blue-700 dark:from-gray-800 dark:to-gray-900">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-white">Create Invite Code</h1>
                        <p class="text-purple-100 dark:text-gray-300 mt-2">Generate a new invitation code for users</p>
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

        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Form Card -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="p-6">
                    <form method="POST" action="{{ route('admin.invite-codes.store') }}" class="space-y-6">
                        @csrf

                        <!-- Code Field -->
                        <div>
                            <label for="code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Invite Code
                                <span class="text-gray-500 dark:text-gray-400 font-normal">(Leave blank to auto-generate)</span>
                            </label>
                            <input type="text" 
                                   name="code" 
                                   id="code" 
                                   value="{{ old('code') }}"
                                   placeholder="Enter custom code or leave blank for auto-generation"
                                   class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500 @error('code') border-red-500 @enderror">
                            @error('code')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                Code must be alphanumeric and unique. Maximum 20 characters.
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
                                   value="{{ old('description') }}"
                                   placeholder="Description for this invite code"
                                   class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500 @error('description') border-red-500 @enderror">
                            @error('description')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                Optional description to help you remember what this code is for.
                            </p>
                        </div>

                        <!-- Single Use Toggle -->
                        <div>
                            <div class="flex items-center">
                                <input type="checkbox" 
                                       name="is_single_use" 
                                       id="is_single_use" 
                                       value="1"
                                       {{ old('is_single_use', true) ? 'checked' : '' }}
                                       class="rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-blue-600 focus:border-blue-500 focus:ring-blue-500">
                                <label for="is_single_use" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                                    Single-use code
                                </label>
                            </div>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                If checked, this code can only be used once. If unchecked, the code can be used multiple times.
                            </p>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <a href="{{ route('admin.invite-codes.index') }}" 
                               class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg font-medium transition-colors duration-200">
                                Cancel
                            </a>
                            <button type="submit" 
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Create Invite Code
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Info Card -->
            <div class="mt-6 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-6">
                <div class="flex">
                    <svg class="w-5 h-5 text-blue-400 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200">
                            Invite Code Guidelines
                        </h3>
                        <div class="mt-2 text-sm text-blue-700 dark:text-blue-300">
                            <ul class="list-disc pl-5 space-y-1">
                                <li>Codes must be alphanumeric (letters and numbers only)</li>
                                <li>Maximum length is 20 characters</li>
                                <li>Codes are case-insensitive and will be converted to uppercase</li>
                                <li>If you don't specify a code, one will be automatically generated</li>
                                <li>Single-use codes become inactive after being used once</li>
                                <li>Multi-use codes can be used by multiple users until deactivated</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 