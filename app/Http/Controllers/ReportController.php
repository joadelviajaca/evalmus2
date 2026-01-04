<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function download(Project $project)
    {
        // Cargamos relaciones para no hacer N+1 queries en la vista
        $project->load(['evaluations.user', 'rubric.criteria']);

        // Generamos el PDF usando una vista Blade
        $pdf = Pdf::loadView('reports.project', [
            'project' => $project
        ]);

        // Opción A: Descargar directamente
        return $pdf->download("Informe_Proyecto_{$project->id}.pdf");
        
        // Opción B: Ver en el navegador (útil para depurar el diseño)
        // return $pdf->stream(); 
    }
}