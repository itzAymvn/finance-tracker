import { router } from '@inertiajs/react';
import type { Category } from '@/lib/types';
import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
} from '@/components/ui/alert-dialog';

interface CategoryDeleteDialogProps {
    open: boolean;
    onOpenChange: (open: boolean) => void;
    category: Category;
}

export function CategoryDeleteDialog({ open, onOpenChange, category }: CategoryDeleteDialogProps) {
    function handleDelete() {
        router.delete(`/categories/${category.id}`, {
            onSuccess: () => onOpenChange(false),
        });
    }

    const hasTransactions = (category.transaction_count ?? 0) > 0;

    return (
        <AlertDialog open={open} onOpenChange={onOpenChange}>
            <AlertDialogContent>
                <AlertDialogHeader>
                    <AlertDialogTitle>Delete Category</AlertDialogTitle>
                    <AlertDialogDescription>
                        Are you sure you want to delete <strong>{category.name}</strong>?
                        {hasTransactions && (
                            <span className="block mt-1 text-amber-600">
                                This category is assigned to {category.transaction_count} transaction(s).
                                They will become uncategorized.
                            </span>
                        )}
                    </AlertDialogDescription>
                </AlertDialogHeader>
                <AlertDialogFooter>
                    <AlertDialogCancel>Cancel</AlertDialogCancel>
                    <AlertDialogAction onClick={handleDelete}>Delete</AlertDialogAction>
                </AlertDialogFooter>
            </AlertDialogContent>
        </AlertDialog>
    );
}
