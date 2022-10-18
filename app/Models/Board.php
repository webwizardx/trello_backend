<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Board extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'workspace_id'
    ];

    function workspace()
    {
        return $this->belongsTo(Workspace::class);
    }

    function users()
    {
        return $this->belongsToMany(User::class)->as('members');
    }

    function lists()
    {
        return $this->hasMany(Lists::class);
    }
}
