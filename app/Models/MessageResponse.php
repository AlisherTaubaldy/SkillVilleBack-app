<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessageResponse extends Model
{
    use HasFactory;

    protected $fillable =
        [
            'id',
            'chat_id',
            'receiver_id',
            'message',
            'sent_by_user'
        ];
}
