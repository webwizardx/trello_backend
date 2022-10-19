<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Todo extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'list_id'
    ];

    function list()
    {
        return $this->belongsTo(Lists::class, 'list_id');
    }

    function users()
    {
        return $this->belongsToMany(User::class);
    }
}
