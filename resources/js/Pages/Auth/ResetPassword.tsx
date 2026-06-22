import { useState, FormEvent } from 'react';
import { router, usePage } from '@inertiajs/react';
import { GuestLayout } from '@/Pages/Auth/Layout';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Card, CardContent } from '@/components/ui/card';

export default function ResetPassword() {
    const page = usePage();
    const props = page.props as Record<string, unknown>;
    const token = (props.token as string) ?? '';
    const defaultEmail = (props.email as string) ?? '';

    const [email, setEmail] = useState(defaultEmail);
    const [password, setPassword] = useState('');
    const [passwordConfirmation, setPasswordConfirmation] = useState('');
    const [errors, setErrors] = useState<Record<string, string>>({});
    const [processing, setProcessing] = useState(false);

    function handleSubmit(e: FormEvent) {
        e.preventDefault();
        setProcessing(true);
        setErrors({});

        router.post('/reset-password', {
            token,
            email,
            password,
            password_confirmation: passwordConfirmation,
        }, {
            onError: (errs) => {
                setErrors(errs as Record<string, string>);
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
                    <form onSubmit={handleSubmit} className="space-y-5">
                        <input type="hidden" name="token" value={token} />

                        <div>
                            <Label htmlFor="email">Email</Label>
                            <Input
                                id="email"
                                type="email"
                                value={email}
                                onChange={(e) => setEmail(e.target.value)}
                                required
                                autoFocus
                                autoComplete="username"
                                className="mt-1"
                            />
                            {errors.email && <p className="text-xs text-red-600 mt-1">{errors.email}</p>}
                        </div>

                        <div>
                            <Label htmlFor="password">Password</Label>
                            <Input
                                id="password"
                                type="password"
                                value={password}
                                onChange={(e) => setPassword(e.target.value)}
                                required
                                autoComplete="new-password"
                                className="mt-1"
                            />
                            {errors.password && <p className="text-xs text-red-600 mt-1">{errors.password}</p>}
                        </div>

                        <div>
                            <Label htmlFor="password_confirmation">Confirm Password</Label>
                            <Input
                                id="password_confirmation"
                                type="password"
                                value={passwordConfirmation}
                                onChange={(e) => setPasswordConfirmation(e.target.value)}
                                required
                                autoComplete="new-password"
                                className="mt-1"
                            />
                            {errors.password_confirmation && <p className="text-xs text-red-600 mt-1">{errors.password_confirmation}</p>}
                        </div>

                        <Button type="submit" className="w-full justify-center" disabled={processing}>
                            Reset Password
                        </Button>
                    </form>
                </CardContent>
            </Card>
        </GuestLayout>
    );
}
