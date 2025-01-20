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

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    public function type()
    {
        return $this->belongsTo(Type::class);
    }
}
