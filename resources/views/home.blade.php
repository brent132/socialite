@extends('layouts.app')

@section('content')
<div class="container max-w-[768px] mx-auto py-4">
    @foreach($posts as $post)
    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-xl overflow-hidden mb-6 transition-colors duration-200">
        <!-- Header with user info -->
        <div class="flex justify-between items-center p-4 border-b border-gray-100 dark:border-gray-700">
            <div class="flex items-center gap-3">
                <div>
                    <img src="{{ $post->user->profile->profileImage() }}" alt="" class="w-[40px] h-[40px] rounded-full object-cover border-2 border-gray-100 dark:border-gray-700">
                </div>
                <h3 class="text-sm font-semibold dark:text-gray-200">
                    <a href="/profile/{{ $post->user->id }}" class="hover:text-blue-500 transition-colors">{{ $post->user->username }}</a>
                </h3>
            </div>
            <div class="flex gap-4 items-center">
                @cannot('update', $post->user->profile)
                @if(Auth::user()->following->contains($post->user))
                <form action="{{ route('unfollow', $post->user->profile) }}" method="POST">
                    @csrf
                    <button type="submit" class="px-4 py-1.5 text-sm font-medium text-red-500 hover:text-white border border-red-500 hover:bg-red-500 rounded-full transition-colors duration-300">Unfollow</button>
                </form>
                @else
                <form action="{{ route('follow', $post->user->profile) }}" method="POST">
                    @csrf
                    <button type="submit" class="px-4 py-1.5 text-sm font-medium text-blue-500 hover:text-white border border-blue-500 hover:bg-blue-500 rounded-full transition-colors duration-300">Follow</button>
                </form>
                @endif
                @endcannot

                @can('delete', $post)
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="p-1 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-full transition-colors duration-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
                        </svg>
                    </button>
                    <div x-show="open"
                        @click.away="open = false"
                        class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-md shadow-lg py-1 z-50 transition-colors duration-200">
                        <form action="{{ route('posts.destroy', $post) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200">
                                Delete Post
                            </button>
                        </form>
                    </div>
                </div>
                @endcan
            </div>
        </div>

        <!-- Post Image -->
        <div class="w-full">
            <a href="/p/{{ $post->id }}">
                <img src="/storage/{{ $post->image }}" alt="Post Image" class="w-full object-cover">
            </a>
        </div>

        <!-- Post Content -->
        <div class="p-4 dark:text-gray-200">
            <!-- Like and Comment Counts -->
            <div class="flex items-center gap-4 mb-4">
                <!-- Comment icon -->
                <div class="flex items-center gap-1 transition-transform hover:scale-110">
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="h-6 w-6"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                    <span class="text-sm font-medium" id="post-{{ $post->id }}-comment-count">{{ $post->comments ? $post->comments->count() : 0 }}</span>
                </div>

                <!-- Like button -->
                <div x-data="likeSystem({{ $post->id }}, {{ $post->likedBy(auth()->user()) ? 'true' : 'false' }}, {{ $post->likes->count() }})" class="flex items-center gap-1 transition-transform hover:scale-110">
                    <button @click="toggleLike" class="focus:outline-none">
                        <svg xmlns="http://www.w3.org/2000/svg"
                            class="h-6 w-6 transition-colors duration-200"
                            :class="liked ? 'text-red-500' : 'text-gray-500 dark:text-gray-400'"
                            :fill="liked ? 'currentColor' : 'none'"
                            viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                    </button>
                    <span class="text-sm font-medium" x-text="likeCount"></span>
                </div>
            </div>

            <!-- Caption -->
            <div class="mb-4">
                <p class="text-sm dark:text-gray-300">
                    <span class="font-semibold dark:text-gray-200">{{ $post->user->username }}</span>
                    {{ $post->caption }}
                </p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $post->created_at->diffForHumans() }}</p>
            </div>

            <!-- Comments Section -->
            <div id="post-{{ $post->id }}-comments">
                <!-- Add Comment Form -->
                <div class="flex items-start gap-3 mb-4">
                    <img src="{{ auth()->user()->profile->profileImage() }}" class="w-8 h-8 rounded-full object-cover">
                    <div class="flex-1">
                        <div class="flex items-center gap-2">
                            <textarea
                                id="commentText-{{ $post->id }}"
                                placeholder="Add a comment..."
                                class="flex-1 border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-blue-300 dark:focus:border-blue-500 resize-none overflow-hidden min-h-[40px]"
                                rows="1"
                                required
                                oninput="this.style.height = 'auto'; this.style.height = (this.scrollHeight) + 'px';"></textarea>
                            <button
                                type="button"
                                onclick="submitComment({{ $post->id }})"
                                class="px-3 py-1.5 bg-blue-500 text-white text-sm font-medium rounded-lg hover:bg-blue-600 focus:outline-none transition-colors"
                                id="postButton-{{ $post->id }}">
                                Post
                            </button>
                            <div id="commentLoading-{{ $post->id }}" class="hidden">
                                <svg class="animate-spin h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Comments List -->
                <div class="comments-list">
                    @foreach($post->comments->take(5) as $comment)
                    <div class="comment-item group flex space-x-3 py-3" data-comment-id="{{ $comment->id }}">
                        <img src="{{ $comment->user->profile->profileImage() }}" class="w-7 h-7 rounded-full object-cover" alt="{{ $comment->user->username }}">
                        <div class="flex-1">
                            <div class="flex justify-between items-start">
                                <div>
                                    <span class="font-semibold text-sm">{{ $comment->user->username }}</span>
                                    <span class="text-sm">{{ $comment->comment }}</span>
                                </div>
                                @if($comment->user_id == auth()->id())
                                <div class="flex items-center gap-2 md:opacity-0 md:group-hover:opacity-100 transition-opacity">
                                    <button type="button" class="text-gray-500 hover:text-gray-700" onclick="editComment('{{ $comment->id }}', '{{ addslashes($comment->comment) }}')">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                    <button type="button" class="text-gray-500 hover:text-red-500" onclick="deleteComment('{{ $comment->id }}')">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                                @endif
                            </div>
                            <div class="flex items-center gap-2 mt-1" 
                                 x-data="commentLikeSystem(
                                    {{ $comment->id }}, 
                                    {{ $comment->likes()->where('user_id', auth()->id())->exists() ? 'true' : 'false' }}, 
                                    {{ $comment->likes()->count() }}
                                 )">
                                <button
                                    @click="toggleLike"
                                    class="text-xs hover:text-gray-700 flex items-center gap-1"
                                    :class="{'text-red-500 hover:text-red-700': liked, 'text-gray-500': !liked}">
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="h-3 w-3"
                                        :fill="liked ? 'currentColor' : 'none'"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                    </svg>
                                    <span x-text="likesCount"></span>
                                </button>
                                <span class="text-xs text-gray-400">
                                    @php
                                    $date = new \DateTime($comment->created_at);
                                    $now = new \DateTime();
                                    $diff = $date->diff($now);

                                    if ($diff->d > 0) {
                                    echo $diff->d . 'd ago';
                                    } elseif ($diff->h > 0) {
                                    echo $diff->h . 'h ago';
                                    } elseif ($diff->i > 0) {
                                    echo $diff->i . 'm ago';
                                    } else {
                                    echo 'just now';
                                    }
                                    @endphp
                                </span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Load More Comments -->
                <div class="flex justify-center mt-2">
                    @if($post->comments->count() > 5)
                    <button type="button" onclick="loadMoreComments({{ $post->id }})" class="text-sm text-gray-500 hover:text-gray-700 font-medium px-4 py-2 bg-gray-100 dark:bg-gray-700 dark:text-gray-300 rounded-md" id="loadMoreBtn-{{ $post->id }}">
                        Load more comments
                    </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

