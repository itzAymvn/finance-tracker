import { useEffect, useState } from 'react';
import { router } from '@inertiajs/react';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { z } from 'zod';
import { Transaction } from '@/lib/types';
import { formatMoney, formatDate } from '@/lib/format';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Checkbox } from '@/components/ui/checkbox';
import {
    Form,
    FormControl,
    FormField,
    FormItem,
    FormLabel,
    FormMessage,
} from '@/components/ui/form';
import { TransactionDeleteDialog } from '@/components/TransactionDeleteDialog';

const createSchema = z.object({
    amount: z.number().positive('Amount must be greater than 0'),
    amount_sign: z.union([z.literal(1), z.literal(-1)]),
    paid_at: z.string().min(1, 'Date is required'),
    value_date: z.string().optional(),
    label: z.string().min(1, 'Label is required').max(255),
    is_salary: z.boolean(),
});

const editSchema = z.object({
    label: z.string().min(1, 'Label is required').max(255),
    is_salary: z.boolean(),
});

type CreateValues = z.infer<typeof createSchema>;
type EditValues = z.infer<typeof editSchema>;

interface TransactionFormProps {
    transaction?: Transaction | null;
    onSuccess?: () => void;
}

export function TransactionForm({ transaction, onSuccess }: TransactionFormProps) {
    const isEdit = !!transaction;
    const [deleteOpen, setDeleteOpen] = useState(false);

    const createForm = useForm<CreateValues>({
        resolver: zodResolver(createSchema),
        defaultValues: {
            amount: undefined as unknown as number,
            amount_sign: 1,
            paid_at: new Date().toISOString().split('T')[0],
            value_date: '',
            label: '',
            is_salary: false,
        },
    });

    const editForm = useForm<EditValues>({
        resolver: zodResolver(editSchema),
        defaultValues: {
            label: transaction?.label ?? '',
            is_salary: transaction?.is_salary ?? false,
        },
    });

    const amountSign = createForm.watch('amount_sign');

    useEffect(() => {
        if (amountSign === -1) {
            createForm.setValue('is_salary', false);
        }
    }, [amountSign, createForm]);

    function handleCreateError(errors: Record<string, string>) {
        Object.entries(errors).forEach(([key, message]) => {
            createForm.setError(key as keyof CreateValues, { message });
        });
    }

    function handleEditError(errors: Record<string, string>) {
        Object.entries(errors).forEach(([key, message]) => {
            editForm.setError(key as keyof EditValues, { message });
        });
    }

    function onCreateSubmit(data: CreateValues) {
        router.post('/transactions', data, {
            onSuccess: () => onSuccess?.(),
            onError: handleCreateError,
        });
    }

    function onEditSubmit(data: EditValues) {
        if (!transaction) return;
        router.patch(`/transactions/${transaction.id}`, data, {
            onSuccess: () => onSuccess?.(),
            onError: handleEditError,
        });
    }

    if (isEdit && transaction) {
        const amountNum = parseFloat(transaction.amount);
        const credit = amountNum >= 0;
        const hasAllocations = transaction.allocations && transaction.allocations.length > 0;

        return (
            <>
                <div className="space-y-0">
                    <div className="grid grid-cols-2 sm:grid-cols-4 gap-y-3 gap-x-4 pb-4 border-b border-border">
                        <div>
                            <p className="text-[10px] font-semibold tracking-wider uppercase text-muted-foreground mb-0.5">Date</p>
                            <p className="text-sm font-medium text-foreground">{formatDate(transaction.paid_at)}</p>
                        </div>
                        <div>
                            <p className="text-[10px] font-semibold tracking-wider uppercase text-muted-foreground mb-0.5">Source</p>
                            <p className="text-xs uppercase tracking-wider font-medium text-muted-foreground">{transaction.source}</p>
                        </div>
                        <div>
                            <p className="text-[10px] font-semibold tracking-wider uppercase text-muted-foreground mb-0.5">Amount</p>
                            <p className={`font-mono text-sm font-semibold ${credit ? 'text-emerald-600' : 'text-red-600'}`}>
                                {credit ? '+' : ''}{formatMoney(transaction.amount)}
                            </p>
                        </div>
                        <div>
                            <p className="text-[10px] font-semibold tracking-wider uppercase text-muted-foreground mb-0.5">Type</p>
                            <p className={`text-xs uppercase tracking-wider font-medium ${credit ? 'text-emerald-600' : 'text-red-600'}`}>
                                {credit ? 'Credit' : 'Debit'}
                            </p>
                        </div>
                        <div className="col-span-2 sm:col-span-4">
                            <p className="text-[10px] font-semibold tracking-wider uppercase text-muted-foreground mb-0.5">Label</p>
                            <p className="text-sm font-medium text-foreground leading-snug">{transaction.label}</p>
                        </div>
                    </div>

                    {transaction.is_salary && hasAllocations && (
                        <div className="py-3 border-b border-border">
                            <p className="text-[10px] font-semibold tracking-wider uppercase text-emerald-600 mb-2">Allocated FIFO across months</p>
                            <div className="space-y-1">
                                {transaction.allocations!.map((a) => (
                                    <div key={a.id} className="flex items-center justify-between text-sm">
                                        <span className="flex items-baseline gap-2">
                                            <span className="font-mono text-foreground">{a.salary_month?.month_key}</span>
                                            <span className="text-muted-foreground text-xs">{a.salary_month?.label}</span>
                                        </span>
                                        <span className="font-mono text-emerald-600 font-medium">{formatMoney(a.amount)}</span>
                                    </div>
                                ))}
                                {transaction.unallocated != null && transaction.unallocated > 0.005 && (
                                    <div className="flex items-center justify-between text-sm pt-1.5 mt-1.5 border-t border-emerald/20">
                                        <span className="text-amber-600 text-xs">Unallocated (no eligible month capacity)</span>
                                        <span className="font-mono text-amber-600 font-medium">{formatMoney(transaction.unallocated)}</span>
                                    </div>
                                )}
                            </div>
                        </div>
                    )}

                    <Form {...editForm}>
                        <form onSubmit={editForm.handleSubmit(onEditSubmit)} className="pt-3 space-y-4">
                            <FormField
                                control={editForm.control}
                                name="label"
                                render={({ field }) => (
                                    <FormItem>
                                        <FormLabel>Label</FormLabel>
                                        <FormControl>
                                            <Input {...field} />
                                        </FormControl>
                                        <FormMessage />
                                    </FormItem>
                                )}
                            />

                            {credit && (
                                <FormField
                                    control={editForm.control}
                                    name="is_salary"
                                    render={({ field }) => (
                                        <FormItem>
                                            <label className="flex items-start gap-3 cursor-pointer p-4 rounded-lg border border-border dark:border-border bg-muted/50 dark:bg-muted/50 hover:bg-muted dark:hover:bg-muted transition-colors">
                                                <FormControl>
                                                    <Checkbox
                                                        checked={field.value}
                                                        onCheckedChange={field.onChange}
                                                        className="mt-0.5"
                                                    />
                                                </FormControl>
                                                <div>
                                                    <span className="block text-sm font-medium text-foreground dark:text-white">This is a salary credit</span>
                                                    <span className="block text-xs text-muted-foreground dark:text-slate-400 mt-0.5">
                                                        Turning this off removes all allocations. Turning it on splits the credit FIFO across eligible salary months on save.
                                                    </span>
                                                </div>
                                            </label>
                                        </FormItem>
                                    )}
                                />
                            )}

                            {!credit && (
                                <p className="text-sm text-muted-foreground dark:text-slate-400 italic">This is a debit transaction. Salary tagging only applies to credits.</p>
                            )}

                            <div className="flex items-center justify-between gap-3 pt-4 border-t border-border dark:border-border">
                                <Button
                                    type="button"
                                    variant="destructive"
                                    onClick={() => setDeleteOpen(true)}
                                >
                                    Delete
                                </Button>
                                <Button type="submit" className="">
                                    Save Changes
                                </Button>
                            </div>
                        </form>
                    </Form>
                </div>

                <TransactionDeleteDialog
                    open={deleteOpen}
                    onOpenChange={setDeleteOpen}
                    transaction={transaction}
                />
            </>
        );
    }

    return (
        <Form {...createForm}>
            <form onSubmit={createForm.handleSubmit(onCreateSubmit)} className="space-y-4">
                <div>
                    <label className="block text-xs font-semibold tracking-wider uppercase text-muted-foreground dark:text-slate-400 mb-2">Type</label>
                    <div className="grid grid-cols-2 gap-3">
                        <button
                            type="button"
                            onClick={() => createForm.setValue('amount_sign', 1)}
                            className={`flex items-center justify-center gap-2 px-4 py-3 rounded-lg border text-sm font-medium transition-all ${
                                amountSign === 1
                                    ? 'border-emerald/30 bg-emerald-lt/40 text-emerald-600 dark:bg-emerald-dark-bg/40 dark:text-emerald-600-300'
                                    : 'border-border dark:border-border bg-white dark:bg-card text-muted-foreground dark:text-slate-400 hover:bg-muted dark:hover:bg-muted'
                            }`}
                        >
                            <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 4v16m8-8H4" /></svg>
                            Credit (money in)
                        </button>
                        <button
                            type="button"
                            onClick={() => createForm.setValue('amount_sign', -1)}
                            className={`flex items-center justify-center gap-2 px-4 py-3 rounded-lg border text-sm font-medium transition-all ${
                                amountSign === -1
                                    ? 'border-ruby/30 bg-red-500-lt/40 text-red-600 dark:bg-red-500-dark-bg/40 dark:text-red-300'
                                    : 'border-border dark:border-border bg-white dark:bg-card text-muted-foreground dark:text-slate-400 hover:bg-muted dark:hover:bg-muted'
                            }`}
                        >
                            <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M20 12H4" /></svg>
                            Debit (money out)
                        </button>
                    </div>
                    <input type="hidden" {...createForm.register('amount_sign', { valueAsNumber: true })} />
                </div>

                <FormField
                    control={createForm.control}
                    name="amount"
                    render={({ field }) => (
                        <FormItem>
                            <FormLabel>
                                Amount <span className="text-primary">*</span>
                            </FormLabel>
                            <FormControl>
                                <div className="relative">
                                    <Input
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        placeholder="0.00"
                                        className="pl-12 py-2.5 text-lg font-mono"
                                        {...field}
                                        value={field.value ?? ''}
                                    />
                                    <span className={`absolute left-3 top-1/2 -translate-y-1/2 text-sm font-mono ${amountSign === 1 ? 'text-emerald-600' : 'text-red-600'}`}>
                                        {amountSign === 1 ? '+' : '\u2212'}
                                    </span>
                                </div>
                            </FormControl>
                            <FormMessage />
                        </FormItem>
                    )}
                />

                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <FormField
                        control={createForm.control}
                        name="paid_at"
                        render={({ field }) => (
                            <FormItem>
                                <FormLabel>
                                    Date <span className="text-primary">*</span>
                                </FormLabel>
                                <FormControl>
                                    <Input type="date" className="py-2.5 font-mono" {...field} />
                                </FormControl>
                                <FormMessage />
                            </FormItem>
                        )}
                    />
                    <FormField
                        control={createForm.control}
                        name="value_date"
                        render={({ field }) => (
                            <FormItem>
                                <FormLabel>
                                    Value date <span className="text-muted-foreground dark:text-slate-400 normal-case font-normal">(optional)</span>
                                </FormLabel>
                                <FormControl>
                                    <Input type="date" className="py-2.5 font-mono" {...field} />
                                </FormControl>
                                <FormMessage />
                            </FormItem>
                        )}
                    />
                </div>

                <FormField
                    control={createForm.control}
                    name="label"
                    render={({ field }) => (
                        <FormItem>
                            <FormLabel>
                                Label <span className="text-primary">*</span>
                            </FormLabel>
                            <FormControl>
                                <Input
                                    placeholder="e.g. VIREMENT RECU DE ... / PAIEMENT CARTE ..."
                                    className="py-2.5"
                                    {...field}
                                />
                            </FormControl>
                            <FormMessage />
                        </FormItem>
                    )}
                />

                <FormField
                    control={createForm.control}
                    name="is_salary"
                    render={({ field }) => (
                        <FormItem>
                            <label className={`flex items-start gap-3 cursor-pointer p-4 rounded-lg border border-border dark:border-border bg-muted/50 dark:bg-muted/50 hover:bg-muted dark:hover:bg-muted transition-colors ${amountSign === -1 ? 'opacity-50 pointer-events-none' : ''}`}>
                                <FormControl>
                                    <Checkbox
                                        checked={field.value}
                                        onCheckedChange={field.onChange}
                                        disabled={amountSign === -1}
                                        className="mt-0.5"
                                    />
                                </FormControl>
                                <div>
                                    <span className="block text-sm font-medium text-foreground dark:text-white">This is a salary credit</span>
                                    <span className="block text-xs text-muted-foreground dark:text-slate-400 mt-0.5">
                                        Tag as salary &mdash; the credit will be split FIFO across eligible salary months on save (applies only when type = Credit).
                                    </span>
                                </div>
                            </label>
                        </FormItem>
                    )}
                />

                <div className="flex items-center justify-end gap-3 pt-2">
                    <Button type="button" variant="outline" onClick={() => onSuccess?.()}>
                        Cancel
                    </Button>
                    <Button type="submit" className="">
                        Create Transaction
                    </Button>
                </div>
            </form>
        </Form>
    );
}
