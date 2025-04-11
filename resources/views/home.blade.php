@extends('layouts.app')

@section('content')
<div class="container max-w-[640px] mx-auto py-4">
    @foreach($posts as $post)
    <div class="bg-white shadow-sm rounded-xl overflow-hidden mb-8">
        <!-- Header with user info -->
        <div class="flex justify-between items-center p-4 border-b border-gray-100">
            <div class="flex items-center gap-3">
                <div>
                    <img src="{{ $post->user->profile->profileImage() }}" alt="" class="w-[40px] h-[40px] rounded-full object-cover border-2 border-gray-100">
                </div>
                <h3 class="text-sm font-semibold">
                    <a href="/profile/{{ $post->user->id }}" class="hover:text-blue-500 transition-colors">{{ $post->user->username }}</a>
                </h3>
            </div>
            <div>
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
            </div>
        </div>

        <!-- Post Image -->
        <div class="w-full aspect-square">
            <a href="/p/{{ $post->id }}">
                <img src="/storage/{{ $post->image }}" alt="Post by {{ $post->user->username }}" class="w-full h-full object-cover">
            </a>
        </div>

        <!-- Post Content -->
        <div class="p-5">
            <!-- Like system and comments count -->
            <div class="flex items-center gap-4 mb-3">
                <div x-data="likeSystem({{ $post->id }}, {{ $post->likedBy(auth()->user()) ? 'true' : 'false' }}, {{ $post->likes->count() }})">
                    <button type="button"
                        @click="toggleLike"
                        class="flex items-center gap-1 transition-transform hover:scale-110">
                        <svg x-show="!liked"
                            x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 scale-90"
                            x-transition:enter-end="opacity-100 scale-100"
                            class="w-6 h-6"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z">
                            </path>
                        </svg>
                        <svg x-show="liked"
                            x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 scale-90"
                            x-transition:enter-end="opacity-100 scale-100"
                            class="w-6 h-6 text-red-500"
                            fill="currentColor"
                            viewBox="0 0 24 24">
                            <path fill-rule="evenodd"
                                d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z"
                                clip-rule="evenodd">
                            </path>
                        </svg>
                        <span class="text-sm font-medium" x-text="likeCount"></span>
                    </button>
                </div>

                <!-- Comment icon with link to post -->
                <a href="/p/{{ $post->id }}"
                    class="flex items-center gap-1 transition-transform hover:scale-110">
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
                    <span class="text-sm font-medium">{{ $post->comments ? $post->comments->count() : 0 }}</span>
                </a>
            </div>

            <!-- Caption -->
            <div class="mb-4">
                <p class="text-sm">
                    <span class="font-semibold">{{ $post->user->username }}</span> {{ $post->caption }}
                </p>
            </div>

            <!-- Comments Section -->
            <div class="mb-4" id="post-{{ $post->id }}-comments">
                <!-- Add Comment Form -->
                <div class="flex items-start gap-3 mb-4">
                    <img src="{{ auth()->user()->profile->profileImage() }}" class="w-8 h-8 rounded-full object-cover">
                    <div class="flex-1">
                        <div class="flex items-center gap-2">
                            <textarea
                                id="commentText-{{ $post->id }}"
                                placeholder="Add a comment..."
                                class="flex-1 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-blue-300 resize-none overflow-hidden min-h-[40px]"
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
                    @php
                    $comments = $post->comments()->with('user.profile')->latest()->take(5)->get();
                    @endphp

                    @foreach($comments as $comment)
                    <div class="flex items-start gap-2 mb-3 group"
                        x-data="{
                             comment: {
                                 id: {{ $comment->id }},
                                 liked: {{ $comment->liked ? 'true' : 'false' }},
                                 likes_count: {{ $comment->likes_count }}
                             }
                         }">
                        <img src="{{ $comment->user->profile->profileImage() }}" class="w-7 h-7 rounded-full object-cover" alt="{{ $comment->user->username }}">
                        <div class="flex-1">
                            <div class="flex justify-between items-start">
                                <div>
                                    <span class="font-semibold text-sm">{{ $comment->user->username }}</span>
                                    <span class="text-sm">{{ $comment->comment }}</span>
                                </div>
                                @if($comment->user_id == auth()->id())
                                <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
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
                            <div class="flex items-center gap-2 mt-1">
                                <button
                                    @click="toggleLikeComment(comment)"
                                    class="text-xs hover:text-gray-700 flex items-center gap-1"
                                    :class="{'text-red-500 hover:text-red-700': comment.liked, 'text-gray-500': !comment.liked}">
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="h-3 w-3"
                                        :fill="comment.liked ? 'currentColor' : 'none'"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                    </svg>
                                    <span x-text="comment.likes_count"></span>
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

                <!-- Load More / View All Comments -->
                <div class="flex justify-between items-center mt-2">
                    @if($post->comments->count() > 5)
                    <a href="/p/{{ $post->id }}#comments" class="text-sm text-gray-500 hover:text-gray-700 font-medium">
                        Load more
                    </a>
                    @endif
                    <a href="/p/{{ $post->id }}#comments" class="text-sm text-gray-500 hover:text-gray-700 font-medium">
                        View all {{ $post->comments->count() }} comments
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endforeach

    <div class="my-8">
        {{ $posts->links() }}
    </div>
</div>

