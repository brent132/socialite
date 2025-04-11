@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <div class="max-w-4xl mx-auto">
        <!-- Chat container with header and content -->
        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-xl overflow-hidden transition-colors duration-200">
            <!-- Chat header with user info and back button -->
            <div class="p-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between bg-white dark:bg-gray-800 sticky top-0 z-10 transition-colors duration-200">
                <div class="flex items-center space-x-3">
                    <a href="{{ route('chat.index') }}" class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-colors">
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
            <div id="messages-container" class="h-[calc(70vh-200px)] min-h-[300px] overflow-y-auto p-4 bg-gray-50 dark:bg-gray-900 transition-colors duration-200">
                <!-- Messages will be loaded here -->
                <button 
                    id="load-more-button" 
                    class="w-full text-center text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 mb-4 hidden">
                    Load older messages
                </button>
                <div id="messages-content"></div>
            </div>

            <!-- Message input form -->
            <div class="p-4 border-t border-gray-100 dark:border-gray-700 bg-white dark:bg-gray-800 transition-colors duration-200">
                <form id="message-form" class="flex gap-2">
                    <input type="hidden" id="receiver-id" value="{{ $user->id }}">
                    <div class="flex-1 relative">
                        <textarea
                            id="message-input"
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
    document.addEventListener('DOMContentLoaded', function() {
        const messagesContainer = document.getElementById('messages-container');
        const messagesContent = document.getElementById('messages-content');
        const loadMoreButton = document.getElementById('load-more-button');
        const messageForm = document.getElementById('message-form');
        const messageInput = document.getElementById('message-input');
        const messageHidden = document.getElementById('message-hidden');
        const sendButton = document.getElementById('send-button');
        const receiverId = document.getElementById('receiver-id').value;
        const currentUserId = {{ Auth::id() }};
        let page = 1;
        let loading = false;
        
        // Auto-resize textarea
        function autoResize() {
            messageHidden.textContent = messageInput.value + '\n';
            messageInput.style.height = 'auto';
            messageInput.style.height = Math.max(40, messageHidden.scrollHeight) + 'px';
        }
        
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
        function formatTime(dateString) {
            const date = new Date(dateString);
            return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        }
        
        // Format date for message groups
        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString();
        }
        
        // Load messages function with pagination
        function loadMessages(loadMore = false) {
            if (loading) return;
            loading = true;
            
            if (!loadMore) {
                page = 1;
            }
            
            fetch(`/api/chat/messages/${receiverId}?page=${page}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Failed to load messages');
                    }
                    return response.json();
                })
                .then(data => {
                    let html = '';
                    let previousDate = null;
                    
                    if (data.messages.length === 0 && page === 1) {
                        html = `<div class="flex justify-center my-4">
                            <span class="text-xs text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 px-3 py-1 rounded-full">No messages yet. Start a conversation!</span>
                        </div>`;
                    } else {
                        data.messages.forEach(message => {
                            const messageDate = formatDate(message.created_at);
                            
                            if (previousDate !== messageDate) {
                                html += `<div class="flex justify-center my-4">
                                    <span class="text-xs text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 px-3 py-1 rounded-full">${messageDate}</span>
                                </div>`;
                                previousDate = messageDate;
                            }
                            
                            const isCurrentUser = message.sender_id == currentUserId;
                            html += `
                            <div class="${isCurrentUser ? 'flex justify-end' : 'flex justify-start'} mb-4">
                                <div class="${isCurrentUser ? 'bg-indigo-500 text-white' : 'bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 dark:text-gray-200'} rounded-lg px-4 py-2 max-w-[75%] shadow-sm">
                                    <p class="text-sm">${message.message}</p>
                                    <p class="text-xs ${isCurrentUser ? 'text-indigo-100' : 'text-gray-500 dark:text-gray-400'} text-right mt-1">${formatTime(message.created_at)}</p>
                                </div>
                            </div>`;
                        });
                    }
                    
                    if (loadMore) {
                        messagesContent.insertAdjacentHTML('afterbegin', html);
                    } else {
                        messagesContent.innerHTML = html;
                    }
                    
                    // Show/hide load more button based on if there are more messages
                    loadMoreButton.style.display = data.has_more ? 'block' : 'none';
                    
                    if (!loadMore) {
                        messagesContainer.scrollTop = messagesContainer.scrollHeight;
                    }
                })
                .catch(error => {
                    console.error('Error loading messages:', error);
                    messagesContent.innerHTML = `<div class="flex justify-center my-4">
                        <span class="text-xs text-red-500 bg-red-50 dark:bg-red-900/30 px-3 py-1 rounded-full">Failed to load messages. Please refresh the page.</span>
                    </div>`;
                })
                .finally(() => {
                    loading = false;
                });
        }
        
        // Send message
        messageForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const message = messageInput.value.trim();
            if (!message) return;
            
            sendButton.disabled = true;
            sendButton.innerHTML = '<span class="inline-block animate-pulse">Sending...</span>';
            
            fetch('/api/chat/send', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({
                    receiver_id: receiverId,
                    message: message
                })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Failed to send message');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    messageInput.value = '';
                    messageInput.style.height = '40px';
                    loadMessages();
                } else {
                    throw new Error(data.error || 'Failed to send message');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                const errorHtml = `<div class="flex justify-center my-2">
                    <span class="text-xs text-red-500 bg-red-50 px-3 py-1 rounded-full">Failed to send message. Please try again.</span>
                </div>`;
                messagesContainer.innerHTML += errorHtml;
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
            })
            .finally(() => {
                sendButton.disabled = false;
                sendButton.innerHTML = `<span>Send</span>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z" />
                </svg>`;
            });
        });
        
        // Load more button click handler
        loadMoreButton.addEventListener('click', () => {
            page++;
            loadMessages(true);
        });
        
        // Initial load
        loadMessages();
        
        // Refresh messages every 10 seconds (only the latest messages)
        setInterval(() => loadMessages(), 10000);
        
        // Focus message input
        messageInput.focus();
    });
</script>
@endsection


