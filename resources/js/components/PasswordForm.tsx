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
    current_password: z.string().min(1, 'Current password is required'),
    password: z.string().min(1, 'New password is required').min(8, 'Password must be at least 8 characters'),
    password_confirmation: z.string().min(1, 'Please confirm your new password'),
}).refine((data) => data.password === data.password_confirmation, {
    message: 'Passwords do not match',
    path: ['password_confirmation'],
});

type FormValues = z.infer<typeof schema>;

export function PasswordForm() {
    const form = useForm<FormValues>({
        resolver: zodResolver(schema),
        defaultValues: {
            current_password: '',
            password: '',
            password_confirmation: '',
        },
    });

    function onError(errors: Record<string, string>) {
        Object.entries(errors).forEach(([key, message]) => {
            form.setError(key as keyof FormValues, { message });
        });
    }

    function onSubmit(data: FormValues) {
        router.put('/password', data, {
            onSuccess: () => form.reset(),
            onError,
        });
    }

    return (
        <Form {...form}>
            <form onSubmit={form.handleSubmit(onSubmit)} className="space-y-6">
                <FormField
                    control={form.control}
                    name="current_password"
                    render={({ field }) => (
                        <FormItem>
                            <FormLabel>Current Password</FormLabel>
                            <FormControl>
                                <Input type="password" autoComplete="current-password" {...field} />
                            </FormControl>
                            <FormMessage />
                        </FormItem>
                    )}
                />

                <FormField
                    control={form.control}
                    name="password"
                    render={({ field }) => (
                        <FormItem>
                            <FormLabel>New Password</FormLabel>
                            <FormControl>
                                <Input type="password" autoComplete="new-password" {...field} />
                            </FormControl>
                            <FormMessage />
                        </FormItem>
                    )}
                />

                <FormField
                    control={form.control}
                    name="password_confirmation"
                    render={({ field }) => (
                        <FormItem>
                            <FormLabel>Confirm Password</FormLabel>
                            <FormControl>
                                <Input type="password" autoComplete="new-password" {...field} />
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
