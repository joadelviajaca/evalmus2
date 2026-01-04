import type { Rubric } from "./Rubric";

export interface Project {
    id: number;
    title: string;
    state: 'pending' | 'evaluating' | 'finished';
    rubric: Rubric;
    evaluation?: {
        id: number;
        is_locked: boolean;
        total_score: number | null;
    } | null;
}

export interface CriterionLevel {
    id: number;
    label: string;
    value: number;
}

export interface Criterion {
    id: number;
    title: string;
    weight: number;
    levels: CriterionLevel[];
}

export interface ProjectDetail {
    id: number;
    title: string;
    rubric: {
        id: number;
        title: string;
        criteria: Criterion[];
    };
}