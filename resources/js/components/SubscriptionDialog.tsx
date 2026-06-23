import { useEffect } from 'react';
import { router, usePage } from '@inertiajs/react';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { z } from 'zod';
import { useModals } from '@/contexts/ModalContext';
import { getCategoryIcon } from '@/lib/icons';
import type { PageProps, SubscriptionFrequency } from '@/lib/types';
import { Dialog, DialogContent, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import {
    Form,
    FormControl,
    FormField,
    FormItem,
    FormLabel,
    FormMessage,
} from '@/components/ui/form';

const schema = z.object({
    label: z.string().min(1, 'Label is required').max(255),
    amount: z.coerce.number().positive('Amount must be greater than 0'),
    amount_sign: z.union([z.literal(1), z.literal(-1)]),
    frequency: z.enum(['weekly', 'biweekly', 'monthly', 'quarterly', 'yearly']),
    start_at: z.string().min(1, 'Start date is required'),
    category_id: z.coerce.number().nullable().optional(),
});

type FormValues = z.infer<typeof schema>;

const frequencyLabels: Record<SubscriptionFrequency, string> = {
    weekly: 'Weekly',
    biweekly: 'Biweekly',
    monthly: 'Monthly',
    quarterly: 'Quarterly',
    yearly: 'Yearly',
};

function toLocalDatetime(iso: string): string {
    // Parse the ISO string directly as naive datetime to avoid UTC conversion.
    // Handles formats like "2027-02-02T09:00:00.000000Z" or "2027-02-02T09:00:00+01:00"
    const match = iso.match(/^(\d{4}-\d{2}-\d{2})T(\d{2}:\d{2})/);
    if (match) {
        return `${match[1]}T${match[2]}`;
    }
    // Fallback: try to parse with Date (may cause timezone shift)
    const d = new Date(iso);
    const pad = (n: number) => String(n).padStart(2, '0');
    return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}T${pad(d.getHours())}:${pad(d.getMinutes())}`;
}

function getDefaultStartAt(): string {
    const d = new Date();
    const pad = (n: number) => String(n).padStart(2, '0');
    return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}T${pad(d.getHours())}:${pad(d.getMinutes())}`;
}

