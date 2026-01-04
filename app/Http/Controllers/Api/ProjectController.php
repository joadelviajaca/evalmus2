<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $projects = Project::query()
            ->whereHas('evaluators', fn ($q) => $q->where('users.id', $user->id))
            ->with([
                'rubric:id,title',
                'evaluations' => fn ($q) =>
                    $q->where('user_id', $user->id)
                    ->with(['criterionEvaluations.criterion', 'project.rubric.criteria']),
            ])
            ->get()
            ->map(function ($project) {
                $evaluation = $project->evaluations->first();

                return [
                    'id' => $project->id,
                    'title' => $project->title,
                    'state' => $project->state,
                    'rubric' => [
                        'id' => $project->rubric->id,
                        'title' => $project->rubric->title,
                    ],
                    'evaluation' => $evaluation
                        ? [
                            'id' => $evaluation->id,
                            'is_locked' => $evaluation->is_locked,
                            'total_score' => $evaluation->is_locked
                                ? $evaluation->totalScore()
                                : null,
                        ]
                        : null,
                ];
            });

        return response()->json($projects);
    }



    public function show(Project $project, Request $request)
    {
        $user = $request->user();

        // Comprobar asignación
        if (!$project->evaluators()->where('users.id', $user->id)->exists()) {
            abort(403, 'No tienes acceso a este proyecto.');
        }

        $project->load([
        'rubric.criteria' => function ($query) {
            // Cambia 'position' por la columna que uses para ordenar criterios.
            // Si no tienes, usa 'id'.
            $query->orderBy('order', 'asc'); 
        },
        'rubric.criteria.levels' => function ($query) {
            // Ordenamos los niveles por valor (puntuación) ascendente (0, 25, 50...)
            $query->orderBy('value', 'asc'); 
        },
    ]);

        return response()->json($project);
    }

   
}
