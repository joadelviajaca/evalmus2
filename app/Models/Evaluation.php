<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evaluation extends Model
{
    protected $fillable = ['project_id','user_id','is_locked','general_comment'];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(EvaluationItem::class);
    }

    /**
     * Calcula la puntuación total ponderada de esta evaluación.
     * Asume: criterion.weight es porcentaje (ej. 20) y criterion_level.value es valor numérico.
     */
    public function totalScore()
    {
        $total = 0;
        $weightSum = $this->project && $this->project->rubric
            ? $this->project->rubric->criteria->sum('weight')
            : 0;

        foreach ($this->items()->with('criterion')->get() as $item) {
            if (!$item->criterion) continue;
            $weight = (float) $item->criterion->weight;
            $score = (float) $item->score; // ya guardado como value del nivel
            if ($weightSum > 0) {
                // normalizamos: cada criterio contribuye (weight/weightSum) * score
                $total += ($weight / $weightSum) * $score;
            } else {
                $total += $score;
            }
        }

        return round($total, 2);
    }
}
