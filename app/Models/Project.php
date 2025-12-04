<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = ['title','summary','rubric_id','state','metadata'];

    protected $casts = ['metadata' => 'array'];

    public function rubric()
    {
        return $this->belongsTo(Rubric::class);
    }

    public function evaluators()
    {
        return $this->belongsToMany(User::class)->withTimestamps(); // pivot project_user
    }

    public function evaluations()
    {
        return $this->hasMany(Evaluation::class);
    }
}
