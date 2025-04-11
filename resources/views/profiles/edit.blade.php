@extends('layouts.app')

@section('content')
<div class="container max-w-[768px] mx-auto px-4 py-8">
    <!-- Page Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100">Edit Profile</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1">Customize your profile information</p>
        </div>
        <a href="/profile/{{ $user->id }}"
            class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-colors p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-full">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </a>
    </div>

    <div x-data="profileEdit()" class="space-y-6">
        <!-- Notification -->
        <div
            x-show="notification.show"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform -translate-y-2"
            x-transition:enter-end="opacity-100 transform translate-y-0"
            x-transition:leave="transition ease-in duration-300"
            x-transition:leave-start="opacity-100 transform translate-y-0"
            x-transition:leave-end="opacity-0 transform -translate-y-2"
            :class="notification.type === 'error' ? 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 border-red-200 dark:border-red-800' : 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 border-green-200 dark:border-green-800'"
            class="p-4 rounded-xl border mb-4 shadow-sm flex items-center">
            <div :class="notification.type === 'error' ? 'text-red-500' : 'text-green-500'" class="mr-3">
                <svg x-show="notification.type === 'success'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <svg x-show="notification.type === 'error'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </div>
            <p x-text="notification.message" class="font-medium"></p>
        </div>

        <!-- Profile Picture Form -->
        <div class="bg-white dark:bg-gray-800 p-8 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-md transition-shadow duration-300">
            <h2 class="text-xl font-semibold mb-2 text-gray-800 dark:text-gray-100">Profile Picture</h2>
            <p class="text-gray-500 dark:text-gray-400 text-sm mb-6">Recommended size: 400x400px</p>

            <div class="flex flex-col items-center">
                <div
                    @click="$refs.fileInput.click()"
                    class="cursor-pointer w-48 h-48 rounded-full transition-all duration-300 relative group"
                    :class="{'border-2 border-dashed border-gray-300 dark:border-gray-600 hover:border-primary': !imageUrl}">
                    <!-- Preview Image -->
                    <template x-if="imageUrl">
                        <img :src="imageUrl" class="w-full h-full rounded-full object-cover">
                    </template>

                    <!-- Upload Icon and Text -->
                    <template x-if="!imageUrl">
                        <div class="w-full h-full flex flex-col items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400 dark:text-gray-500 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Click to upload photo</p>
                        </div>
                    </template>

                    <!-- Hover Overlay -->
                    <div class="absolute inset-0 bg-black bg-opacity-40 opacity-0 group-hover:opacity-100 transition-opacity duration-300 rounded-full flex items-center justify-center">
                        <p class="text-white font-medium">Change Photo</p>
                    </div>

                    <input type="file" class="hidden" x-ref="fileInput" @change="handleImageChange" accept="image/*" name="image">
                </div>

                @error('image')
                <p class="text-red-500 text-sm mt-4">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Background Picture Form -->
        <div class="bg-white p-8 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow duration-300">
            <h2 class="text-xl font-semibold mb-2 text-gray-800">Profile Background</h2>
            <p class="text-gray-500 text-sm mb-6">Recommended size: 1500x500px</p>

            <div class="flex flex-col items-center">
                <div
                    @click="$refs.backgroundInput.click()"
                    class="cursor-pointer w-full h-48 rounded-xl transition-all duration-300 relative group"
                    :class="{'border-2 border-dashed border-gray-300 dark:border-gray-600 hover:border-primary': !backgroundUrl}">
                    <!-- Preview Image -->
                    <template x-if="backgroundUrl">
                        <img :src="backgroundUrl" class="w-full h-full rounded-xl object-cover">
                    </template>

                    <!-- Upload Icon and Text -->
                    <template x-if="!backgroundUrl">
                        <div class="w-full h-full flex flex-col items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400 dark:text-gray-500 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Click to upload background</p>
                        </div>
                    </template>

                    <!-- Hover Overlay -->
                    <div class="absolute inset-0 bg-black bg-opacity-40 opacity-0 group-hover:opacity-100 transition-opacity duration-300 rounded-xl flex items-center justify-center">
                        <p class="text-white font-medium">Change Background</p>
                    </div>

                    <input type="file" class="hidden" x-ref="backgroundInput" @change="handleBackgroundChange" accept="image/*" name="background">
                </div>
            </div>
        </div>

        <!-- Bio Form -->
        <div class="bg-white p-8 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow duration-300">
            <h2 class="text-xl font-semibold mb-2 text-gray-800">Bio</h2>
            <p class="text-gray-500 text-sm mb-6">Tell others about yourself</p>

            <div class="relative">
                <textarea
                    id="description"
                    placeholder="Write something about yourself..."
                    maxlength="100"
                    class="bg-gray-50 dark:bg-gray-700 dark:text-gray-200 rounded-xl w-full py-4 px-5 leading-relaxed focus:outline-none focus:ring-2 focus:ring-primary/20 dark:focus:ring-primary/40 focus:bg-white dark:focus:bg-gray-600 resize-none h-32 pr-16 transition-all duration-300"
                    name="description"
                    x-model="bio"
                    @input="handleBioChange">{{ old('description') ?? $user->profile->description }}</textarea>

                <div class="absolute bottom-4 right-4 text-sm font-medium"
                    :class="bioLength > 80 ? 'text-red-500' : 'text-gray-400 dark:text-gray-500'">
                    <span x-text="bioLength + '/100'"></span>
                </div>
            </div>

            @error('description')
            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
            @enderror
        </div>

        <!-- Main Action Buttons -->
        <div class="flex justify-end gap-4 mt-8" x-show="hasChanges">
            <a
                href="/profile/{{ $user->id }}"
                class="px-6 py-3 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition-all duration-300 font-medium inline-flex items-center"
                :class="{'opacity-50 cursor-not-allowed': isSubmitting}"
                :disabled="isSubmitting">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                Cancel
            </a>
            <button
                @click="saveProfile"
                class="px-6 py-3 bg-primary text-white rounded-xl hover:bg-primary/90 transition-all duration-300 font-medium inline-flex items-center shadow-sm hover:shadow-md"
                :disabled="isSubmitting"
                :class="{'opacity-50 cursor-not-allowed': isSubmitting}">
                <span x-show="!isSubmitting" class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Save Changes
                </span>
                <span x-show="isSubmitting" class="flex items-center">
                    <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Saving...
                </span>
            </button>
        </div>
    </div>
