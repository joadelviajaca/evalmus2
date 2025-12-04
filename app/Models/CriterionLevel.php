<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CriterionLevel extends Model
{
    protected $fillable = ['criterion_id','label','description','value','order'];

    public function criterion()
    {
        return $this->belongsTo(Criterion::class);
    }
}