export function SubscriptionDialog() {
    const { subscriptionOpen, editingSubscription, closeSubscription } = useModals();
    const { categories } = usePage<PageProps>().props;

    const form = useForm<FormValues>({
        resolver: zodResolver(schema),
        defaultValues: {
            label: '',
            amount: undefined as unknown as number,
            amount_sign: -1,
            frequency: 'monthly',
            start_at: getDefaultStartAt(),
            category_id: null,
        },
    });

    const amountSign = form.watch('amount_sign');

    useEffect(() => {
        if (subscriptionOpen) {
            if (editingSubscription) {
                const amountNum = Math.abs(parseFloat(editingSubscription.amount));
                form.reset({
                    label: editingSubscription.label,
                    amount: amountNum,
                    amount_sign: parseFloat(editingSubscription.amount) >= 0 ? 1 : -1,
                    frequency: editingSubscription.frequency,
                    start_at: toLocalDatetime(editingSubscription.start_at),
                    category_id: editingSubscription.category_id,
                });
            } else {
                form.reset({
                    label: '',
                    amount: undefined as unknown as number,
                    amount_sign: -1,
                    frequency: 'monthly',
                    start_at: getDefaultStartAt(),
                    category_id: null,
                });
            }
        }
    }, [subscriptionOpen, editingSubscription, form]);

    function handleOpenChange(open: boolean) {
        if (!open) {
            closeSubscription();
            form.reset();
        }
    }

    function handleError(errors: Record<string, string>) {
        Object.entries(errors).forEach(([key, message]) => {
            form.setError(key as keyof FormValues, { message });
        });
    }

    function onSubmit(data: FormValues) {
        if (editingSubscription) {
            router.patch(`/subscriptions/${editingSubscription.id}`, data, {
                onSuccess: () => {
                    closeSubscription();
                    form.reset();
                },
                onError: handleError,
            });
        } else {
            router.post('/subscriptions', data, {
                onSuccess: () => {
                    closeSubscription();
                    form.reset();
                },
                onError: handleError,
            });
        }
    }

    return (
        <Dialog open={subscriptionOpen} onOpenChange={handleOpenChange}>
            <DialogContent className="max-w-lg">
                <DialogHeader>
                    <DialogTitle>{editingSubscription ? 'Edit Subscription' : 'New Subscription'}</DialogTitle>
                </DialogHeader>
                <Form {...form}>
                    <form onSubmit={form.handleSubmit(onSubmit)} className="space-y-4">
                        <div>
                            <label className="block text-xs font-semibold tracking-wider uppercase text-muted-foreground mb-2">Type</label>
                            <div className="grid grid-cols-2 gap-3">
                                <button
                                    type="button"
                                    onClick={() => form.setValue('amount_sign', -1)}
                                    className={`flex items-center justify-center gap-2 px-4 py-3 rounded-lg border text-sm font-medium transition-all ${
                                        amountSign === -1
                                            ? 'border-ruby/30 bg-red-500-lt/40 text-red-600 dark:bg-red-500-dark-bg/40 dark:text-red-300'
                                            : 'border-border bg-white dark:bg-card text-muted-foreground hover:bg-muted'
                                    }`}
                                >
                                    <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M20 12H4" /></svg>
                                    Expense
                                </button>
                                <button
                                    type="button"
                                    onClick={() => form.setValue('amount_sign', 1)}
                                    className={`flex items-center justify-center gap-2 px-4 py-3 rounded-lg border text-sm font-medium transition-all ${
                                        amountSign === 1
                                            ? 'border-emerald/30 bg-emerald-lt/40 text-emerald-600 dark:bg-emerald-dark-bg/40 dark:text-emerald-300'
                                            : 'border-border bg-white dark:bg-card text-muted-foreground hover:bg-muted'
                                    }`}
                                >
                                    <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 4v16m8-8H4" /></svg>
                                    Income
                                </button>
                            </div>
                            <input type="hidden" {...form.register('amount_sign', { valueAsNumber: true })} />
                        </div>

                        <FormField
                            control={form.control}
                            name="label"
                            render={({ field }) => (
                                <FormItem>
                                    <FormLabel>Label <span className="text-primary">*</span></FormLabel>
                                    <FormControl>
                                        <Input placeholder="e.g. Netflix, Gym, Rent" {...field} />
                                    </FormControl>
                                    <FormMessage />
                                </FormItem>
                            )}
                        />

                        <FormField
                            control={form.control}
                            name="amount"
                            render={({ field }) => (
                                <FormItem>
                                    <FormLabel>Amount <span className="text-primary">*</span></FormLabel>
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

                        <div className="grid grid-cols-2 gap-4">
                            <FormField
                                control={form.control}
                                name="frequency"
                                render={({ field }) => (
                                    <FormItem>
                                        <FormLabel>Frequency</FormLabel>
                                        <Select
                                            value={field.value}
                                            onValueChange={field.onChange}
                                            items={Object.fromEntries(
                                                Object.entries(frequencyLabels).map(([val, label]) => [val, label])
                                            )}
                                        >
                                            <FormControl>
                                                <SelectTrigger className="w-full">
                                                    <SelectValue placeholder="Select frequency" />
                                                </SelectTrigger>
                                            </FormControl>
                                            <SelectContent>
                                                {Object.entries(frequencyLabels).map(([value, label]) => (
                                                    <SelectItem key={value} value={value}>{label}</SelectItem>
                                                ))}
                                            </SelectContent>
                                        </Select>
                                        <FormMessage />
                                    </FormItem>
                                )}
                            />

                            <FormField
                                control={form.control}
                                name="start_at"
                                render={({ field }) => (
                                    <FormItem>
                                        <FormLabel>Start date <span className="text-primary">*</span></FormLabel>
                                        <FormControl>
                                            <Input type="datetime-local" className="py-2.5 font-mono" {...field} />
                                        </FormControl>
                                        <FormMessage />
                                    </FormItem>
                                )}
                            />
                        </div>

                        <FormField
                            control={form.control}
                            name="category_id"
                            render={({ field }) => (
                                <FormItem>
                                    <FormLabel>Category</FormLabel>
                                    <Select
                                        value={field.value ? String(field.value) : ''}
                                        onValueChange={(val) => field.onChange(val ? Number(val) : null)}
                                        items={Object.fromEntries(
                                            categories.map((cat) => {
                                                const IconComp = getCategoryIcon(cat.icon);
                                                return [String(cat.id), <span key={cat.id} className="flex items-center gap-2"><IconComp className="w-3.5 h-3.5 text-muted-foreground" />{cat.name}</span>];
                                            })
                                        )}
                                    >
                                        <FormControl>
                                            <SelectTrigger className="w-full">
                                                <SelectValue placeholder="No category" />
                                            </SelectTrigger>
                                        </FormControl>
                                        <SelectContent>
                                            {categories.map((cat) => {
                                                const IconComp = getCategoryIcon(cat.icon);
                                                return (
                                                    <SelectItem key={cat.id} value={String(cat.id)}>
                                                        <span className="flex items-center gap-2">
                                                            <IconComp className="w-3.5 h-3.5 text-muted-foreground" />
                                                            {cat.name}
                                                        </span>
                                                    </SelectItem>
                                                );
                                            })}
                                        </SelectContent>
                                    </Select>
                                    <FormMessage />
                                </FormItem>
                            )}
                        />

                        <div className="flex items-center justify-end gap-3 pt-2">
                            <Button type="button" variant="outline" onClick={() => { closeSubscription(); form.reset(); }}>
                                Cancel
                            </Button>
                            <Button type="submit">
                                {editingSubscription ? 'Save Changes' : 'Create Subscription'}
                            </Button>
                        </div>
                    </form>
                </Form>
            </DialogContent>
        </Dialog>
    );
}
