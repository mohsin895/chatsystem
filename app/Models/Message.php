<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Message extends Model
{
    protected $fillable = ['sender_id', 'receiver_id', 'text', 'read_at'];

    public function admin(){
        return $this->belongsTo(User::class,Auth::guard('sanctum')->user()->id,'sender_id');
    }
    // In App\Models\Message
public function sender()
{
    return $this->belongsTo(User::class, 'sender_id');
}

public function receiver()
{
    return $this->belongsTo(User::class, 'receiver_id');
}
}
