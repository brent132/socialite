<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    /**
     * Show the list of users to chat with
     */
    public function index()
    {
        // Get users the current user is following
        $following = Auth::user()->following;
        
        // Add last message info to each user
        foreach ($following as $user) {
            $lastMessage = Message::where(function($query) use ($user) {
                    $query->where('sender_id', Auth::id())
                          ->where('receiver_id', $user->id);
                })
                ->orWhere(function($query) use ($user) {
                    $query->where('sender_id', $user->id)
                          ->where('receiver_id', Auth::id());
                })
                ->latest()
                ->first();
                
            $user->last_message = $lastMessage ? $lastMessage->message : null;
            $user->last_message_time = $lastMessage ? $lastMessage->created_at : null;
            $user->is_sender = $lastMessage ? ($lastMessage->sender_id === Auth::id()) : false;
        }
        
        return view('chat.index', compact('following'));
    }
    
    /**
     * Show conversation with a specific user
     */
    public function show($userId)
    {
        $user = User::findOrFail($userId);
        
        return view('chat.show', compact('user'));
    }
    
    /**
     * Get messages between current user and another user
     */
    public function getMessages($userId)
    {
        $messages = Message::where(function($query) use ($userId) {
            $query->where('sender_id', auth()->id())
                  ->where('receiver_id', $userId)
                  ->orWhere(function($query) use ($userId) {
                      $query->where('sender_id', $userId)
                            ->where('receiver_id', auth()->id());
                  });
        })
        ->orderBy('created_at', 'desc')
        ->paginate(20); // 20 messages per page

        return response()->json([
            'messages' => $messages->items(),
            'has_more' => $messages->hasMorePages()
        ]);
    }
    
    /**
     * Send a new message
     */
    public function sendMessage(Request $request)
    {
        try {
            $validated = $request->validate([
                'receiver_id' => 'required|exists:users,id',
                'message' => 'required|string|max:1000'
            ]);
            
            $message = Message::create([
                'sender_id' => Auth::id(),
                'receiver_id' => $validated['receiver_id'],
                'message' => $validated['message'],
                'is_read' => false
            ]);
            
            return response()->json([
                'success' => true,
                'message' => $message
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to send message', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Failed to send message: ' . $e->getMessage()
            ], 500);
        }
    }
}


