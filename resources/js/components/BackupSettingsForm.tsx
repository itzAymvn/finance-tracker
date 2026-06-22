import { router } from '@inertiajs/react';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { z } from 'zod';
import { BackupSettings } from '@/lib/types';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
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
    backup_enabled: z.boolean(),
    backup_interval_hours: z.number(),
});

type FormValues = z.infer<typeof schema>;

interface BackupSettingsFormProps {
    settings: BackupSettings;
}

const INTERVAL_OPTIONS = [
    { value: 6, label: 'Every 6 hours' },
    { value: 12, label: 'Every 12 hours' },
    { value: 24, label: 'Daily' },
    { value: 168, label: 'Weekly' },
];

export function BackupSettingsForm({ settings }: BackupSettingsFormProps) {
    const form = useForm<FormValues>({
        resolver: zodResolver(schema),
        defaultValues: {
            backup_enabled: settings.backup_enabled,
            backup_interval_hours: settings.backup_interval_hours,
        },
    });

    const enabled = form.watch('backup_enabled');

    function onError(errors: Record<string, string>) {
        Object.entries(errors).forEach(([key, message]) => {
            form.setError(key as keyof FormValues, { message });
        });
    }

    function onSubmit(data: FormValues) {
        router.post('/backup/settings', data, { onError });
    }

    return (
        <Form {...form}>
            <form onSubmit={form.handleSubmit(onSubmit)} className="space-y-6">
                <FormField
                    control={form.control}
                    name="backup_enabled"
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
                                    <span className="block text-sm font-medium text-foreground dark:text-white">Automatic backups</span>
                                    <span className="block text-xs text-muted-foreground dark:text-slate-400 mt-0.5">
                                        Periodically export a full JSON backup of the database.
                                    </span>
                                </div>
                            </label>
                            <input type="hidden" name="backup_enabled" value={field.value ? '1' : '0'} />
                        </FormItem>
                    )}
                />

                <FormField
                    control={form.control}
                    name="backup_interval_hours"
                    render={({ field }) => (
                        <FormItem>
                            <FormLabel>Backup Interval</FormLabel>
                            <FormControl>
                                <Select
                                    value={String(field.value)}
                                    onValueChange={(val) => field.onChange(Number(val))}
                                    disabled={!enabled}
                                >
                                    <SelectTrigger className="w-full">
                                        <SelectValue />
                                    </SelectTrigger>
                                    <SelectContent>
                                        {INTERVAL_OPTIONS.map((opt) => (
                                            <SelectItem key={opt.value} value={String(opt.value)}>
                                                {opt.label}
                                            </SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                            </FormControl>
                            <FormMessage />
                        </FormItem>
                    )}
                />

                <div className="flex items-center gap-3">
                    <Button type="submit" className="" disabled={form.formState.isSubmitting}>
                        Save
                    </Button>
                    {form.formState.isSubmitSuccessful && (
                        <p className="text-sm text-emerald-600 dark:text-emerald-600-400">Saved.</p>
                    )}
                </div>
            </form>
        </Form>
    );
}
