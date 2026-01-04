import { BrowserRouter, Routes, Route, Navigate } from 'react-router-dom';
import { useAuth } from './context/AuthContext';
import LoginPage from './pages/LoginPage';
import ProjectsPage from './pages/ProjectsPage';
import type { JSX } from 'react';
import EvaluationPage from './pages/EvaluationPage';
import { UIProvider } from './context/UiContext';

const ProtectedRoute = ({ children }: { children: JSX.Element }) => {
  const { user, loading } = useAuth();

  if (loading) return <p>Cargando...</p>;
  if (!user) return <Navigate to="/login" />;

  return children;
};

export default function App() {
  return (
    <UIProvider>
      <BrowserRouter>
        <Routes>
          <Route path="/login" element={<LoginPage />} />
          <Route
            path="/"
            element={
              <ProtectedRoute>
                <ProjectsPage />
              </ProtectedRoute>
            }
          />
          <Route
            path="/projects/:projectId/evaluate"
            element={
              <ProtectedRoute>
                <EvaluationPage mode="edit" />
              </ProtectedRoute>
            }
          />
          <Route
            path="/projects/:projectId/evaluation"
            element={
              <ProtectedRoute>
                <EvaluationPage mode="view" />
              </ProtectedRoute>
            }
          />

        </Routes>
      </BrowserRouter>
    </UIProvider>
  );
}
