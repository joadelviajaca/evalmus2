<!DOCTYPE html>
<html>
<head>
    <title>Informe de Proyecto</title>
    <style>
        body { font-family: sans-serif; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #0E8B68; padding-bottom: 10px; }
        h1 { color: #0E8B68; margin: 0; }
        .meta { margin-bottom: 20px; color: #555; }
        .score-box { 
            float: right; 
            background: #f3f4f6; 
            padding: 15px; 
            border-radius: 8px; 
            text-align: center;
        }
        .score-val { font-size: 24px; font-weight: bold; color: #0E8B68; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #0E8B68; color: white; }
    </style>
</head>
<body>
    <div class="score-box">
        <div>Nota Final</div>
        <div class="score-val">{{ $project->final_score ?? '-' }}</div>
    </div>

    <div class="header">
        <h1>{{ $project->title }}</h1>
        <p>Informe de Evaluación Erasmus</p>
    </div>

    <div class="meta">
        <strong>Rúbrica utilizada:</strong> {{ $project->rubric->title ?? 'N/A' }}<br>
        <strong>Estado:</strong> {{ $project->state === 'finished' ? 'Finalizado' : 'En proceso' }}<br>
        <strong>Fecha:</strong> {{ date('d/m/Y') }}
    </div>

    <h3>Desglose por Evaluador</h3>
    <table>
        <thead>
            <tr>
                <th>Evaluador</th>
                <th>Fecha Evaluación</th>
                <th>Puntuación</th>
            </tr>
        </thead>
        <tbody>
            @forelse($project->evaluations->where('is_locked', true) as $evaluation)
                <tr>
                    <td>{{ $evaluation->user->name }}</td>
                    <td>{{ $evaluation->updated_at->format('d/m/Y') }}</td>
                    <td>{{ $evaluation->totalScore() }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" style="text-align: center">No hay evaluaciones finalizadas.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    
    @if($project->summary)
        <h3>Resumen del Proyecto</h3>
        <p>{{ $project->summary }}</p>
    @endif
</body>
</html>