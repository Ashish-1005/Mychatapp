<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chatm extends Model
{
    use HasFactory;
    protected $fillable = [
        'body',
        'from_id',
        'to_id',
        'file_path',
        'file_type',
    ];
}
