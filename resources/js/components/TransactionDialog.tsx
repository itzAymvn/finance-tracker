import { Dialog, DialogContent, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { useModals } from '@/contexts/ModalContext';
import { TransactionForm } from '@/components/TransactionForm';

export function TransactionDialog() {
    const { transactionOpen, editingTransaction, closeTransaction } = useModals();

    return (
        <Dialog open={transactionOpen} onOpenChange={(open) => !open && closeTransaction()}>
            <DialogContent className="max-w-2xl">
                <DialogHeader>
                    <DialogTitle>{editingTransaction ? 'Edit Transaction' : 'New Transaction'}</DialogTitle>
                </DialogHeader>
                <TransactionForm
                    transaction={editingTransaction}
                    onSuccess={closeTransaction}
                />
            </DialogContent>
        </Dialog>
    );
}
