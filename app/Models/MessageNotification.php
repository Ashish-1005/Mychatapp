<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class MessageNotification extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id'

    ];
    protected $table = 'messagenotification';

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
