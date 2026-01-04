<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Evaluation;
use App\Models\EvaluationItem;
use App\Models\CriterionLevel;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EvaluationController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'project_id' => ['required', 'exists:projects,id'],
            'general_comment' => ['nullable', 'string'],
            'items' => ['required', 'array'],
            'items.*.criterion_id' => ['required', 'exists:criteria,id'],
            'items.*.criterion_level_id' => ['required', 'exists:criterion_levels,id'],
            'items.*.comment' => ['nullable', 'string'],
        ]);

        $user = $request->user();
        $project = Project::with('rubric.criteria')->findOrFail($data['project_id']);

        // Comprobar asignación
        if (!$project->evaluators()->where('users.id', $user->id)->exists()) {
            abort(403, 'No autorizado.');
        }

        return DB::transaction(function () use ($data, $user, $project) {

            $evaluation = Evaluation::firstOrCreate(
                [
                    'project_id' => $project->id,
                    'user_id' => $user->id,
                ],
                [
                    'rubric_id' => $project->rubric_id,
                ]
            );

            if ($evaluation->is_locked) {
                abort(403, 'La evaluación está cerrada.');
            }

            $evaluation->update([
                'general_comment' => $data['general_comment'] ?? null,
            ]);

            foreach ($data['items'] as $itemData) {
                $level = CriterionLevel::with('criterion')
                    ->findOrFail($itemData['criterion_level_id']);

                if ($level->criterion_id !== $itemData['criterion_id']) {
                    abort(422, 'Nivel inválido para el criterio');
                }

                if ($level->criterion->rubric_id !== $project->rubric_id) {
                    abort(422, 'El criterio no pertenece a la rúbrica del proyecto');
                }

                EvaluationItem::updateOrCreate(
                    [
                        'evaluation_id' => $evaluation->id,
                        'criterion_id' => $itemData['criterion_id'],
                    ],
                    [
                        'criterion_level_id' => $level->id,
                        'score' => $level->value,
                        'comment' => $itemData['comment'] ?? null,
                    ]
                );
            }

            if ($project->state === 'pending') {
                $project->update(['state' => 'evaluating']);
            }


            return response()->json([
                'message' => 'Evaluación guardada',
                'evaluation_id' => $evaluation->id,
            ]);
        });
    }

    /**
     * Cerrar evaluación
     */
    public function submit(Evaluation $evaluation, Request $request)
    {
        // 1. Verificación de usuario
        if ($evaluation->user_id !== $request->user()->id) {
            abort(403);
        }

        // 2. Verificación si ya está cerrada
        if ($evaluation->is_locked) {
            abort(403, 'La evaluación ya está cerrada.');
        }

        // 3. Verificación de evaluación completa  
        // Cargamos la relación del proyecto y la rúbrica para contar los criterios
        $evaluation->load('project.rubric.criteria');
        
        // Contamos cuántos criterios tiene la rúbrica
        $totalCriteria = $evaluation->project->rubric->criteria->count();
        
        // Contamos cuántas evaluaciones de criterios ha guardado el usuario
        // Asumo que tu relación en el modelo Evaluation se llama 'criterionEvaluations' 
        // (basado en tu método show)
        $evaluatedCount = $evaluation->criterionEvaluations()->count();

        if ($evaluatedCount < $totalCriteria) {
            return response()->json([
                'message' => "No puedes finalizar. Has evaluado $evaluatedCount de $totalCriteria criterios.",
            ], 422); 
        }   

        return DB::transaction(function () use ($evaluation) {
            $evaluation->update(['is_locked' => true]);
            
            // Obtenemos el proyecto
            $project = $evaluation->project;

            // --- NUEVA LÓGICA DE ESTADO ---
            // Contamos cuántos evaluadores tiene asignados el proyecto
            $totalEvaluators = $project->evaluators()->count();

            // Contamos cuántas evaluaciones CERRADAS existen para este proyecto
            $completedEvaluations = $project->evaluations()
                ->where('is_locked', true)
                ->count();

            // Si todos los evaluadores han cerrado su evaluación, el proyecto finaliza
            if ($completedEvaluations >= $totalEvaluators) {
                $project->update(['state' => 'finished']);
            }
            // ------------------------------

            return response()->json([
                'message' => 'Evaluación enviada correctamente',
                'total_score' => $evaluation->totalScore(),
            ]);
        });
    }

    /**
     * Obtener evaluación existente de un proyecto
     */
    public function show(Project $project, Request $request)
    {
        $user = $request->user();

        $evaluation = $project->evaluations()
            ->where('user_id', $user->id)
            ->with([
                'criterionEvaluations:id,evaluation_id,criterion_id,criterion_level_id,comment,score',
            ])
            ->first();

        if (!$evaluation) {
            return response()->json(null);
        }

        return response()->json([
            'id' => $evaluation->id,
            'is_locked' => $evaluation->is_locked,
            'general_comment' => $evaluation->general_comment,
            'total_score' => $evaluation->totalScore(),
            'criterionEvaluations' => $evaluation->criterionEvaluations,
        ]);
    }
}
