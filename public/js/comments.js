// Function to load more comments
function loadMoreComments(postId) {
    const loadMoreBtn = document.getElementById(`loadMoreBtn-${postId}`);
    const commentsSection = document.querySelector(`#post-${postId}-comments`);
    const commentsList = commentsSection.querySelector(".comments-list");

    // If comments-list doesn't exist, create it
    if (!commentsList) {
        console.error(`Comments list not found for post ${postId}`);
        return;
    }

    const currentComments =
        commentsList.querySelectorAll(".comment-item").length;

    // Show loading state
    loadMoreBtn.innerHTML = `<svg class="animate-spin h-4 w-4 text-gray-500 inline mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
    </svg> Loading...`;
    loadMoreBtn.disabled = true;

    // Get CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    // Get the current page from the number of comments
    const page = Math.floor(currentComments / 5) + 1;

    // Fetch more comments
    fetch(`/p/${postId}/comments?page=${page}&limit=5`, {
        headers: {
            "X-CSRF-TOKEN": csrfToken,
            Accept: "application/json",
        },
    })
        .then((response) => response.json())
        .then((data) => {
            // Check if we have data in the response
            // The API returns data in 'data' property when using Laravel's paginate
            const comments = data.data || [];

            if (comments.length > 0) {
                comments.forEach((comment) => {
                    // Format the timestamp
                    const timeAgo = formatTimeAgo(new Date(comment.created_at));

                    // Create comment HTML
                    const commentHtml = `
                    <div class="comment-item group flex space-x-3 py-3 border-b border-gray-100 dark:border-gray-700 last:border-0" data-comment-id="${
                        comment.id
                    }">
                        <img src="${
                            comment.user.profile_image ||
                            "/storage/profile/default-avatar.png"
                        }"
                             alt="${comment.user.username}"
                             class="w-7 h-7 rounded-full object-cover">
                        <div class="flex-1">
                            <div class="flex justify-between items-start">
                                <div>
                                    <span class="font-semibold text-sm">${
                                        comment.user.username
                                    }</span>
                                    <span class="text-sm">${
                                        comment.comment
                                    }</span>
                                </div>
                                ${
                                    comment.user_id === window.currentUserId
                                        ? `
                                <div class="flex items-center gap-2 md:opacity-0 md:group-hover:opacity-100 transition-opacity">
                                    <button type="button" class="text-gray-500 hover:text-gray-700" onclick="editComment('${
                                        comment.id
                                    }', '${comment.comment.replace(
                                              /'/g,
                                              "\\'"
                                          )}')">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                    <button type="button" class="text-gray-500 hover:text-red-500" onclick="deleteComment('${
                                        comment.id
                                    }')">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                                `
                                        : ""
                                }
                            </div>
                            <div class="flex items-center gap-2 mt-1" x-data="commentLikeSystem(${
                                comment.id
                            }, ${comment.liked}, ${comment.likes_count})">
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
                                <span class="text-xs text-gray-400">${timeAgo}</span>
                            </div>
                        </div>
                    </div>
                `;

                    // Add the comment to the list
                    commentsList.insertAdjacentHTML("beforeend", commentHtml);
                });

                // Hide the load more button if there are no more comments
                if (comments.length < 5 || !data.next_page_url) {
                    loadMoreBtn.style.display = "none";
                } else {
                    // Reset the button
                    loadMoreBtn.innerHTML = "Load more comments";
                    loadMoreBtn.disabled = false;
                }
            } else {
                // No more comments to load
                loadMoreBtn.style.display = "none";
            }
        })
        .catch((error) => {
            console.error("Error loading more comments:", error);
            loadMoreBtn.innerHTML = "Load more comments";
            loadMoreBtn.disabled = false;
        });
}

// Helper function to format time ago
function formatTimeAgo(date) {
    const now = new Date();
    const diff = Math.floor((now - date) / 1000);

    if (diff < 60) {
        return "just now";
    } else if (diff < 3600) {
        const minutes = Math.floor(diff / 60);
        return `${minutes}m ago`;
    } else if (diff < 86400) {
        const hours = Math.floor(diff / 3600);
        return `${hours}h ago`;
    } else {
        const days = Math.floor(diff / 86400);
        return `${days}d ago`;
    }
}

// Function for comment liking system
function commentLikeSystem(commentId, initialLiked, initialCount) {
    return {
        liked: initialLiked,
        likesCount: initialCount,

        toggleLike() {
            fetch(`/comments/${commentId}/like`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector(
                        'meta[name="csrf-token"]'
                    ).content,
                    Accept: "application/json",
                },
                credentials: "same-origin",
            })
                .then((response) => {
                    if (!response.ok)
                        throw new Error("Network response was not ok");
                    return response.json();
                })
                .then((data) => {
                    if (data.success) {
                        this.liked = data.liked;
                        this.likesCount = data.count;
                    }
                })
                .catch((error) => console.error("Error:", error));
        },
    };
}

// Function to submit a new comment
function submitComment(postId) {
    // Get the comment text
    const commentText = document.getElementById(`commentText-${postId}`).value;

    if (!commentText.trim()) {
        return;
    }

    // Hide post button and show loading indicator
    document.getElementById(`postButton-${postId}`).classList.add("hidden");
    document
        .getElementById(`commentLoading-${postId}`)
        .classList.remove("hidden");

    // Get CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    // Submit the comment via AJAX
    fetch(`/p/${postId}/comments`, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": csrfToken,
            Accept: "application/json",
        },
        body: JSON.stringify({
            comment: commentText,
        }),
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.success) {
                // Clear the comment text
                const textarea = document.getElementById(
                    `commentText-${postId}`
                );
                textarea.value = "";
                textarea.style.height = "40px"; // Reset height to default

                // Create a new comment element
                const commentsList = document.querySelector(
                    `#post-${postId}-comments .comments-list`
                );

                // Create the HTML for the new comment
                const newCommentHtml = `
                <div class="comment-item group flex space-x-3 py-3 border-b border-gray-100 dark:border-gray-700 last:border-0" data-comment-id="${
                    data.comment.id
                }">
                    <img src="${
                        data.user.profile_image ||
                        "/storage/profile/default-avatar.png"
                    }" class="w-7 h-7 rounded-full object-cover" alt="${
                    data.user.username
                }">
                    <div class="flex-1">
                        <div class="flex justify-between items-start">
                            <div>
                                <span class="font-semibold text-sm">${
                                    data.user.username
                                }</span>
                                <span class="text-sm">${
                                    data.comment.comment
                                }</span>
                            </div>
                            <div class="flex items-center gap-2 md:opacity-0 md:group-hover:opacity-100 transition-opacity">
                                <button type="button" class="text-gray-500 hover:text-gray-700" onclick="editComment('${
                                    data.comment.id
                                }', '${data.comment.comment.replace(
                    /'/g,
                    "\\'"
                )}')">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                                <button type="button" class="text-gray-500 hover:text-red-500" onclick="deleteComment('${
                                    data.comment.id
                                }')">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 mt-1" x-data="commentLikeSystem(${
                            data.comment.id
                        }, false, 0)">
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
                                <span x-text="likesCount">0</span>
                            </button>
                            <span class="text-xs text-gray-400">just now</span>
                        </div>
                    </div>
                </div>
                `;

                // Add the new comment to the top of the list
                const tempDiv = document.createElement("div");
                tempDiv.innerHTML = newCommentHtml;
                const newComment = tempDiv.firstElementChild;

                if (commentsList.firstChild) {
                    commentsList.insertBefore(
                        newComment,
                        commentsList.firstChild
                    );
                } else {
                    commentsList.appendChild(newComment);
                }

                // Initialize Alpine.js components on the new comment
                if (window.Alpine) {
                    window.Alpine.initTree(newComment);
                }

                // Update the comment count
                const commentCountElement = document.getElementById(
                    `post-${postId}-comment-count`
                );
                if (commentCountElement) {
                    const currentCount =
                        parseInt(commentCountElement.textContent) || 0;
                    commentCountElement.textContent = currentCount + 1;
                }
            }

            // Hide loading indicator and show post button again
            document
                .getElementById(`commentLoading-${postId}`)
                .classList.add("hidden");
            document
                .getElementById(`postButton-${postId}`)
                .classList.remove("hidden");
        })
        .catch((error) => {
            console.error("Error adding comment:", error);
            // Hide loading indicator and show post button again
            document
                .getElementById(`commentLoading-${postId}`)
                .classList.add("hidden");
            document
                .getElementById(`postButton-${postId}`)
                .classList.remove("hidden");
        });
}

