@extends('layouts.app')

@section('content')
<div class="container max-w-[768px] mx-auto px-4">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Create New Post</h1>
        <a href="/profile/{{ auth()->user()->id }}" class="text-gray-500 hover:text-gray-700 transition-colors">
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
            class="p-4 rounded-lg border mb-4 shadow-sm">
            <p x-text="notification.message" class="font-medium"></p>
        </div>

        <!-- Image Upload -->
        <div class="bg-white p-8 rounded-xl shadow-sm border border-gray-100">
            <h2 class="text-xl font-semibold mb-6 text-gray-800 text-center">Upload Photo</h2>

            <div class="flex flex-col items-center">
                <div
                    @click="$refs.fileInput.click()"
                    class="cursor-pointer w-full h-96 rounded-lg transition-all duration-300"
                    :class="imageUrl ? 'border-none' : 'border-2 border-dashed border-gray-300 hover:border-primary hover:bg-gray-50'">
                    <div class="h-full w-full flex items-center justify-center">
                        <!-- Preview Image -->
                        <template x-if="imageUrl">
                            <img :src="imageUrl" class="w-full h-full rounded-lg object-contain">
                        </template>

                        <!-- Upload Icon and Text -->
                        <template x-if="!imageUrl">
                            <div class="text-center p-6">
                                <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-16 w-16 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <p class="mt-4 text-gray-600 font-medium">Click to upload a photo</p>
                                <p class="mt-2 text-sm text-gray-500">JPG, PNG, GIF up to 10MB</p>
                            </div>
                        </template>
                    </div>
                </div>

                <input
                    type="file"
                    class="hidden"
                    x-ref="fileInput"
                    @change="handleImageChange"
                    accept="image/*"
                    name="image">

                @error('image')
                <p class="text-red-500 text-sm mt-4">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Caption Form -->
        <div class="bg-white p-8 rounded-xl shadow-sm border border-gray-100">
            <h2 class="text-xl font-semibold mb-6 text-gray-800">Write a Caption</h2>

            <div class="relative">
                <textarea
                    id="caption"
                    placeholder="What's on your mind?"
                    maxlength="2200"
                    class="bg-gray-50 rounded-lg w-full py-4 px-5 leading-relaxed focus:outline-none focus:ring-2 focus:ring-primary/20 focus:bg-white resize-none h-40 pr-16 transition-all duration-300"
                    name="caption"
                    x-model="caption"
                    @input="handleCaptionChange"></textarea>

                <div class="absolute bottom-4 right-4 text-sm font-medium" :class="captionLength > 2000 ? 'text-red-500' : 'text-gray-400'">
                    <span x-text="captionLength + '/2200'"></span>
                </div>
            </div>

            @error('caption')
            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
            @enderror
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-end gap-4 mt-8" x-show="hasChanges">
            <a
                href="/profile/{{ auth()->user()->id }}"
                class="px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors font-medium"
                :class="{'opacity-50 cursor-not-allowed': isSubmitting}"
                :disabled="isSubmitting">
                Cancel
            </a>
            <button
                @click="submitPost"
                class="px-6 py-3 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors font-medium"
                :disabled="!isValid || isSubmitting"
                :class="{'opacity-50 cursor-not-allowed': !isValid || isSubmitting}">
                <span x-show="!isSubmitting">Share Post</span>
                <span x-show="isSubmitting" class="flex items-center">
                    <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
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