<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'message',
        'todo_id',
        'user_id',
    ];

    function todo()
    {
        return $this->belongsTo(Todo::class);
    }

    function user()
    {
        return $this->belongsTo(User::class);
    }
}
