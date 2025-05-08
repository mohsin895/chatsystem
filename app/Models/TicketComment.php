<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketComment extends Model
{
    protected $fillable = ['comment','ticket_id','user_id','parent_id'];

    public function replies()
{
    return $this->hasMany(TicketComment::class, 'parent_id')->with('user', 'replies'); // if recursive
}

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
}
