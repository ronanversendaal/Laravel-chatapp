<?php

namespace App;

use App\Message;
use Illuminate\Database\Eloquent\Model;

class Thread extends Model
{
    protected $fillable = ['subject', 'name', 'chatroom', 'emailaddress'];

    public function messages()
    {
        return $this->hasMany(Message::class);
    }
}
