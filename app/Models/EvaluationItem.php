<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EvaluationItem extends Model
{
    protected $fillable = ['evaluation_id','criterion_id','criterion_level_id','score','comment'];

    public function evaluation()
    {
        return $this->belongsTo(Evaluation::class);
    }

    public function criterion()
    {
        return $this->belongsTo(Criterion::class);
    }

    public function level()
    {
        return $this->belongsTo(CriterionLevel::class, 'criterion_level_id');
    }
}
