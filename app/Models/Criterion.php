<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Criterion extends Model
{
    protected $fillable = ['rubric_id','title','description','weight','order'];

    public function rubric()
    {
        return $this->belongsTo(Rubric::class);
    }

    public function levels()
    {
        return $this->hasMany(CriterionLevel::class)->orderBy('order');
    }
}
