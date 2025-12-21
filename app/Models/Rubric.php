<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rubric extends Model
{
    protected $fillable = ['title','description','scale','meta'];

    protected $casts = [
        'meta' => 'array',
    ];

    public function criteria()
    {
        return $this->hasMany(Criterion::class)->orderBy('order');
    }

    public function projects()
    {
        return $this->hasMany(Project::class);
    }
}
