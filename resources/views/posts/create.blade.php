@extends('layouts.app')

@section('content')
<div class="container max-w-[768px] mx-auto px-4">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100">Create New Post</h1>
        <a href="/profile/{{ auth()->user()->id }}" class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 transition-colors">
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
            x-transition:enter-start="opacity-0 transform -translate-y-2"
            x-transition:enter-end="opacity-100 transform translate-y-0"
            x-transition:leave="transition ease-in duration-300"
            x-transition:leave-start="opacity-100 transform translate-y-0"
            x-transition:leave-end="opacity-0 transform -translate-y-2"
            :class="notification.type === 'error' ? 'bg-red-100 text-red-700 border-red-200' : 'bg-green-100 text-green-700 border-green-200'"
            class="p-4 rounded-lg border mb-4 shadow-sm"
        >
            <p x-text="notification.message" class="font-medium"></p>
        </div>

        <!-- Image Upload -->
        <div class="bg-white dark:bg-slate-800 p-8 rounded-xl shadow-sm border border-gray-100 dark:border-slate-700">
            <h2 class="text-xl font-semibold mb-6 text-gray-800 dark:text-gray-100 text-center">Upload Photo</h2>
            
            <div class="flex flex-col items-center">
                <div 
                    @click="$refs.fileInput.click()" 
                    class="cursor-pointer w-full h-96 rounded-lg transition-all duration-300"
                    :class="imageUrl ? 'border-none' : 'border-2 border-dashed border-gray-300 dark:border-gray-600 hover:border-primary hover:bg-gray-50 dark:hover:bg-slate-700'"
                >
                    <div class="h-full w-full flex items-center justify-center">
                        <!-- Preview Image -->
                        <template x-if="imageUrl">
                            <img :src="imageUrl" class="w-full h-full rounded-lg object-contain">
                        </template>

                        <!-- Upload Icon and Text -->
                        <template x-if="!imageUrl">
                            <div class="text-center p-6">
                                <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-16 w-16 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <p class="mt-4 text-gray-600 dark:text-gray-300 font-medium">Click to upload a photo</p>
                                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">JPG, PNG, GIF up to 10MB</p>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="mt-4 w-full">
                    <input type="file" class="hidden" x-ref="fileInput" @change="handleFileChange" accept="image/*" name="image">
                    @error('image')
                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Caption Form -->
        <div class="bg-white dark:bg-slate-800 p-8 rounded-xl shadow-sm border border-gray-100 dark:border-slate-700">
            <h2 class="text-xl font-semibold mb-6 text-gray-800 dark:text-gray-100 text-center">Add Caption</h2>
            
            <div class="space-y-4">
                <div>
                    <textarea
                        x-model="caption"
                        name="caption"
                        placeholder="Write a caption..."
                        class="w-full p-4 border border-gray-300 dark:border-slate-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent resize-none bg-gray-50 dark:bg-slate-700 dark:text-gray-100"
                        rows="4"
                    ></textarea>
                    @error('caption')
                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <div class="mt-8 flex justify-end">
                <button
                    @click="submit"
                    :disabled="!imageUrl || isSubmitting"
                    :class="(!imageUrl || isSubmitting) ? 'opacity-50 cursor-not-allowed' : 'hover:bg-indigo-700'"
                    class="px-8 py-3 bg-indigo-600 dark:bg-indigo-700 text-white font-medium rounded-lg transition-colors"
                >
                    <span x-show="!isSubmitting">Share</span>
                    <span x-show="isSubmitting" class="flex items-center">
                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Posting...
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Alpine.js Component Script -->
<script>
function createPost() {
    return {
        imageUrl: null,
        caption: '',
        isSubmitting: false,
        notification: {
            show: false,
            message: '',
            type: 'success'
        },
        
        handleFileChange(event) {
            const file = event.target.files[0];
            if (!file) return;
            
            // Validate file size (10MB max)
            if (file.size > 10 * 1024 * 1024) {
                this.showNotification('File is too large. Maximum size is 10MB.', 'error');
                return;
            }
            
            // Validate file type
            if (!['image/jpeg', 'image/png', 'image/gif'].includes(file.type)) {
                this.showNotification('Invalid file type. Please upload a JPG, PNG, or GIF.', 'error');
                return;
            }
            
            // Create preview URL
            this.imageUrl = URL.createObjectURL(file);
        },
        
        showNotification(message, type = 'success') {
            this.notification.message = message;
            this.notification.type = type;
            this.notification.show = true;
            
            setTimeout(() => {
                this.notification.show = false;
            }, 5000);
        },
        
        submit() {
            if (!this.imageUrl || this.isSubmitting) return;
            
            this.isSubmitting = true;
            
            // Create form data
            const formData = new FormData();
            formData.append('image', this.$refs.fileInput.files[0]);
            formData.append('caption', this.caption);
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
            
            // Submit form
            fetch('/p', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                window.location.href = data.redirect;
            })
            .catch(error => {
                this.isSubmitting = false;
                this.showNotification('There was an error uploading your post. Please try again.', 'error');
                console.error('Error:', error);
            });
        }
    };
}
</script>
@endsection