<!-- Edit Comment Modal -->
<div id="editCommentModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
    <div class="flex items-center justify-center h-full w-full">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full p-6 relative">
            <h3 class="text-lg font-semibold mb-4 dark:text-gray-200">Edit Comment</h3>
            <form id="editCommentForm" method="POST">
                @csrf
                @method('PATCH')
                <textarea
                    id="editCommentText"
                    name="comment"
                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 overflow-hidden"
                    rows="3"></textarea>
                <div class="flex justify-end mt-4 gap-2">
                    <button type="button" onclick="closeEditModal()" class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 text-sm bg-blue-500 text-white rounded-md hover:bg-blue-600">
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('likeSystem', (postId, initialLiked, initialCount) => ({
            liked: initialLiked,
            likeCount: initialCount,

            toggleLike() {
                fetch(`/p/${postId}/like`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            this.liked = data.liked;
                            this.likeCount = data.count;
                        }
                    })
                    .catch(error => console.error('Error:', error));
            }
        }));

        Alpine.data('commentLikeSystem', (commentId, initialLiked, initialCount) => ({
            liked: initialLiked,
            likesCount: initialCount,

            toggleLike() {
                fetch(`/comments/${commentId}/like`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        credentials: 'same-origin'
                    })
                    .then(response => {
                        if (!response.ok) throw new Error('Network response was not ok');
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            this.liked = data.liked;
                            this.likesCount = data.count;
                        }
                    })
                    .catch(error => console.error('Error:', error));
            }
        }));
    });
</script>
<script>
    // Store the current user ID for use in the loadMoreComments function
    window.currentUserId = {{ auth()->id() }};
</script>
<script src="/js/comments.js"></script>
@endsection

