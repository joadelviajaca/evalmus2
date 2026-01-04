import { useEffect, useState } from 'react';
import { useAuth } from '../context/AuthContext';
import { useNavigate } from 'react-router-dom';

export default function LoginPage() {
    const { login } = useAuth();
    const navigate = useNavigate();
    const [email, setEmail] = useState('');
    const [password, setPassword] = useState('');
    const [error, setError] = useState('');
    const { user } = useAuth();

    useEffect(() => {
        if (user) {
            navigate('/');
        }
    }, [user]);


    const submit = async (e: React.FormEvent) => {
        e.preventDefault();
        setError('');

        try {
            await login(email, password);
            navigate('/');
        } catch {
            setError('Credenciales incorrectas o acceso no permitido');
        }
    };

    return (
        <div className="min-h-screen flex items-center justify-center bg-gray-100">
            <div className="w-full max-w-md bg-white rounded-xl shadow-lg p-8 space-y-6">

                <img
                    src="/images/logo.svg"
                    alt="Logo"
                    className="h-16 mx-auto"
                />

                <h1 className="text-center text-2xl font-bold text-primary">
                    Acceso evaluadores
                </h1>

                {error && (
                    <p className="text-sm text-red-600 text-center">
                        {error}
                    </p>
                )}

                <form onSubmit={submit} className="space-y-4">
                    <div>
                        <label className="block text-sm font-medium text-gray-700">
                            Email
                        </label>
                        <input
                            type="email"
                            value={email}
                            onChange={e => setEmail(e.target.value)}
                            className="mt-1 w-full rounded-lg border border-gray-300 bg-gray-100 focus:border-primary focus:ring-primary"
                            required
                        />
                    </div>

                    <div>
                        <label className="block text-sm font-medium text-gray-700">
                            Contraseña
                        </label>
                        <input
                            type="password"
                            value={password}
                            onChange={e => setPassword(e.target.value)}
                            className="mt-1 w-full rounded-lg border border-gray-300 bg-gray-100 focus:border-primary focus:ring-primary"
                            required
                        />
                    </div>

                    <button
                        type="submit"
                        className="w-full rounded-lg bg-primary py-2 text-white font-medium hover:bg-primary/90 transition"
                    >
                        Entrar
                    </button>
                </form>
            </div>
        </div>
    );
}
