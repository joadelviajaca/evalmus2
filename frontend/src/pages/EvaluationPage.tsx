import { useEffect, useState, useMemo } from 'react';
import { useParams, useNavigate } from 'react-router-dom';
import { useUI } from '../context/UiContext';
import { fetchProject } from '../api/projects';
import {
    fetchEvaluation,
    saveEvaluation,
    submitEvaluation,
} from '../api/evaluations'; // Asegúrate que la ruta es correcta
import type { ProjectDetail, Criterion } from '../types/Project';

// Definimos la estructura del estado local de la evaluación
type EvaluationState = Record<
    number,
    {
        level_id: number;
        comment: string;
    }
>;

interface EvaluationPageProps {
    mode: 'edit' | 'view';
}

export default function EvaluationPage({ mode }: EvaluationPageProps) {
    const { projectId } = useParams();
    const navigate = useNavigate();

    // Estados de datos
    const [project, setProject] = useState<ProjectDetail | null>(null);
    const [evaluationId, setEvaluationId] = useState<number | null>(null);
    const [totalScore, setTotalScore] = useState<number | null>(null);

    // Estados del formulario
    const [evalState, setEvalState] = useState<EvaluationState>({});
    const [generalComment, setGeneralComment] = useState('');

    // Estados de UI
    const [loading, setLoading] = useState(true);
    const [saving, setSaving] = useState(false);
    const [submitting, setSubmitting] = useState(false);

    // Notificaciones
    const { showToast, confirm } = useUI();

    const isViewMode = mode === 'view';

    useEffect(() => {
        if (!projectId) return;

        const loadData = async () => {
            try {
                const [projectData, evaluationData] = await Promise.all([
                    fetchProject(Number(projectId)),
                    fetchEvaluation(Number(projectId)),
                ]);
                console.log("Datos del proyecto:", projectData);
                console.log("Datos de la evaluación:", evaluationData);
                setProject(projectData);

                if (evaluationData) {
                    setEvaluationId(evaluationData.id);
                    setGeneralComment(evaluationData.general_comment ?? '');
                    setTotalScore(evaluationData.total_score ?? null);

                    // Mapear la respuesta de la API al estado local
                    const mappedState: EvaluationState = {};
                    evaluationData.criterionEvaluations?.forEach((item) => {
                        mappedState[item.criterion_id] = {
                            level_id: item.criterion_level_id,
                            comment: item.comment || '',
                        };
                    });
                    setEvalState(mappedState);
                }
                else {
                    setEvalState({}); // Nuevo, todo vacío
                }
            } catch (error) {
                console.error("Error cargando datos", error);
                alert("Error cargando los datos del proyecto");
                navigate('/');
            } finally {
                setLoading(false);
            }
        };

        loadData();
    }, [projectId, navigate]);

    // Helpers para actualizar el estado
    const handleLevelSelect = (criterionId: number, levelId: number) => {
        if (isViewMode) return;
        setEvalState((prev) => ({
            ...prev,
            [criterionId]: {
                ...prev[criterionId],
                level_id: levelId,
                comment: prev[criterionId]?.comment || '',
            },
        }));
    };

    const handleCommentChange = (criterionId: number, comment: string) => {
        if (isViewMode) return;
        setEvalState((prev) => ({
            ...prev,
            [criterionId]: {
                level_id: prev[criterionId]?.level_id || 0, // 0 indica no seleccionado
                comment: comment,
            },
        }));
    };

    // Calcular progreso
    const progress = useMemo(() => {
        if (!project) return { current: 0, total: 0 };
        const total = project.rubric.criteria.length;
        const current = Object.values(evalState).filter(v => v.level_id > 0).length;
        return { current, total, percent: Math.round((current / total) * 100) };
    }, [project, evalState]);

    // Helper para obtener los datos actuales del formulario
    const getPayload = () => {
        if (!project) return null;
        return {
            project_id: project.id,
            general_comment: generalComment,
            items: Object.entries(evalState)
                .filter(([, val]) => val.level_id > 0)
                .map(([criterionId, val]) => ({
                    criterion_id: Number(criterionId),
                    criterion_level_id: val.level_id,
                    comment: val.comment,
                })),
        };
    };

    const handleSave = async (silent = false) => {
        // Añadimos parámetro 'silent' por si lo llamamos desde submit sin querer mostrar toast
        const payload = getPayload();
        if (!payload) return null;

        if (!silent) setSaving(true);

        try {
            const response = await saveEvaluation(payload);
            if (response.evaluation_id) {
                setEvaluationId(response.evaluation_id);
            }
            if (!silent) showToast('Borrador guardado correctamente', 'success');
            return response.evaluation_id; // Devolvemos el ID
        } catch (error) {
            console.error(error);
            if (!silent) showToast('Error al guardar el borrador', 'error');
            throw error;
        } finally {
            if (!silent) setSaving(false);
        }
    };

    const handleSubmit = async () => {
        // 1. Validaciones visuales (Progreso)
        if (progress.current < progress.total) {
            showToast(`Faltan criterios por evaluar (${progress.current}/${progress.total}).`, 'error');
            return;
        }

        // 2. Confirmación al usuario
        const isConfirmed = await confirm({
            title: 'Finalizar Evaluación',
            message: 'Se guardarán los cambios actuales y se finalizará la evaluación. El proyecto pasará a estado completado y no podrás editar más. ¿Continuar?',
            confirmText: 'Sí, finalizar',
            cancelText: 'Revisar',
            type: 'info'
        });

        if (!isConfirmed) return;

        setSubmitting(true);
        try {
            // 3. PASO CRÍTICO: GUARDAR PRIMERO
            // Usamos la función handleSave en modo silencioso (true) o normal
            // Pero necesitamos asegurarnos de que tenemos el ID actualizado
            let currentEvalId = evaluationId;

            // Siempre guardamos antes de enviar para asegurar que la DB tiene lo último
            const payload = getPayload();
            if (payload) {
                const saveResponse = await saveEvaluation(payload);
                currentEvalId = saveResponse.evaluation_id;
            }

            if (!currentEvalId) throw new Error("No hay ID de evaluación");

            // 4. AHORA SÍ, ENVIAMOS (BLOQUEAMOS)
            await submitEvaluation(currentEvalId);

            showToast('Evaluación finalizada con éxito', 'success');
            navigate('/');

        } catch (error) {
            console.error(error);
            showToast('Ocurrió un error al procesar la evaluación', 'error');
        } finally {
            setSubmitting(false);
        }
    };

    if (loading || !project) {
        return (
            <div className="min-h-screen flex items-center justify-center bg-gray-50">
                <div className="animate-pulse flex flex-col items-center">
                    <div className="h-4 w-32 bg-gray-200 rounded mb-4"></div>
                    <p className="text-gray-500">Cargando evaluación...</p>
                </div>
            </div>
        );
    }

    return (
        <div className="min-h-screen bg-gray-100 pb-20">
            {/* Header Sticky */}
            <header className="bg-white shadow sticky top-0 z-10">
                <div className="max-w-5xl mx-auto px-4 py-4 flex justify-between items-center">
                    <div>
                        <h1 className="text-xl font-bold text-gray-800">{project.title}</h1>
                        <p className="text-sm text-gray-500">Rúbrica: {project.rubric.title}</p>
                    </div>

                    {/* Indicador de progreso */}
                    {!isViewMode && (
                        <div className="text-right hidden sm:block">
                            <span className="text-sm font-medium text-gray-600">
                                Progreso: {progress.current} / {progress.total}
                            </span>
                            <div className="w-32 h-2 bg-gray-200 rounded-full mt-1 overflow-hidden">
                                <div
                                    className="h-full bg-primary transition-all duration-500"
                                    style={{ width: `${progress.percent}%` }}
                                ></div>
                            </div>
                        </div>
                    )}

                    {isViewMode && totalScore !== null && (
                        <div className="bg-emerald-100 text-emerald-800 px-4 py-2 rounded-lg font-bold text-xl">
                            {totalScore} pts
                        </div>
                    )}
                </div>
            </header>

            <main className="max-w-5xl mx-auto px-4 py-8 space-y-8">

                {/* Lista de Criterios */}
                <div className="space-y-6">
                    {project.rubric.criteria.map((criterion) => (
                        <CriterionCard
                            key={criterion.id}
                            criterion={criterion}
                            selectedLevelId={evalState[criterion.id]?.level_id}
                            comment={evalState[criterion.id]?.comment || ''}
                            onSelect={(levelId) => handleLevelSelect(criterion.id, levelId)}
                            onCommentChange={(text) => handleCommentChange(criterion.id, text)}
                            readOnly={isViewMode}
                        />
                    ))}
                </div>

                {/* Comentario General */}
                <div className="bg-white rounded-xl shadow p-6">
                    <h3 className="text-lg font-semibold mb-4 text-gray-800">Comentarios Generales y Feedback</h3>
                    <textarea
                        disabled={isViewMode}
                        value={generalComment}
                        onChange={(e) => setGeneralComment(e.target.value)}
                        placeholder="Escribe aquí una conclusión general del proyecto..."
                        className="w-full border-gray-300 rounded-lg shadow-sm focus:border-primary focus:ring-primary min-h-[120px] p-3 border"
                    />
                </div>
            </main>

            {/* Footer de Acciones (Solo modo edición) */}
            {!isViewMode && (
                <div className="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 p-4 shadow-lg z-20">
                    <div className="max-w-5xl mx-auto flex justify-end gap-4">
                        <button
                            onClick={() => navigate('/')}
                            className="px-6 py-2 rounded-lg text-gray-600 hover:bg-gray-100 font-medium transition"
                        >
                            Cancelar
                        </button>

                        <button
                            onClick={handleSave}
                            disabled={saving || submitting}
                            className="px-6 py-2 rounded-lg bg-white border-2 border-primary text-primary font-medium hover:bg-primary/5 transition disabled:opacity-50"
                        >
                            {saving ? 'Guardando...' : 'Guardar Borrador'}
                        </button>

                        <button
                            onClick={handleSubmit}
                            disabled={saving || submitting || !evaluationId}
                            className={`px-6 py-2 rounded-lg text-white font-medium transition shadow-md ${!evaluationId
                                ? 'bg-gray-400 cursor-not-allowed'
                                : 'bg-primary hover:bg-primary/90'
                                }`}
                        >
                            {submitting ? 'Enviando...' : 'Finalizar y Enviar'}
                        </button>
                    </div>
                </div>
            )}
        </div>
    );
}

