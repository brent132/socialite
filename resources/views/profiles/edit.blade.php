@extends('layouts.app')

@section('content')
<div class="container max-w-[768px] mx-auto px-4">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100">Edit Profile</h1>
        <a href="/profile/{{ $user->id }}" class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </a>
    </div>

    <div x-data="profileEdit()" class="space-y-6">
        <!-- Notification -->
        <div x-show="notification.show"
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

        <form id="profile-form" method="POST" action="/profile/{{ $user->id }}" enctype="multipart/form-data" @submit.prevent="saveProfile" class="space-y-6">
            @csrf
            @method('PATCH')

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <!-- Profile Image -->
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Profile Image</h2>
                    <div class="flex flex-col items-center">
                        <div class="w-32 h-32 rounded-full overflow-hidden border-4 border-white dark:border-gray-700 shadow-md mb-4 hover-scale"
                            :class="{'border-2 border-dashed border-gray-300 dark:border-gray-600': !imageUrl}">
                            <label for="image-upload" class="cursor-pointer block w-full h-full">
                                <template x-if="imageUrl">
                                    <img :src="imageUrl" alt="Profile Picture" class="w-full h-full object-cover">
                                </template>
                                <template x-if="!imageUrl">
                                    <div class="w-full h-full flex items-center justify-center bg-gray-50 dark:bg-gray-700">
                                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                        </svg>
                                    </div>
                                </template>
                            </label>
                            <input type="file" id="image-upload" name="image" class="hidden" accept="image/*" @change="handleImageChange">
                        </div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Click to upload a new profile picture</p>
                    </div>
                </div>

                <!-- Background Image -->
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Background Image</h2>
                    <div class="w-full h-48 rounded-xl overflow-hidden border-2 border-dashed border-gray-300 dark:border-gray-600 hover-scale"
                        :class="{'border-solid border-gray-200 dark:border-gray-700': backgroundUrl}">
                        <label for="background-upload" class="cursor-pointer block w-full h-full">
                            <template x-if="backgroundUrl">
                                <img :src="backgroundUrl" alt="Background Image" class="w-full h-full object-cover">
                            </template>
                            <template x-if="!backgroundUrl">
                                <div class="w-full h-full flex items-center justify-center bg-gray-50 dark:bg-gray-700">
                                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            </template>
                        </label>
                        <input type="file" id="background-upload" name="background" class="hidden" accept="image/*" @change="handleBackgroundChange">
                    </div>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Recommended size: 1500x500px</p>
                </div>

                <!-- Bio -->
                <div class="p-6">
                    <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Bio</h2>
                    <div class="relative">
                        <textarea
                            id="bio"
                            name="description"
                            x-model="bio"
                            @input="handleBioChange"
                            class="w-full border rounded-xl px-4 py-3 text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 resize-none"
                            rows="3"
                            placeholder="Write a short bio about yourself..."></textarea>
                        <div class="absolute bottom-3 right-4 text-sm font-medium"
                            :class="bioLength > 80 ? 'text-red-500' : 'text-gray-400 dark:text-gray-500'">
                            <span x-text="bioLength"></span>/80
                        </div>
                    </div>
                </div>
            </div>

            <!-- Save Button -->
            <div class="flex justify-end">
                <button
                    type="submit"
                    class="px-6 py-2.5 bg-gradient-to-r from-blue-500 to-purple-500 text-white rounded-xl hover:from-blue-600 hover:to-purple-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition-all duration-200 hover:shadow-lg hover-scale"
                    :disabled="isSubmitting || !hasChanges"
                    :class="{'opacity-50 cursor-not-allowed': isSubmitting || !hasChanges}">
                    <span x-show="!isSubmitting">Save Changes</span>
                    <span x-show="isSubmitting" class="flex items-center">
                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Saving...
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function profileEdit() {
        return {
            imageUrl: '{{ $user->profile->profileImage() }}',
            originalImageUrl: '{{ $user->profile->profileImage() }}',
            backgroundUrl: '{{ $user->profile->backgroundImage() }}',
            originalBackgroundUrl: '{{ $user->profile->backgroundImage() }}',
            bio: '{{ addslashes($user->profile->description) }}',
            originalBio: '{{ addslashes($user->profile->description) }}',
            bioLength: {{ strlen($user->profile->description) }},
            hasChanges: false,
            imageFile: null,
            backgroundFile: null,
            isSubmitting: false,
            notification: {
                show: false,
                message: '',
                type: 'success'
            },

            handleImageChange(event) {
                const file = event.target.files[0];
                if (file) {
                    if (file.size > 5 * 1024 * 1024) {
                        this.showNotification('Image size should be less than 5MB', 'error');
                        event.target.value = '';
                        return;
                    }
                    if (!file.type.match('image.*')) {
                        this.showNotification('Please select an image file', 'error');
                        event.target.value = '';
                        return;
                    }
                    this.imageFile = file;
                    this.imageUrl = URL.createObjectURL(file);
                    this.hasChanges = true;
                }
            },

            handleBackgroundChange(event) {
                const file = event.target.files[0];
                if (file) {
                    if (file.size > 5 * 1024 * 1024) {
                        this.showNotification('Image size should be less than 5MB', 'error');
                        event.target.value = '';
                        return;
                    }
                    if (!file.type.match('image.*')) {
                        this.showNotification('Please select an image file', 'error');
                        event.target.value = '';
                        return;
                    }
                    this.backgroundFile = file;
                    this.backgroundUrl = URL.createObjectURL(file);
                    this.hasChanges = true;
                }
            },

            handleBioChange(event) {
                this.bioLength = event.target.value.length;
                this.hasChanges = this.bio !== this.originalBio || 
                                this.imageUrl !== this.originalImageUrl || 
                                this.backgroundUrl !== this.originalBackgroundUrl;
            },

            showNotification(message, type = 'success') {
                this.notification.show = true;
                this.notification.message = message;
                this.notification.type = type;
                setTimeout(() => {
                    this.notification.show = false;
                }, 3000);
            },

            async saveProfile() {
                if (this.isSubmitting) return;
                this.isSubmitting = true;

                try {
                    const formData = new FormData();
                    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
                    formData.append('_method', 'PATCH');
                    formData.append('description', this.bio);

                    if (this.imageFile) {
                        formData.append('image', this.imageFile);
                    }
                    if (this.backgroundFile) {
                        formData.append('background', this.backgroundFile);
                    }

                    const response = await fetch('/profile/{{ $user->id }}', {
                        method: 'POST',
                        body: formData
                    });

                    if (response.ok) {
                        this.showNotification('Profile updated successfully!');
                        this.originalBio = this.bio;
                        this.originalImageUrl = this.imageUrl;
                        this.originalBackgroundUrl = this.backgroundUrl;
                        this.hasChanges = false;

                        setTimeout(() => {
                            window.location.href = '/profile/{{ $user->id }}';
                        }, 1500);
                    } else {
                        const contentType = response.headers.get('content-type');
                        if (contentType && contentType.includes('application/json')) {
                            const data = await response.json();
                            throw new Error(data.message || 'Failed to update profile');
                        } else {
                            throw new Error(`Server error (${response.status}): Please try again later`);
                        }
                    }
                } catch (error) {
                    console.error('Error updating profile:', error);
                    this.showNotification(error.message || 'Error updating profile', 'error');
                } finally {
                    this.isSubmitting = false;
                }
            }
        };
    }
</script>
@endsection



