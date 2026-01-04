import { createContext, useContext, useEffect, useState } from 'react';
import api from '../api/axios';
import type { User } from '../types/User';

interface AuthContextType {
    user: User | null;
    loading: boolean;
    login: (email: string, password: string) => Promise<void>;
    logout: () => Promise<void>;
}

const AuthContext = createContext<AuthContextType>({} as AuthContextType);

export const AuthProvider = ({ children }: { children: React.ReactNode }) => {
    const [user, setUser] = useState<User | null>(null);
    const [loading, setLoading] = useState(true);

    /**
     * Restaurar sesión desde localStorage
     */
    useEffect(() => {
        const token = localStorage.getItem('token');

        if (!token) {
            setLoading(false);
            return;
        }

        api.defaults.headers.common.Authorization = `Bearer ${token}`;

        api.get<User>('/me')
            .then(response => {
                setUser(response.data);
            })
            .catch(() => {
                localStorage.removeItem('token');
                setUser(null);
            })
            .finally(() => {
                setLoading(false);
            });
    }, []);

    /**
     * Login
     */
    const login = async (email: string, password: string) => {
        const { data } = await api.post('/login', { email, password });

        localStorage.setItem('token', data.token);
        api.defaults.headers.common.Authorization = `Bearer ${data.token}`;

        setUser(data.user);
    };

    /**
     * Logout
     */
    const logout = async () => {
        await api.post('/logout');

        localStorage.removeItem('token');
        delete api.defaults.headers.common.Authorization;

        setUser(null);
    };

    return (
        <AuthContext.Provider value={{ user, loading, login, logout }}>
            {children}
        </AuthContext.Provider>
    );
};

export const useAuth = () => useContext(AuthContext);
