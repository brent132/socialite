@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <div class="max-w-4xl mx-auto">
        <!-- Chat container with header and content -->
        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-xl overflow-hidden transition-colors duration-200">
            <!-- Chat header with user info and back button -->
            <div class="p-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between bg-white dark:bg-gray-800 sticky top-0 z-10 transition-colors duration-200">
                <div class="flex items-center space-x-3">
                    <a href="{{ route('chat.list') }}" class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                        </svg>
                    </a>
                    <div class="flex items-center space-x-3">
                        <img src="{{ $user->profile->profileImage() }}" alt="{{ $user->name }}" class="h-10 w-10 rounded-full object-cover border-2 border-gray-200 dark:border-gray-700">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $user->name }}</h2>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ '@' . $user->username }}</p>
                        </div>
                    </div>
                </div>
                <div class="text-xs px-2 py-1 bg-green-100 text-green-800 rounded-full">Online</div>
            </div>

            <!-- Chat messages area -->
            <div id="chat-box" class="h-[calc(70vh-200px)] min-h-[300px] overflow-y-auto p-4 bg-gray-50 dark:bg-gray-900 transition-colors duration-200"></div>

            <!-- Message input form -->
            <div class="p-4 border-t border-gray-100 dark:border-gray-700 bg-white dark:bg-gray-800 transition-colors duration-200">
                <form id="chat-form" class="flex gap-2">
                    <input type="hidden" id="receiver_id" value="{{ $user->id }}">
                    <div class="flex-1 relative">
                        <textarea
                            id="message"
                            rows="1"
                            class="block w-full px-4 py-2 border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 dark:focus:ring-indigo-400 dark:focus:border-indigo-400 resize-none h-[40px] transition-colors duration-200"
                            placeholder="Type a message..."
                            style="overflow-y: hidden"></textarea>
                        <div id="message-hidden" class="invisible absolute top-0 left-0 px-4 py-2 border border-transparent w-full break-words"></div>
                    </div>
                    <button
                        type="submit"
                        id="send-button"
                        class="shrink-0 flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors h-[40px] min-w-[80px]">
                        <span>Send</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z" />
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    const authUserId = {
        {
            Auth::id()
        }
    };
    const receiverId = document.getElementById('receiver_id').value;
    const chatBox = document.getElementById('chat-box');
    const sendButton = document.getElementById('send-button');
    const messageInput = document.getElementById('message');
    const messageHidden = document.getElementById('message-hidden');

    // Auto-resize textarea based on content
    function autoResize() {
        // Copy the text to the hidden div to measure its height
        messageHidden.textContent = messageInput.value + '\n';

        // Set the textarea height to match the hidden div
        messageInput.style.height = 'auto';
        messageInput.style.height = Math.max(40, messageHidden.scrollHeight) + 'px';
    }

    // Listen for input events to resize the textarea
    messageInput.addEventListener('input', autoResize);
    messageInput.addEventListener('change', autoResize);
    messageInput.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendButton.click();
        }
        if (e.key === 'Enter' && e.shiftKey) {
            setTimeout(autoResize, 0);
        }
    });

    // Format timestamp
    function formatTimestamp(dateString) {
        const date = new Date(dateString);
        return date.toLocaleTimeString([], {
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    function loadMessages() {
        fetch(`/chat/messages/${receiverId}`, {
                headers: {
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(messages => {
                let html = '';
                let previousDate = null;

                messages.forEach(msg => {
                    const messageDate = new Date(msg.created_at).toLocaleDateString();

                    // Add date separator if this is a new day
                    if (previousDate !== messageDate) {
                        html += `<div class="flex justify-center my-4">
                            <span class="text-xs text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 px-3 py-1 rounded-full transition-colors duration-200">${messageDate}</span>
                        </div>`;
                        previousDate = messageDate;
                    }

                    // Determine if message is from current user or the other person
                    const isCurrentUser = msg.sender_id == authUserId;

                    html += `
                    <div class="${isCurrentUser ? 'flex justify-end' : 'flex justify-start'} mb-4">
                        <div class="${isCurrentUser ? 'bg-indigo-500 text-white' : 'bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 dark:text-gray-200'} rounded-lg px-4 py-2 max-w-[75%] shadow-sm transition-colors duration-200">
                            <p class="text-sm">${msg.message}</p>
                            <p class="text-xs ${isCurrentUser ? 'text-indigo-100' : 'text-gray-500 dark:text-gray-400'} text-right mt-1">${formatTimestamp(msg.created_at)}</p>
                        </div>
                    </div>`;
                });

                chatBox.innerHTML = html;
                chatBox.scrollTop = chatBox.scrollHeight; // Auto-scroll to the bottom
            })
            .catch(error => {
                console.error('Error loading messages:', error);
                // Show a user-friendly error message
                chatBox.innerHTML = `<div class="flex justify-center my-4">
                    <span class="text-xs text-red-500 bg-red-50 dark:bg-red-900/30 px-3 py-1 rounded-full">Failed to load messages. Please try again.</span>
                </div>`;
            });
    }

    document.getElementById('chat-form').addEventListener('submit', function(e) {
        e.preventDefault();
        let message = messageInput.value.trim();

        if (!message) return;

        // Disable button and show loading state
        sendButton.disabled = true;
        sendButton.innerHTML = '<span class="inline-block animate-pulse">Sending...</span>';

        fetch('/chat/send', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    message,
                    receiver_id: receiverId
                })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                messageInput.value = '';
                messageInput.style.height = 'auto'; // Reset height
                loadMessages(); // Fetch messages immediately after sending
            })
            .catch(error => {
                console.error('Error sending message:', error);
                // Show a user-friendly error message in the chat box
                const errorHtml = `<div class="flex justify-center my-2">
                    <span class="text-xs text-red-500 bg-red-50 px-3 py-1 rounded-full">Failed to send message. Please try again.</span>
                </div>`;
                chatBox.innerHTML += errorHtml;
                chatBox.scrollTop = chatBox.scrollHeight;
            })
            .finally(() => {
                // Re-enable button and restore original text
                sendButton.disabled = false;
                sendButton.innerHTML = '<span>Send</span><svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" viewBox="0 0 20 20" fill="currentColor"><path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z" /></svg>';
            });
    });

    // Fetch messages every 5 seconds (polling)
    setInterval(loadMessages, 5000);

    // Load messages when page loads
    loadMessages();

    // Focus the message input when the page loads
    messageInput.focus();

    // Initialize the textarea height
    autoResize();
</script>
@endsection