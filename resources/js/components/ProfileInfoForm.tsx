import { router } from '@inertiajs/react';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { z } from 'zod';
import { User } from '@/lib/types';
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
    name: z.string().min(1, 'Name is required').max(255),
    email: z.string().min(1, 'Email is required').email('Invalid email address').max(255),
});

type FormValues = z.infer<typeof schema>;

interface ProfileInfoFormProps {
    user: User;
}

export function ProfileInfoForm({ user }: ProfileInfoFormProps) {
    const form = useForm<FormValues>({
        resolver: zodResolver(schema),
        defaultValues: {
            name: user.name,
            email: user.email,
        },
    });

    function onError(errors: Record<string, string>) {
        Object.entries(errors).forEach(([key, message]) => {
            form.setError(key as keyof FormValues, { message });
        });
    }

    function onSubmit(data: FormValues) {
        router.patch('/profile', data, {
            onError,
        });
    }

    return (
        <Form {...form}>
            <form onSubmit={form.handleSubmit(onSubmit)} className="space-y-6">
                <FormField
                    control={form.control}
                    name="name"
                    render={({ field }) => (
                        <FormItem>
                            <FormLabel>Name</FormLabel>
                            <FormControl>
                                <Input {...field} />
                            </FormControl>
                            <FormMessage />
                        </FormItem>
                    )}
                />

                <FormField
                    control={form.control}
                    name="email"
                    render={({ field }) => (
                        <FormItem>
                            <FormLabel>Email</FormLabel>
                            <FormControl>
                                <Input type="email" {...field} />
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
