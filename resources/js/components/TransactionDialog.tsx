import { Dialog, DialogContent, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { usePage } from '@inertiajs/react';
import { useModals } from '@/contexts/ModalContext';
import { TransactionForm } from '@/components/TransactionForm';
import type { PageProps } from '@/lib/types';

export function TransactionDialog() {
    const { transactionOpen, editingTransaction, closeTransaction } = useModals();
    const { categories } = usePage<PageProps>().props;

    return (
        <Dialog open={transactionOpen} onOpenChange={(open) => !open && closeTransaction()}>
            <DialogContent className="max-w-2xl">
                <DialogHeader>
                    <DialogTitle>{editingTransaction ? 'Edit Transaction' : 'New Transaction'}</DialogTitle>
                </DialogHeader>
                <TransactionForm
                    transaction={editingTransaction}
                    categories={categories}
                    onSuccess={closeTransaction}
                />
                </DialogContent>
            </Dialog>
    );
}
