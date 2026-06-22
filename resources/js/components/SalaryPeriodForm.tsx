import { router } from '@inertiajs/react';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { z } from 'zod';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import {
    Form,
    FormControl,
    FormField,
    FormItem,
    FormLabel,
    FormMessage,
} from '@/components/ui/form';

const schema = z.object({
    from_month: z.string().min(1, 'Start month is required').regex(/^\d{4}-\d{2}$/, 'Start month must be in YYYY-MM format'),
    to_month: z.string().min(1, 'End month is required').regex(/^\d{4}-\d{2}$/, 'End month must be in YYYY-MM format'),
    expected_salary: z.coerce.number().min(0.01, 'Expected salary must be at least 0.01'),
    currency: z.string().min(1, 'Currency is required').max(10),
    notes: z.string().max(1000).optional(),
}).refine((data) => data.to_month >= data.from_month, {
    message: 'End month must be the same as or after the start month.',
    path: ['to_month'],
});

type FormValues = z.infer<typeof schema>;

interface SalaryPeriodFormProps {
    onSuccess?: () => void;
}

export function SalaryPeriodForm({ onSuccess }: SalaryPeriodFormProps) {
    const form = useForm<FormValues>({
        resolver: zodResolver(schema),
        defaultValues: {
            from_month: '',
            to_month: '',
            expected_salary: undefined as unknown as number,
            currency: 'MAD',
            notes: '',
        },
    });

    function onError(errors: Record<string, string>) {
        Object.entries(errors).forEach(([key, message]) => {
            form.setError(key as keyof FormValues, { message });
        });
    }

    function onSubmit(data: FormValues) {
        router.post('/salary-months/period', data, {
            onSuccess: () => onSuccess?.(),
            onError,
        });
    }

    return (
        <Form {...form}>
            <form onSubmit={form.handleSubmit(onSubmit)} className="space-y-4">
                <div className="rounded-lg border border-amber/30 bg-amber-lt/30 dark:bg-amber-dark-bg/30 px-4 py-3 text-sm text-amber-600 dark:text-amber-600-300">
                    Creates one salary month for every month in the range. Months that already exist are skipped.
                </div>

                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <FormField
                        control={form.control}
                        name="from_month"
                        render={({ field }) => (
                            <FormItem>
                                <FormLabel>
                                    From Month <span className="text-primary">*</span>
                                </FormLabel>
                                <FormControl>
                                    <Input type="month" className="py-2.5 font-mono" {...field} />
                                </FormControl>
                                <FormMessage />
                            </FormItem>
                        )}
                    />
                    <FormField
                        control={form.control}
                        name="to_month"
                        render={({ field }) => (
                            <FormItem>
                                <FormLabel>
                                    To Month <span className="text-primary">*</span>
                                </FormLabel>
                                <FormControl>
                                    <Input type="month" className="py-2.5 font-mono" {...field} />
                                </FormControl>
                                <FormMessage />
                            </FormItem>
                        )}
                    />
                </div>

                <FormField
                    control={form.control}
                    name="expected_salary"
                    render={({ field }) => (
                        <FormItem>
                            <FormLabel>
                                Expected Salary <span className="text-primary">*</span>
                            </FormLabel>
                            <FormControl>
                                <Input
                                    type="number"
                                    step="0.01"
                                    min="0.01"
                                    placeholder="0.00"
                                    className="py-2.5 font-mono"
                                    {...field}
                                    value={field.value ?? ''}
                                />
                            </FormControl>
                            <FormMessage />
                        </FormItem>
                    )}
                />

                <FormField
                    control={form.control}
                    name="currency"
                    render={({ field }) => (
                        <FormItem>
                            <FormLabel>
                                Currency <span className="text-primary">*</span>
                            </FormLabel>
                            <FormControl>
                                <Input className="py-2.5" {...field} />
                            </FormControl>
                            <FormMessage />
                        </FormItem>
                    )}
                />

                <FormField
                    control={form.control}
                    name="notes"
                    render={({ field }) => (
                        <FormItem>
                            <FormLabel>Notes</FormLabel>
                            <FormControl>
                                <textarea
                                    className="w-full rounded-lg border border-input bg-transparent px-2.5 py-1.5 text-base transition-colors outline-none placeholder:text-muted-foreground focus-visible:border-ring focus-visible:ring-3 focus-visible:ring-ring/50 disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-50 md:text-sm min-h-[80px]"
                                    rows={3}
                                    {...field}
                                />
                            </FormControl>
                            <FormMessage />
                        </FormItem>
                    )}
                />

                <div className="flex items-center justify-end gap-3 pt-2">
                    <Button type="button" variant="outline" onClick={() => onSuccess?.()}>
                        Cancel
                    </Button>
                    <Button type="submit" className="">
                        Create Period
                    </Button>
                </div>
            </form>
        </Form>
    );
}
