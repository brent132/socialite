@extends('layouts.app')

@section('content')
<div class="container max-w-[768px] mx-auto px-4">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100">Create New Post</h1>
        <a href="/profile/{{ auth()->user()->id }}" class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </a>
    </div>

    <div x-data="createPost()" class="space-y-8">
        <!-- Notification -->
        <div
            x-show="notification.show"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform translate-y-2"
            x-transition:enter-end="opacity-100 transform translate-y-0"
            x-transition:leave="transition ease-in duration-300"
            x-transition:leave-start="opacity-100 transform translate-y-0"
            x-transition:leave-end="opacity-0 transform translate-y-2"
            :class="notification.type === 'error' ? 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 border-red-200 dark:border-red-800' : 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 border-green-200 dark:border-green-800'"
            class="fixed bottom-20 left-4 z-50 p-4 rounded-lg shadow-lg border">
            <div class="flex items-center">
                <svg x-show="notification.type === 'success'" class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                <svg x-show="notification.type === 'error'" class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                </svg>
                <span x-text="notification.message"></span>
            </div>
        </div>

        <!-- Image Upload -->
        <div class="bg-white dark:bg-gray-800 p-8 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 transition-colors duration-200">
            <h2 class="text-xl font-semibold mb-6 text-gray-800 dark:text-gray-100 text-center">Upload Photo</h2>
            
            <div class="relative aspect-square w-full bg-gray-50 dark:bg-gray-900 rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-600 hover:border-gray-400 dark:hover:border-gray-500 transition-colors">
                <input type="file" 
                    @change="handleImageChange" 
                    class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" 
                    accept="image/*">
                
                <div class="absolute inset-0 flex flex-col items-center justify-center" x-show="!imageUrl">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">Click to upload an image</p>
                </div>

                <img x-show="imageUrl" :src="imageUrl" class="absolute inset-0 w-full h-full object-cover rounded-lg">
            </div>

            <!-- Caption Input -->
            <div class="mt-6">
                <label for="caption" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Caption</label>
                <textarea
                    id="caption"
                    x-model="caption"
                    @input="handleCaptionChange"
                    placeholder="Write a caption..."
                    class="w-full px-4 py-3 rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-colors duration-200"
                    rows="3"
                ></textarea>
                <div class="mt-2 flex justify-end">
                    <span x-text="captionLength" class="text-sm text-gray-500 dark:text-gray-400"></span>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-end gap-4" x-show="hasChanges">
            <a
                href="/profile/{{ auth()->user()->id }}"
                class="px-6 py-3 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors font-medium"
                :class="{'opacity-50 cursor-not-allowed': isSubmitting}"
                :disabled="isSubmitting">
                Cancel
            </a>
            <button
                @click="submitPost"
                class="px-6 py-3 bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-lg hover:from-blue-600 hover:to-purple-700 transition-all duration-300 font-medium shadow-md hover:shadow-lg"
                :disabled="!isValid || isSubmitting"
                :class="{'opacity-50 cursor-not-allowed': !isValid || isSubmitting}">
                <span x-show="!isSubmitting">Share Post</span>
                <span x-show="isSubmitting" class="flex items-center">
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Sharing...
                </span>
            </button>
        </div>
    </div>
</div>

<script>
    function createPost() {
        return {
            imageUrl: '',
            imageFile: null,
            caption: '',
            captionLength: 0,
            hasChanges: false,
            isSubmitting: false,
            notification: {
                show: false,
                message: '',
                type: 'success'
            },

            handleImageChange(event) {
                const file = event.target.files[0];
                if (file) {
                    this.imageFile = file;
                    this.imageUrl = URL.createObjectURL(file);
                    this.hasChanges = true;
                }
            },

            handleCaptionChange(event) {
                this.captionLength = event.target.value.length;
                this.hasChanges = this.caption !== '' || this.imageUrl !== '';
            },

            get isValid() {
                return this.imageFile && this.caption.length > 0;
            },

            showNotification(message, type = 'success') {
                this.notification.show = true;
                this.notification.message = message;
                this.notification.type = type;

                setTimeout(() => {
                    this.notification.show = false;
                }, 3000);
            },

            async submitPost() {
                if (!this.isValid || this.isSubmitting) return;

                this.isSubmitting = true;

                const formData = new FormData();
                formData.append('_token', '{{ csrf_token() }}');
                formData.append('caption', this.caption);
                formData.append('image', this.imageFile);

                try {
                    const response = await fetch('/p', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'Accept': 'application/json'
                        }
                    });

                    if (response.ok) {
                        this.showNotification('Post created successfully!');
                        // Wait a brief moment to show the success message
                        setTimeout(() => {
                            window.location.href = '/profile/{{ auth()->user()->id }}';
                        }, 1000);
                    } else {
                        // Check if the response is JSON
                        const contentType = response.headers.get('content-type');
                        if (contentType && contentType.includes('application/json')) {
                            const data = await response.json();
                            throw new Error(data.message || 'Failed to create post');
                        } else {
                            throw new Error('Server error: Failed to create post');
                        }
                    }
                } catch (error) {
                    this.showNotification(error.message || 'Error creating post', 'error');
                    this.isSubmitting = false;
                }
            }
        }
    }
</script>
@endsection

