import { useState } from 'react';
import { usePage } from '@inertiajs/react';
import { Plus, Pencil, Trash2 } from 'lucide-react';
import type { CategoriesIndexProps, Category } from '@/lib/types';
import { getCategoryIcon } from '@/lib/icons';
import { AppLayout } from '@/components/AppLayout';
import { CategoryForm } from '@/components/CategoryForm';
import { CategoryDeleteDialog } from '@/components/CategoryDeleteDialog';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Table, TableHeader, TableBody, TableRow, TableHead, TableCell } from '@/components/ui/table';

export default function CategoriesIndex() {
    const { categories } = usePage<CategoriesIndexProps>().props;

    const [formOpen, setFormOpen] = useState(false);
    const [editingCategory, setEditingCategory] = useState<Category | null>(null);
    const [deleteOpen, setDeleteOpen] = useState(false);
    const [deletingCategory, setDeletingCategory] = useState<Category | null>(null);

    function openCreate() {
        setEditingCategory(null);
        setFormOpen(true);
    }

    function openEdit(cat: Category) {
        setEditingCategory(cat);
        setFormOpen(true);
    }

    function openDelete(cat: Category) {
        setDeletingCategory(cat);
        setDeleteOpen(true);
    }

    return (
        <>
            <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
                <div>
                    <h1 className="text-2xl font-bold text-foreground">Categories</h1>
                    <p className="text-sm text-muted-foreground mt-0.5">Organize your transactions with categories</p>
                </div>
                <Button onClick={openCreate}>
                    <Plus className="w-4 h-4" />
                    New Category
                </Button>
            </div>

            <div className="bg-card rounded-xl border border-border shadow-sm overflow-hidden">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead className="px-5 py-3.5 text-xs font-semibold tracking-wider uppercase text-muted-foreground bg-muted">Icon</TableHead>
                            <TableHead className="px-5 py-3.5 text-xs font-semibold tracking-wider uppercase text-muted-foreground bg-muted">Name</TableHead>
                            <TableHead className="px-5 py-3.5 text-xs font-semibold tracking-wider uppercase text-muted-foreground bg-muted">Transactions</TableHead>
                            <TableHead className="px-5 py-3.5 text-xs font-semibold tracking-wider uppercase text-muted-foreground bg-muted text-right">Actions</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        {categories.length === 0 ? (
                            <TableRow>
                                <TableCell colSpan={4} className="px-5 py-16 text-center text-sm text-muted-foreground">
                                    No categories yet. Create one to start organizing your transactions.
                                </TableCell>
                            </TableRow>
                        ) : (
                            categories.map((cat) => {
                                const IconComp = getCategoryIcon(cat.icon);
                                return (
                                    <TableRow key={cat.id} className="border-b-border/60 hover:bg-muted/60">
                                        <TableCell className="px-5 py-4">
                                            <div className="w-8 h-8 rounded-lg bg-muted flex items-center justify-center">
                                                <IconComp className="w-4 h-4 text-muted-foreground" />
                                            </div>
                                        </TableCell>
                                        <TableCell className="px-5 py-4 text-sm font-medium text-foreground">
                                            <div className="flex items-center gap-2">
                                                {cat.name}
                                                {cat.is_salary && (
                                                    <span className="badge-emerald">
                                                        <span className="w-1.5 h-1.5 rounded-full bg-emerald mr-1"></span>
                                                        Salary
                                                    </span>
                                                )}
                                            </div>
                                        </TableCell>
                                        <TableCell className="px-5 py-4 text-sm text-muted-foreground font-mono">
                                            {cat.transaction_count ?? 0}
                                        </TableCell>
                                        <TableCell className="px-5 py-4 text-right">
                                            <div className="flex items-center justify-end gap-1">
                                                <Button variant="ghost" size="sm" onClick={() => openEdit(cat)}>
                                                    <Pencil className="w-3.5 h-3.5" />
                                                </Button>
                                                <Button variant="ghost" size="sm" onClick={() => openDelete(cat)}>
                                                    <Trash2 className="w-3.5 h-3.5 text-destructive" />
                                                </Button>
                                            </div>
                                        </TableCell>
                                    </TableRow>
                                );
                            })
                        )}
                    </TableBody>
                </Table>
            </div>

            <Dialog open={formOpen} onOpenChange={setFormOpen}>
                <DialogContent>
                    <DialogHeader>
                        <DialogTitle>{editingCategory ? 'Edit Category' : 'New Category'}</DialogTitle>
                    </DialogHeader>
                    <CategoryForm
                        key={editingCategory?.id ?? 'new'}
                        category={editingCategory}
                        onSuccess={() => setFormOpen(false)}
                    />
                </DialogContent>
            </Dialog>

            {deletingCategory && (
                <CategoryDeleteDialog
                    open={deleteOpen}
                    onOpenChange={setDeleteOpen}
                    category={deletingCategory}
                />
            )}
        </>
    );
}

// eslint-disable-next-line @typescript-eslint/no-explicit-any
(CategoriesIndex as any).layout = (page: React.ReactNode) => <AppLayout>{page}</AppLayout>;
