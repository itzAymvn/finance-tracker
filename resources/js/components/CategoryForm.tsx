import { router } from '@inertiajs/react';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { z } from 'zod';
import type { Category } from '@/lib/types';
import { CATEGORY_ICONS, CATEGORY_ICON_NAMES, getCategoryIcon } from '@/lib/icons';
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

const schema = z.object({
    name: z.string().min(1, 'Name is required').max(255),
    icon: z.string().nullable().optional(),
    is_salary: z.boolean(),
});

type FormValues = z.infer<typeof schema>;

interface CategoryFormProps {
    category?: Category | null;
    onSuccess?: () => void;
}

export function CategoryForm({ category, onSuccess }: CategoryFormProps) {
    const isEdit = !!category;

    const form = useForm<FormValues>({
        resolver: zodResolver(schema),
        defaultValues: {
            name: category?.name ?? '',
            icon: category?.icon ?? null,
            is_salary: category?.is_salary ?? false,
        },
    });

    const selectedIcon = form.watch('icon');

    function handleErrors(errors: Record<string, string>) {
        Object.entries(errors).forEach(([key, message]) => {
            form.setError(key as keyof FormValues, { message });
        });
    }

    function onSubmit(data: FormValues) {
        if (isEdit && category) {
            router.patch(`/categories/${category.id}`, data, {
                onSuccess: () => onSuccess?.(),
                onError: handleErrors,
            });
        } else {
            router.post('/categories', data, {
                onSuccess: () => onSuccess?.(),
                onError: handleErrors,
            });
        }
    }

    const SelectedIconComponent = getCategoryIcon(selectedIcon);

    return (
        <Form {...form}>
            <form onSubmit={form.handleSubmit(onSubmit)} className="space-y-4">
                <FormField
                    control={form.control}
                    name="name"
                    render={({ field }) => (
                        <FormItem>
                            <FormLabel>Name</FormLabel>
                            <FormControl>
                                <Input placeholder="e.g. Subscriptions, Food, Salary..." {...field} />
                            </FormControl>
                            <FormMessage />
                        </FormItem>
                    )}
                />

                <FormField
                    control={form.control}
                    name="icon"
                    render={() => (
                        <FormItem>
                            <FormLabel>Icon</FormLabel>
                            <div className="grid grid-cols-6 gap-2">
                                {CATEGORY_ICON_NAMES.map((name) => {
                                    const IconComp = CATEGORY_ICONS[name];
                                    const isSelected = selectedIcon === name;
                                    return (
                                        <button
                                            key={name}
                                            type="button"
                                            onClick={() => form.setValue('icon', isSelected ? null : name)}
                                            className={`flex flex-col items-center gap-1 p-2 rounded-lg border text-xs transition-all ${
                                                isSelected
                                                    ? 'border-primary bg-primary/10 text-primary'
                                                    : 'border-border bg-muted/50 text-muted-foreground hover:bg-muted'
                                            }`}
                                            title={name}
                                        >
                                            <IconComp className="w-5 h-5" />
                                            <span className="truncate w-full text-center text-[10px]">{name}</span>
                                        </button>
                                    );
                                })}
                            </div>
                            <p className="text-xs text-muted-foreground mt-1 flex items-center gap-1.5">
                                Selected: <SelectedIconComponent className="w-3.5 h-3.5" /> {selectedIcon ?? 'none'}
                            </p>
                            <FormMessage />
                        </FormItem>
                    )}
                />

                <FormField
                    control={form.control}
                    name="is_salary"
                    render={({ field }) => (
                        <FormItem>
                            <label className="flex items-start gap-3 cursor-pointer p-4 rounded-lg border border-border bg-muted/50 hover:bg-muted transition-colors">
                                <FormControl>
                                    <Checkbox
                                        checked={field.value}
                                        onCheckedChange={field.onChange}
                                        className="mt-0.5"
                                    />
                                </FormControl>
                                <div>
                                    <span className="block text-sm font-medium text-foreground">Salary category</span>
                                    <span className="block text-xs text-muted-foreground mt-0.5">
                                        Mark this as the salary category &mdash; credits here are split FIFO across eligible salary months. Only one category can be salary.
                                    </span>
                                </div>
                            </label>
                            <FormMessage />
                        </FormItem>
                    )}
                />

                <div className="flex items-center justify-end gap-3 pt-2">
                    <Button type="button" variant="outline" onClick={() => onSuccess?.()}>
                        Cancel
                    </Button>
                    <Button type="submit">
                        {isEdit ? 'Save Changes' : 'Create Category'}
                    </Button>
                </div>
            </form>
        </Form>
    );
}