<!-- Edit Comment Modal -->
<div id="editCommentModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
    <div class="flex items-center justify-center h-full w-full">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6 relative">
            <h3 class="text-lg font-semibold mb-4">Edit Comment</h3>
            <form id="editCommentForm" method="POST">
                @csrf
                @method('PATCH')
                <textarea
                    id="editCommentText"
                    name="comment"
                    class="w-full border rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 overflow-hidden"
                    rows="3"></textarea>
                <div class="flex justify-end mt-4 gap-2">
                    <button type="button" onclick="closeEditModal()" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800">
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
    function editComment(commentId, commentText) {
        // Set the form action
        document.getElementById('editCommentForm').action = `/comments/${commentId}`;

        // Set the comment text
        document.getElementById('editCommentText').value = commentText;

        // Show the modal
        document.getElementById('editCommentModal').classList.remove('hidden');
    }

    function closeEditModal() {
        document.getElementById('editCommentModal').classList.add('hidden');
    }

    // Close modal when clicking outside
    document.getElementById('editCommentModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeEditModal();
        }
    });

    function submitComment(postId) {
        // Get the comment text
        const commentText = document.getElementById(`commentText-${postId}`).value;

        if (!commentText.trim()) {
            return;
        }

        // Hide post button and show loading indicator
        document.getElementById(`postButton-${postId}`).classList.add('hidden');
        document.getElementById(`commentLoading-${postId}`).classList.remove('hidden');

        // Get CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        // Submit the comment via AJAX
        fetch(`/p/${postId}/comments`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    comment: commentText
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Clear the comment text
                    const textarea = document.getElementById(`commentText-${postId}`);
                    textarea.value = '';
                    textarea.style.height = '40px'; // Reset height to default

                    // Create a new comment element
                    const commentsList = document.querySelector(`#post-${postId}-comments .comments-list`);

                    // Create the HTML for the new comment
                    const newCommentHtml = `
                    <div class="flex items-start gap-2 mb-3 group" data-comment-id="${data.comment.id}">
                        <img src="${data.user.profile_image || '/storage/profile/default-avatar.png'}" class="w-7 h-7 rounded-full object-cover" alt="${data.user.username}">
                        <div class="flex-1">
                            <div class="flex justify-between items-start">
                                <div>
                                    <span class="font-semibold text-sm">${data.user.username}</span>
                                    <span class="text-sm">${data.comment.comment}</span>
                                </div>
                                <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <button type="button" class="text-gray-500 hover:text-gray-700" onclick="editComment('${data.comment.id}', '${data.comment.comment.replace(/'/g, "\\'")}')">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                    <button type="button" class="text-gray-500 hover:text-red-500" onclick="deleteComment('${data.comment.id}')">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 mt-1">
                                <div class="text-xs text-gray-500 hover:text-gray-700 flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                    </svg>
                                    <span>0</span>
                                </div>
                                <span class="text-xs text-gray-400">just now</span>
                            </div>
                        </div>
                    </div>
                `;

                    // Add the new comment to the top of the list
                    const tempDiv = document.createElement('div');
                    tempDiv.innerHTML = newCommentHtml;
                    const newComment = tempDiv.firstElementChild;

                    if (commentsList.firstChild) {
                        commentsList.insertBefore(newComment, commentsList.firstChild);
                    } else {
                        commentsList.appendChild(newComment);
                    }

                    // Update the comment count
                    const commentCountElement = document.getElementById(`post-${postId}-comment-count`);
                    if (commentCountElement) {
                        const currentCount = parseInt(commentCountElement.textContent);
                        commentCountElement.textContent = currentCount + 1;
                    }
                }

                // Hide loading indicator
                document.getElementById(`commentLoading-${postId}`).classList.add('hidden');
            })
            .catch(error => {
                console.error('Error adding comment:', error);
                // Hide loading indicator
                document.getElementById(`commentLoading-${postId}`).classList.add('hidden');
            });
    }

    function deleteComment(commentId) {
        if (!confirm('Are you sure you want to delete this comment?')) {
            return;
        }

        // Get CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        // Delete the comment via AJAX
        fetch(`/comments/${commentId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Remove the comment element
                    const commentElement = document.querySelector(`[data-comment-id="${commentId}"]`);
                    if (commentElement) {
                        commentElement.remove();
                    }

                    // Update the comment count for all posts (since we don't know which post this comment belongs to)
                    document.querySelectorAll('[id^="post-"][id$="-comment-count"]').forEach(element => {
                        const currentCount = parseInt(element.textContent);
                        if (currentCount > 0) {
                            element.textContent = currentCount - 1;
                        }
                    });
                }
            })
            .catch(error => {
                console.error('Error deleting comment:', error);
            });
    }

    // Initialize the edit form to handle submission via AJAX
    document.getElementById('editCommentForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const formAction = this.action;
        const commentText = document.getElementById('editCommentText').value;

        if (!commentText.trim()) {
            return;
        }

        // Get CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        // Update the comment via AJAX
        fetch(formAction, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    comment: commentText
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Find the comment element
                    const commentId = formAction.split('/').pop();
                    const commentElement = document.querySelector(`[data-comment-id="${commentId}"]`);

                    if (commentElement) {
                        // Update the comment text
                        const commentTextElement = commentElement.querySelector('.flex-1 > div > div > span:nth-child(2)');
                        if (commentTextElement) {
                            commentTextElement.textContent = commentText;
                        }
                    }

                    // Close the modal
                    closeEditModal();
                }
            })
            .catch(error => {
                console.error('Error updating comment:', error);
            });
    });
</script>
<script>
    // Add global function for comment liking
    window.toggleLikeComment = function(comment) {
        fetch(`/comments/${comment.id}/like`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                credentials: 'same-origin'
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Update the comment in the UI
                    comment.liked = data.liked;
                    comment.likes_count = data.count;
                }
            })
            .catch(error => {
                console.error('Error toggling comment like:', error);
            });
    };
</script>
@endsection