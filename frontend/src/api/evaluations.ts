import api from './axios';
import type { Evaluation } from '../types/Evaluation';

export const fetchEvaluation = async (
    projectId: number
): Promise<Evaluation | null> => {
    // Si devuelve 200 pero vacío, manejamos null.
    // Asumo que tu backend devuelve null o 204 si no existe.
    // Si tu backend devuelve 404, habría que capturar el error.
    try {
        const { data } = await api.get(`/evaluations/project/${projectId}`);
        return data || null;
    } catch (error) {
        return null;
    }
};

export const saveEvaluation = async (payload: {
    project_id: number;
    general_comment?: string;
    items: {
        criterion_id: number;
        criterion_level_id: number;
        comment?: string;
    }[];
}) => {
    const { data } = await api.post('/evaluations', payload);
    return data; // Devuelve { message, evaluation_id }
};

export const submitEvaluation = async (evaluationId: number) => {
    const { data } = await api.post(`/evaluations/${evaluationId}/submit`);
    return data;
};