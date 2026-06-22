import { Dialog, DialogContent, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { useModals } from '@/contexts/ModalContext';
import { SalaryPeriodForm } from '@/components/SalaryPeriodForm';

export function SalaryPeriodDialog() {
    const { salaryPeriodOpen, closeSalaryPeriod } = useModals();

    return (
        <Dialog open={salaryPeriodOpen} onOpenChange={(open) => !open && closeSalaryPeriod()}>
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Create Salary Period</DialogTitle>
                </DialogHeader>
                <SalaryPeriodForm onSuccess={closeSalaryPeriod} />
            </DialogContent>
        </Dialog>
    );
}
