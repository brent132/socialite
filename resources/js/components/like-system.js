/**
 * Like System Component
 * Reusable Alpine.js component for post and comment liking functionality
 */
export default function likeSystem(postId, initialLiked, initialCount) {
    return {
        liked: initialLiked,
        likeCount: initialCount,

        toggleLike() {
            fetch(`/p/${postId}/like`, {
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
                this.liked = data.liked;
                this.likeCount = data.count;
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }
    };
}
