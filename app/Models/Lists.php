<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lists extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'board_id'
    ];

    function board()
    {
        return $this->belongsTo(Board::class);
    }
}