</div>

<script>
    function profileEdit() {
        return {
            imageUrl: '{{ $user->profile->profileImage() }}',
            originalImageUrl: '{{ $user->profile->profileImage() }}',
            backgroundUrl: '{{ $user->profile->backgroundImage() }}',
            originalBackgroundUrl: '{{ $user->profile->backgroundImage() }}',
            bio: "{{ old('description') ?? $user->profile->description }}",
            originalBio: "{{ $user->profile->description }}",
            bioLength: {
                {
                    strlen(old('description') ?? $user - > profile - > description)
                }
            },
            hasChanges: false,
            imageFile: null,
            backgroundFile: null,
            isSubmitting: false,
            notification: {
                show: false,
                message: '',
                type: 'success'
            },

            init() {
                // Cleanup object URLs when component is destroyed
                window.addEventListener('beforeunload', () => {
                    if (this.imageUrl && this.imageUrl.startsWith('blob:')) {
                        URL.revokeObjectURL(this.imageUrl);
                    }
                    if (this.backgroundUrl && this.backgroundUrl.startsWith('blob:')) {
                        URL.revokeObjectURL(this.backgroundUrl);
                    }
                });
            },

            handleImageChange(event) {
                const file = event.target.files[0];
                if (file) {
                    // Check file size (e.g., 5MB limit)
                    if (file.size > 5 * 1024 * 1024) {
                        this.showNotification('Image size should be less than 5MB', 'error');
                        event.target.value = '';
                        return;
                    }

                    // Check file type
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
                    // Check file size (e.g., 5MB limit)
                    if (file.size > 5 * 1024 * 1024) {
                        this.showNotification('Image size should be less than 5MB', 'error');
                        event.target.value = '';
                        return;
                    }

                    // Check file type
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
                this.hasChanges = this.bio !== this.originalBio || this.imageUrl !== this.originalImageUrl;
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
                    // Create FormData for image upload
                    if (this.imageFile || this.backgroundFile) {
                        const imageFormData = new FormData();
                        if (this.imageFile) {
                            imageFormData.append('image', this.imageFile);
                        }
                        if (this.backgroundFile) {
                            imageFormData.append('background', this.backgroundFile);
                        }
                        imageFormData.append('_method', 'PATCH');

                        const imageResponse = await fetch('/profile/{{ $user->id }}/picture', {
                            method: 'POST',
                            body: imageFormData,
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        });

                        if (!imageResponse.ok) {
                            const data = await imageResponse.json();
                            throw new Error(data.message || 'Failed to update profile pictures');
                        }
                    }

                    // Update bio if changed
                    if (this.bio !== this.originalBio) {
                        const bioResponse = await fetch('/profile/{{ $user->id }}/bio', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                description: this.bio,
                                _method: 'PATCH'
                            })
                        });

                        if (!bioResponse.ok) {
                            const data = await bioResponse.json();
                            throw new Error(data.message || 'Failed to update bio');
                        }
                    }

                    this.showNotification('Profile updated successfully!');

                    setTimeout(() => {
                        window.location.href = '/profile/{{ $user->id }}';
                    }, 1000);

                } catch (error) {
                    this.showNotification(error.message || 'Error updating profile', 'error');
                } finally {
                    this.isSubmitting = false;
                }
            }
        }
    }
</script>
@endsection