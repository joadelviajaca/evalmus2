<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Project extends Model
{
    protected $fillable = ['title','summary','rubric_id','state','metadata', 'attachment'];

    protected $casts = ['metadata' => 'array'];

    protected $appends = ['attachment_url'];

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

    public function getAttachmentUrlAttribute()
    {
        return $this->attachment ? url(Storage::url($this->attachment)) : null;
    }

    protected function finalScore(): Attribute
    {
        return Attribute::make(
            get: function () {
                // Filtramos solo las evaluaciones cerradas
                $completedEvaluations = $this->evaluations->where('is_locked', true);

                if ($completedEvaluations->isEmpty()) {
                    return null; // O 0, según prefieras
                }

                // Usamos el método totalScore() de cada evaluación y hacemos la media
                return round($completedEvaluations->avg(fn ($ev) => $ev->totalScore()), 2);
            }
        );
    }
}
