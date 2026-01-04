export interface EvaluationItem {
    criterion_id: number;
    criterion_level_id: number;
    comment?: string;
}

export interface Evaluation {
    id: number;
    is_locked: boolean;
    general_comment?: string;
    total_score?: number;
    criterionEvaluations: EvaluationItem[];
}
