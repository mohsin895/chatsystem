<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    public function __construct(Message $message)
    {
        $this->message = $message;
        
        // Eager load the sender to avoid N+1 issues in broadcastWith()
        $this->message->load('sender');
    }

    public function broadcastOn(): array
    {
        // Send to both the specific user's channel and the admin channel
        return [
            new PrivateChannel("chat.user.{$this->message->receiver_id}"),
            new PrivateChannel("chat.admin"), // Always broadcast to admin channel
        ];
    }

    public function broadcastAs(): string
    {
        return 'MessageSent';
    }

    public function broadcastWith(): array
    {
        $isAdmin = $this->message->sender->role === 1;
        
        return [
            'id' => $this->message->id,
            'text' => $this->message->text,
            'sender_id' => $this->message->sender_id,
            'receiver_id' => $this->message->receiver_id,
            'time' => $this->message->created_at->format('h:i A'),
            'is_admin' => $isAdmin,
            'status' => $this->message->read_at ? 'read' : 'delivered',
            'timestamp' => $this->message->created_at->toDateTimeString(),
            'sender' => $isAdmin ? 'agent' : 'user',
        ];
    }
}