// Function to edit a comment
function editComment(commentId, commentText) {
    // Set the form action
    document.getElementById(
        "editCommentForm"
    ).action = `/comments/${commentId}`;

    // Set the comment text
    document.getElementById("editCommentText").value = commentText;

    // Show the modal
    document.getElementById("editCommentModal").classList.remove("hidden");
}

// Function to close the edit modal
function closeEditModal() {
    document.getElementById("editCommentModal").classList.add("hidden");
}

// Function to delete a comment
function deleteComment(commentId) {
    if (!confirm("Are you sure you want to delete this comment?")) {
        return;
    }

    // Get CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    // Delete the comment via AJAX
    fetch(`/comments/${commentId}`, {
        method: "DELETE",
        headers: {
            "X-CSRF-TOKEN": csrfToken,
            Accept: "application/json",
        },
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.success) {
                // Remove the comment element
                const commentElement = document.querySelector(
                    `[data-comment-id="${commentId}"]`
                );
                if (commentElement) {
                    // Find the post ID from the comment's parent elements
                    const commentsSection = commentElement.closest(
                        '[id^="post-"][id$="-comments"]'
                    );
                    if (commentsSection) {
                        const postId =
                            commentsSection.id.match(/post-(\d+)-comments/)[1];

                        // Update the comment count
                        const commentCountElement = document.getElementById(
                            `post-${postId}-comment-count`
                        );
                        if (commentCountElement) {
                            const currentCount =
                                parseInt(commentCountElement.textContent) || 0;
                            if (currentCount > 0) {
                                commentCountElement.textContent =
                                    currentCount - 1;
                            }
                        }
                    }

                    // Remove the comment element
                    commentElement.remove();
                }
            }
        })
        .catch((error) => {
            console.error("Error deleting comment:", error);
        });
}

