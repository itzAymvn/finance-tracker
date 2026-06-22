import { useState, FormEvent } from 'react';
import { router } from '@inertiajs/react';
import { GuestLayout } from '@/Pages/Auth/Layout';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Card, CardContent } from '@/components/ui/card';

export default function ForgotPassword() {
    const [email, setEmail] = useState('');
    const [errors, setErrors] = useState<Record<string, string>>({});
    const [processing, setProcessing] = useState(false);
    const [status, setStatus] = useState('');

    function handleSubmit(e: FormEvent) {
        e.preventDefault();
        setProcessing(true);
        setErrors({});
        setStatus('');

        router.post('/forgot-password', {
            email,
        }, {
            onError: (errs) => {
                setErrors(errs as Record<string, string>);
                setProcessing(false);
            },
            onSuccess: () => {
                setStatus('We have emailed your password reset link.');
                setProcessing(false);
            },
            onFinish: () => {
                setProcessing(false);
            },
        });
    }

    return (
        <GuestLayout>
            <Card>
                <CardContent className="p-6 sm:p-10">
                    <div className="mb-4 text-sm text-muted-foreground dark:text-slate-400">
                        Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.
                    </div>

                    {status && (
                        <div className="mb-4 text-sm font-medium text-emerald-600 dark:text-emerald-600-400">
                            {status}
                        </div>
                    )}

                    <form onSubmit={handleSubmit} className="space-y-5">
                        <div>
                            <Label htmlFor="email">Email</Label>
                            <Input
                                id="email"
                                type="email"
                                value={email}
                                onChange={(e) => setEmail(e.target.value)}
                                required
                                autoFocus
                                className="mt-1"
                            />
                            {errors.email && <p className="text-xs text-red-600 mt-1">{errors.email}</p>}
                        </div>

                        <Button type="submit" className="w-full justify-center" disabled={processing}>
                            Email Password Reset Link
                        </Button>
                    </form>
                </CardContent>
            </Card>
        </GuestLayout>
    );
}
