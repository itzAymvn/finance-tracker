import { Dialog, DialogContent, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { useModals } from '@/contexts/ModalContext';
import { SalaryMonthForm } from '@/components/SalaryMonthForm';

export function SalaryMonthDialog() {
    const { salaryMonthOpen, editingSalaryMonth, closeSalaryMonth } = useModals();

    return (
        <Dialog open={salaryMonthOpen} onOpenChange={(open) => !open && closeSalaryMonth()}>
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>{editingSalaryMonth ? 'Edit Salary Month' : 'Add Salary Month'}</DialogTitle>
                </DialogHeader>
                <SalaryMonthForm
                    salaryMonth={editingSalaryMonth}
                    onSuccess={closeSalaryMonth}
                />
            </DialogContent>
        </Dialog>
    );
}