// Subcomponente para renderizar cada criterio (Mejora la legibilidad)
const CriterionCard = ({
    criterion,
    selectedLevelId,
    comment,
    onSelect,
    onCommentChange,
    readOnly
}: {
    criterion: Criterion;
    selectedLevelId?: number;
    comment: string;
    onSelect: (id: number) => void;
    onCommentChange: (val: string) => void;
    readOnly: boolean;
}) => {
    return (
        <div className={`bg-white rounded-xl shadow overflow-hidden border-l-4 ${selectedLevelId ? 'border-primary' : 'border-gray-300'}`}>
            <div className="p-6">
                <div className="flex justify-between items-start mb-4">
                    <h2 className="text-lg font-bold text-gray-800 w-3/4">{criterion.title}</h2>
                    <span className="bg-gray-100 text-gray-600 text-xs font-bold px-2 py-1 rounded">
                        Peso: {criterion.weight}%
                    </span>
                </div>

                {/* Grid de Niveles */}
                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                    {criterion.levels.map((level) => {
                        const isSelected = selectedLevelId === level.id;
                        return (
                            <button
                                key={level.id}
                                disabled={readOnly}
                                onClick={() => onSelect(level.id)}
                                className={`
                                    relative p-4 rounded-lg border text-left transition-all duration-200
                                    ${isSelected
                                        ? 'border-primary bg-primary/5 ring-1 ring-primary'
                                        : 'border-gray-200 hover:border-primary/50 bg-gray-50'}
                                    ${readOnly ? 'cursor-default' : 'cursor-pointer'}
                                `}
                            >
                                <div className="flex justify-between items-center mb-2">
                                    <span className={`font-bold text-lg ${isSelected ? 'text-primary' : 'text-gray-700'}`}>
                                        {level.value} pts
                                    </span>
                                    {isSelected && (
                                        <div className="w-3 h-3 bg-primary rounded-full"></div>
                                    )}
                                </div>
                                <p className="text-sm text-gray-600 leading-snug">
                                    {level.label}
                                </p>
                            </button>
                        );
                    })}
                </div>

                {/* Área de comentario específica */}
                <div>
                    <label className="block text-sm font-medium text-gray-700 mb-1">
                        Observaciones para este criterio
                    </label>
                    <textarea
                        value={comment}
                        disabled={readOnly}
                        onChange={(e) => onCommentChange(e.target.value)}
                        className="w-full text-sm border-gray-300 rounded-md bg-gray-50 focus:bg-white focus:border-primary focus:ring-primary transition p-2 border"
                        rows={2}
                        placeholder="Añadir comentario específico..."
                    />
                </div>
            </div>
        </div>
    );
};