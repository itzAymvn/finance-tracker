import { createContext, useContext, useState, useCallback, type ReactNode } from 'react';
import type { Transaction, SalaryMonth } from '@/lib/types';

interface ModalState {
    transactionOpen: boolean;
    editingTransaction: Transaction | null;
    salaryMonthOpen: boolean;
    editingSalaryMonth: SalaryMonth | null;
    salaryPeriodOpen: boolean;
    deleteTransactionOpen: boolean;
    deletingTransaction: Transaction | null;
    deleteSalaryMonthOpen: boolean;
    deletingSalaryMonth: SalaryMonth | null;
}

interface ModalContextValue extends ModalState {
    openTransaction: (tx?: Transaction | null) => void;
    closeTransaction: () => void;
    openSalaryMonth: (m?: SalaryMonth | null) => void;
    closeSalaryMonth: () => void;
    openSalaryPeriod: () => void;
    closeSalaryPeriod: () => void;
    openDeleteTransaction: (tx: Transaction) => void;
    closeDeleteTransaction: () => void;
    openDeleteSalaryMonth: (m: SalaryMonth) => void;
    closeDeleteSalaryMonth: () => void;
}

const initialState: ModalState = {
    transactionOpen: false,
    editingTransaction: null,
    salaryMonthOpen: false,
    editingSalaryMonth: null,
    salaryPeriodOpen: false,
    deleteTransactionOpen: false,
    deletingTransaction: null,
    deleteSalaryMonthOpen: false,
    deletingSalaryMonth: null,
};

const ModalContext = createContext<ModalContextValue>({
    ...initialState,
    openTransaction: () => {},
    closeTransaction: () => {},
    openSalaryMonth: () => {},
    closeSalaryMonth: () => {},
    openSalaryPeriod: () => {},
    closeSalaryPeriod: () => {},
    openDeleteTransaction: () => {},
    closeDeleteTransaction: () => {},
    openDeleteSalaryMonth: () => {},
    closeDeleteSalaryMonth: () => {},
});

export function ModalProvider({ children }: { children: ReactNode }) {
    const [state, setState] = useState<ModalState>(initialState);

    const openTransaction = useCallback((tx?: Transaction | null) => {
        setState((s) => ({ ...s, transactionOpen: true, editingTransaction: tx ?? null }));
    }, []);

    const closeTransaction = useCallback(() => {
        setState((s) => ({ ...s, transactionOpen: false, editingTransaction: null }));
    }, []);

    const openSalaryMonth = useCallback((m?: SalaryMonth | null) => {
        setState((s) => ({ ...s, salaryMonthOpen: true, editingSalaryMonth: m ?? null }));
    }, []);

    const closeSalaryMonth = useCallback(() => {
        setState((s) => ({ ...s, salaryMonthOpen: false, editingSalaryMonth: null }));
    }, []);

    const openSalaryPeriod = useCallback(() => {
        setState((s) => ({ ...s, salaryPeriodOpen: true }));
    }, []);

    const closeSalaryPeriod = useCallback(() => {
        setState((s) => ({ ...s, salaryPeriodOpen: false }));
    }, []);

    const openDeleteTransaction = useCallback((tx: Transaction) => {
        setState((s) => ({ ...s, deleteTransactionOpen: true, deletingTransaction: tx }));
    }, []);

    const closeDeleteTransaction = useCallback(() => {
        setState((s) => ({ ...s, deleteTransactionOpen: false, deletingTransaction: null }));
    }, []);

    const openDeleteSalaryMonth = useCallback((m: SalaryMonth) => {
        setState((s) => ({ ...s, deleteSalaryMonthOpen: true, deletingSalaryMonth: m }));
    }, []);

    const closeDeleteSalaryMonth = useCallback(() => {
        setState((s) => ({ ...s, deleteSalaryMonthOpen: false, deletingSalaryMonth: null }));
    }, []);

    return (
        <ModalContext.Provider
            value={{
                ...state,
                openTransaction,
                closeTransaction,
                openSalaryMonth,
                closeSalaryMonth,
                openSalaryPeriod,
                closeSalaryPeriod,
                openDeleteTransaction,
                closeDeleteTransaction,
                openDeleteSalaryMonth,
                closeDeleteSalaryMonth,
            }}
        >
            {children}
        </ModalContext.Provider>
    );
}

export function useModals() {
    return useContext(ModalContext);
}