// Initialize the edit form to handle submission via AJAX
document.addEventListener("DOMContentLoaded", function () {
    const editCommentForm = document.getElementById("editCommentForm");
    if (editCommentForm) {
        editCommentForm.addEventListener("submit", function (e) {
            e.preventDefault();

            const formAction = this.action;
            const commentText =
                document.getElementById("editCommentText").value;

            if (!commentText.trim()) {
                return;
            }

            // Get CSRF token
            const csrfToken = document.querySelector(
                'meta[name="csrf-token"]'
            ).content;

            // Update the comment via AJAX
            fetch(formAction, {
                method: "PATCH",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken,
                    Accept: "application/json",
                },
                body: JSON.stringify({
                    comment: commentText,
                }),
            })
                .then((response) => response.json())
                .then((data) => {
                    if (data.success) {
                        // Find the comment element
                        const commentId = formAction.split("/").pop();
                        const commentElement = document.querySelector(
                            `[data-comment-id="${commentId}"]`
                        );

                        if (commentElement) {
                            // Update the comment text
                            const commentTextElement =
                                commentElement.querySelector(
                                    ".flex-1 > div > div > span:nth-child(2)"
                                );
                            if (commentTextElement) {
                                commentTextElement.textContent = commentText;
                            }

                            // Update the edit button onclick attribute
                            const editButton = commentElement.querySelector(
                                'button[onclick^="editComment"]'
                            );
                            if (editButton) {
                                editButton.setAttribute(
                                    "onclick",
                                    `editComment('${commentId}', '${commentText.replace(
                                        /'/g,
                                        "\\'"
                                    )}')`
                                );
                            }
                        }

                        // Close the modal
                        closeEditModal();
                    }
                })
                .catch((error) => {
                    console.error("Error updating comment:", error);
                });
        });
    }

    // Close modal when clicking outside
    const editCommentModal = document.getElementById("editCommentModal");
    if (editCommentModal) {
        editCommentModal.addEventListener("click", function (e) {
            if (e.target === this) {
                closeEditModal();
            }
        });
    }
});

// Store the current user ID for use in the loadMoreComments function
// This will be set in the blade template
