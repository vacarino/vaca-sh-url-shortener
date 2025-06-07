<x-app-layout>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <!-- Header Section -->
        <div class="bg-gradient-to-r from-purple-600 to-blue-700 dark:from-gray-800 dark:to-gray-900">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <div class="flex items-center">
                    <a href="{{ route('admin.invite-codes.index') }}" class="text-white hover:text-purple-200 mr-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-3xl font-bold text-white">Create Invite Code</h1>
                        <p class="text-purple-100 dark:text-gray-300 mt-2">Generate a new invitation code for user registration</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="p-6">
                    <form method="POST" action="{{ route('admin.invite-codes.store') }}" class="space-y-6">
                        @csrf

                        <!-- Custom Code -->
                        <div>
                            <label for="code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Custom Code (Optional)
                            </label>
                            <input type="text" name="code" id="code" value="{{ old('code') }}" 
                                   placeholder="Leave empty to auto-generate"
                                   class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500 @error('code') border-red-300 dark:border-red-600 @enderror">
                            @error('code')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                If left empty, an 8-character code will be generated automatically.
                            </p>
                        </div>

                        <!-- Description -->
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Description (Optional)
                            </label>
                            <input type="text" name="description" id="description" value="{{ old('description') }}" 
                                   placeholder="e.g., For beta testers, Marketing campaign, etc."
                                   class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500 @error('description') border-red-300 dark:border-red-600 @enderror">
                            @error('description')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Single Use -->
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input id="is_single_use" name="is_single_use" type="checkbox" checked
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="is_single_use" class="text-gray-700 dark:text-gray-300">
                                    Single use only
                                </label>
                                <p class="text-gray-500 dark:text-gray-400">If checked, this code can only be used once for registration.</p>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex items-center justify-end space-x-4">
                            <a href="{{ route('admin.invite-codes.index') }}" 
                               class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200">
                                Cancel
                            </a>
                            <button type="submit" 
                                    class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Create Invite Code
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 