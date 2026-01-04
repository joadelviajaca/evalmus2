import { createContext, useContext, useState, useCallback, type ReactNode } from 'react';

// --- Tipos ---
type ToastType = 'success' | 'error' | 'info';

interface Toast {
    id: number;
    type: ToastType;
    message: string;
}

interface ConfirmOptions {
    title: string;
    message: string;
    confirmText?: string;
    cancelText?: string;
    type?: 'danger' | 'info';
}

interface UIContextType {
    showToast: (message: string, type?: ToastType) => void;
    confirm: (options: ConfirmOptions) => Promise<boolean>;
}

const UIContext = createContext<UIContextType>({} as UIContextType);

// --- Provider ---
export const UIProvider = ({ children }: { children: ReactNode }) => {
    // Estado para Toasts
    const [toasts, setToasts] = useState<Toast[]>([]);
    
    // Estado para Modal de Confirmación
    const [confirmState, setConfirmState] = useState<{
        isOpen: boolean;
        options: ConfirmOptions;
        resolve: (value: boolean) => void;
    } | null>(null);

    // -- Lógica Toasts --
    const showToast = useCallback((message: string, type: ToastType = 'success') => {
        const id = Date.now();
        setToasts(prev => [...prev, { id, type, message }]);

        // Auto eliminar a los 3 segundos
        setTimeout(() => {
            setToasts(prev => prev.filter(t => t.id !== id));
        }, 3000);
    }, []);

    const removeToast = (id: number) => {
        setToasts(prev => prev.filter(t => t.id !== id));
    };

    // -- Lógica Confirmación (Promise based) --
    const confirm = useCallback((options: ConfirmOptions): Promise<boolean> => {
        return new Promise((resolve) => {
            setConfirmState({
                isOpen: true,
                options,
                resolve,
            });
        });
    }, []);

    const handleConfirmClose = (result: boolean) => {
        if (confirmState) {
            confirmState.resolve(result);
            setConfirmState(null);
        }
    };

    return (
        <UIContext.Provider value={{ showToast, confirm }}>
            {children}
            
            {/* Renderizado de Toasts */}
            <div className="fixed top-5 right-5 z-50 flex flex-col gap-2">
                {toasts.map(toast => (
                    <div 
                        key={toast.id}
                        onClick={() => removeToast(toast.id)}
                        className={`
                            min-w-[300px] p-4 rounded-lg shadow-lg cursor-pointer transition-all animate-fade-in-left
                            flex items-center gap-3 text-white
                            ${toast.type === 'success' ? 'bg-emerald-600' : ''}
                            ${toast.type === 'error' ? 'bg-red-600' : ''}
                            ${toast.type === 'info' ? 'bg-blue-600' : ''}
                        `}
                    >
                        {/* Iconos simples SVG */}
                        {toast.type === 'success' && <CheckIcon className="w-6 h-6" />}
                        {toast.type === 'error' && <XCircleIcon className="w-6 h-6" />}
                        <p className="font-medium text-sm">{toast.message}</p>
                    </div>
                ))}
            </div>

            {/* Renderizado de Modal Confirmación */}
            {confirmState && (
                <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
                    <div className="bg-white rounded-xl shadow-2xl max-w-md w-full p-6 animate-scale-up">
                        <h3 className="text-xl font-bold text-gray-900 mb-2">
                            {confirmState.options.title}
                        </h3>
                        <p className="text-gray-600 mb-6">
                            {confirmState.options.message}
                        </p>
                        <div className="flex justify-end gap-3">
                            <button
                                onClick={() => handleConfirmClose(false)}
                                className="px-4 py-2 rounded-lg text-gray-700 hover:bg-gray-100 transition"
                            >
                                {confirmState.options.cancelText || 'Cancelar'}
                            </button>
                            <button
                                onClick={() => handleConfirmClose(true)}
                                className={`px-4 py-2 rounded-lg text-white font-medium transition
                                    ${confirmState.options.type === 'danger' 
                                        ? 'bg-red-600 hover:bg-red-700' 
                                        : 'bg-primary hover:bg-primary/90'
                                    }
                                `}
                            >
                                {confirmState.options.confirmText || 'Confirmar'}
                            </button>
                        </div>
                    </div>
                </div>
            )}
        </UIContext.Provider>
    );
};

export const useUI = () => useContext(UIContext);

// --- Iconos Inline para no depender de librerías ---
const CheckIcon = (props: any) => (
    <svg fill="none" viewBox="0 0 24 24" strokeWidth={2} stroke="currentColor" {...props}>
        <path strokeLinecap="round" strokeLinejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
    </svg>
);

const XCircleIcon = (props: any) => (
    <svg fill="none" viewBox="0 0 24 24" strokeWidth={2} stroke="currentColor" {...props}>
        <path strokeLinecap="round" strokeLinejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
    </svg>
);