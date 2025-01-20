<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'name',
        'description',
    ];

    public function journalists()
    {
        return $this->hasMany(Journalist::class);
    }
}
