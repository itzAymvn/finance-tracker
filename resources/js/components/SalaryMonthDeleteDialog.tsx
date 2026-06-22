import { router } from '@inertiajs/react';
import { SalaryMonth } from '@/lib/types';
import { Button } from '@/components/ui/button';
import {
    AlertDialog,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
} from '@/components/ui/alert-dialog';

interface SalaryMonthDeleteDialogProps {
    open: boolean;
    onOpenChange: (open: boolean) => void;
    salaryMonth: SalaryMonth | null;
}

export function SalaryMonthDeleteDialog({ open, onOpenChange, salaryMonth }: SalaryMonthDeleteDialogProps) {
    function handleDelete() {
        if (!salaryMonth) return;
        router.delete(`/salary-months/${salaryMonth.id}`, {
            onSuccess: () => onOpenChange(false),
        });
    }

    return (
        <AlertDialog open={open} onOpenChange={onOpenChange}>
            <AlertDialogContent>
                <AlertDialogHeader>
                    <AlertDialogTitle>Delete Salary Month</AlertDialogTitle>
                    <AlertDialogDescription>
                        Are you sure you want to delete this salary month? Any salary allocations tied to this month will also be removed. This action cannot be undone.
                    </AlertDialogDescription>
                </AlertDialogHeader>
                <AlertDialogFooter>
                    <AlertDialogCancel>Cancel</AlertDialogCancel>
                    <Button variant="destructive" onClick={handleDelete}>
                        Delete
                    </Button>
                </AlertDialogFooter>
            </AlertDialogContent>
        </AlertDialog>
    );
}
