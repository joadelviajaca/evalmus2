import { useEffect, useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { fetchProjects } from '../api/projects';
import type { Project } from '../types/Project';
import { useAuth } from '../context/AuthContext';

export default function ProjectsPage() {
    const navigate = useNavigate();
    const { logout } = useAuth();

    const [projects, setProjects] = useState<Project[]>([]);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        fetchProjects()
            .then(setProjects)
            .catch((error) => {
                console.error("Error fetching projects", error);
                alert("Error cargando los proyectos");
            })
            .finally(() => setLoading(false));
    }, []);

    if (loading) {
        return (
            <div className="min-h-screen flex items-center justify-center">
                <p className="text-gray-600">Cargando proyectos…</p>
            </div>
        );
    }
 
    return (
        <div className="min-h-screen bg-gray-100 p-6">
            <div className="max-w-5xl mx-auto space-y-6">

                {/* Header */}
                <div className="flex justify-between items-center">
                    <h1 className="text-2xl font-bold text-primary">
                        Proyectos asignados
                    </h1>

                    <button
                        onClick={logout}
                        className="text-sm text-gray-600 hover:text-primary transition"
                    >
                        Cerrar sesión
                    </button>
                </div>

                {/* Lista */}
                {projects.length === 0 ? (
                    <p className="text-gray-600">
                        No tienes proyectos asignados.
                    </p>
                ) : (
                    <div className="grid gap-4 md:grid-cols-2">
                        {projects.map(project => {
                            const hasEvaluation = !!project.evaluation;
                            const isLocked = project.evaluation?.is_locked;

                            return (
                                <div
                                    key={project.id}
                                    className="bg-white rounded-xl shadow p-5 space-y-3 flex flex-col justify-between"
                                >
                                    {/* Info principal */}
                                    <div className="space-y-2">
                                        <h2 className="text-lg font-semibold">
                                            {project.title}
                                        </h2>

                                        <p className="text-sm text-gray-600">
                                            Rúbrica:{' '}
                                            <span className="font-medium">
                                                {project.rubric.title}
                                            </span>
                                        </p>

                                        {/* Estado */}
                                        <span
                                            className={`inline-block w-fit rounded-full px-3 py-1 text-xs font-medium ${project.state === 'finished'
                                                    ? 'bg-green-100 text-green-700'
                                                    : project.state === 'evaluating'
                                                        ? 'bg-yellow-100 text-yellow-700'
                                                        : 'bg-gray-200 text-gray-700'
                                                }`}
                                        >
                                            {project.state === 'pending' && 'Pendiente'}
                                            {project.state === 'evaluating' && 'En evaluación'}
                                            {project.state === 'finished' && 'Finalizado'}
                                        </span>
                                    </div>

                                    {/* Acciones */}
                                    <div className="pt-3 flex justify-between items-center">
                                        <button
                                            onClick={() => {
                                                // Solo vamos a modo vista si existe Y está bloqueada.
                                                // Si existe pero es borrador (isLocked == false), vamos a editar.
                                                if (hasEvaluation && isLocked) {
                                                    navigate(`/projects/${project.id}/evaluation`);
                                                } else {
                                                    navigate(`/projects/${project.id}/evaluate`);
                                                }
                                            }}
                                            className={`text-sm font-medium transition ${isLocked
                                                    ? 'text-gray-500 hover:text-gray-700'
                                                    : 'text-primary hover:underline'
                                                }`}
                                        >
                                            {isLocked 
                                                ? 'Ver resultados →'
                                                : hasEvaluation
                                                    ? 'Continuar evaluación →' // Si hay borrador
                                                    : 'Evaluar proyecto →'     // Si es nueva
                                            }
                                        </button>

                                        {/* Puntuación */}
                                        {isLocked && project.evaluation?.total_score !== undefined && (
                                            <span className="text-sm font-semibold text-primary">
                                                {project.evaluation.total_score} pts
                                            </span>
                                        )}
                                    </div>
                                </div>
                            );
                        })}
                    </div>
                )}
            </div>
        </div>
    );
}
