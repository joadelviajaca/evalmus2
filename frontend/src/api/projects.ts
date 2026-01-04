import api from './axios';
import type { Project, ProjectDetail } from '../types/Project';

export const fetchProjects = async (): Promise<Project[]> => {
  const { data } = await api.get<Project[]>('/projects');
  return data;
};

export const fetchProject = async (projectId: number): Promise<ProjectDetail> => {
    const { data } = await api.get(`/projects/${projectId}`);
    return data;
};