<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Journalist extends Model
{
    protected $fillable = [
        'user_id',
        'folder',
        'file',
        'sheet',
        'ccaa',
        'geographical_scope',
        'category',
        'name',
        'contact',
        'position',
        'phone',
        'type',
        'email',
        'status',
        'notes',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
