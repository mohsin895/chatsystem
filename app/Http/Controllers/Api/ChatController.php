<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Events\MessageSent;


class ChatController extends Controller
{
    public function getUsers()
    {
        $currentUserId = Auth::guard('sanctum')->id();
    
        $users = User::where('role', 2)
            ->withCount([
                'receivedMessages as unread_count' => function ($query) use ($currentUserId) {
                    $query->whereNull('read_at')
                          ->where('receiver_id', $currentUserId);
                }
            ])
            ->orderByDesc('unread_count')
            ->get();
    
        return response()->json($users);
    }
    
    
    // Get messages between admin and specific user
    public function getMessages(User $user)
    {
        $authUser = Auth::guard('sanctum')->user();
    
        $messages = Message::where(function ($query) use ($authUser, $user) {
                $query->where('sender_id', $authUser->id)
                      ->where('receiver_id', $user->id);
            })
            ->orWhere(function ($query) use ($authUser, $user) {
                $query->where('sender_id', $user->id)
                      ->where('receiver_id', $authUser->id);
            })
            ->orderBy('created_at')
            ->get()
            ->map(function ($msg) use ($authUser) {
                return [
                    'id' => $msg->id,
                    'sender_id' => $msg->sender_id,
                    'receiver_id' => $msg->receiver_id,
                    'text' => $msg->text,
                    'read_at' => $msg->read_at,
                    'created_at' => $msg->created_at,
                    'isAdmin' => $msg->sender_id === $authUser->id,
                    'status' => $msg->read_at ? 'read' : 'delivered',
                    'time' => $msg->created_at->format('h:i A'),
                ];
            });
    
        return response()->json($messages);
    }
    

    public function sendMessage(Request $request)
{
    $request->validate([
        'receiver_id' => 'required|exists:users,id',
        'text' => 'required|string|max:1000',
    ]);

    $message = Message::create([
        'sender_id' => Auth::guard('sanctum')->id(),
        'receiver_id' => $request->receiver_id,
        'text' => $request->text,
    ]);
    event(new MessageSent($message));
    return response()->json([
        'id' => $message->id,
        'text' => $message->text,
        'sender_id' => $message->sender_id,
        'receiver_id' => $message->receiver_id,
        'time' => $message->created_at->format('h:i A'),
        'isAdmin' => true,
        'status' => 'delivered',
    ]);
}


public function getUserMessages()
{
    $user = Auth::guard('sanctum')->user();

    // Ensure only role 2 (customer/user) accesses this route
    if ($user->role != 2) {
        return response()->json(['error' => 'Unauthorized'], 403);
    }

    $admin = User::where('role', 1)->first();
    if (!$admin) {
        return response()->json(['error' => 'Admin not found'], 404);
    }

    // Mark admin's unread messages as read
    Message::where('sender_id', $admin->id)
        ->where('receiver_id', $user->id)
        ->whereNull('read_at')
        ->update(['read_at' => now()]);

    // Fetch all messages between this user and the admin
    $messages = Message::where(function ($query) use ($user, $admin) {
            $query->where('sender_id', $user->id)
                  ->where('receiver_id', $admin->id);
        })
        ->orWhere(function ($query) use ($user, $admin) {
            $query->where('sender_id', $admin->id)
                  ->where('receiver_id', $user->id);
        })
        ->orderBy('created_at', 'asc')
        ->get()
        ->map(function ($message) use ($user) {
            return [
                'id' => $message->id,
                'text' => $message->text,
                'timestamp' => $message->created_at->toDateTimeString(),
                'sender' => $message->sender_id === $user->id ? 'user' : 'agent',
            ];
        });

    return response()->json([
        'messages'=>$messages,
        'admin' => $admin,

        ]);
}


    
    // User sends message
    public function sendUserMessage(Request $request)
    {
        $request->validate([
            'text' => 'required|string'
        ]);
      
        $admin = User::where('role', 1)->first();
        $userId = Auth::guard('sanctum')->user()->id;
        $message = Message::create([
            'sender_id' => $userId ,
            'receiver_id' => $admin->id,
            'text' => $request->text
        ]);
        
        event(new MessageSent($message));
        return response()->json($message);
    }
}
