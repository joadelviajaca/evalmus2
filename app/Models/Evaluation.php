<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evaluation extends Model
{
    protected $fillable = ['project_id','rubric_id','user_id','is_locked','general_comment'];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function criterionEvaluations()
    {
        return $this->hasMany(EvaluationItem::class);
    }

    /**
     * Calcula la puntuación total ponderada de esta evaluación.
     * Asume: criterion.weight es porcentaje (ej. 20) y criterion_level.value es valor numérico.
     */
    public function totalScore(): float
    {
        if (!$this->relationLoaded('items')) {
            $this->load(['items.criterion']);
        }

        $criteria = $this->project?->rubric?->criteria;
        if (!$criteria || $criteria->isEmpty()) {
            return 0;
        }

        $weightSum = $criteria->sum('weight');
        if ($weightSum <= 0) {
            return round($this->criterionEvaluations->sum('score'), 2);
        }

        $total = $this->criterionEvaluations->sum(function ($item) use ($weightSum) {
            if (!$item->criterion) {
                return 0;
            }

            return ((float) $item->criterion->weight / $weightSum) * (float) $item->score;
        });

        return round($total, 2);
}

    public function rubric()
    {
        return $this->belongsTo(Rubric::class);
    }
}
