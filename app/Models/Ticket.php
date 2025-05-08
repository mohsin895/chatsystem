<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
   public function user(){
    return $this->belongsTo(User::class);
   }
   public function comments()
    {
        return $this->hasMany(TicketComment::class)->whereNull('parent_id')->with('replies');
    }
   
}
