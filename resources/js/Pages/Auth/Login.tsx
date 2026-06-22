import { useState, FormEvent } from 'react';
import { router, Link } from '@inertiajs/react';
import { GuestLayout } from '@/Pages/Auth/Layout';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Checkbox } from '@/components/ui/checkbox';
import { Card, CardContent } from '@/components/ui/card';

export default function Login() {
    const [email, setEmail] = useState('');
    const [password, setPassword] = useState('');
    const [remember, setRemember] = useState(false);
    const [errors, setErrors] = useState<Record<string, string>>({});
    const [processing, setProcessing] = useState(false);

    function handleSubmit(e: FormEvent) {
        e.preventDefault();
        setProcessing(true);
        setErrors({});

        router.post('/login', {
            email,
            password,
            remember,
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
                                autoComplete="current-password"
                                className="mt-1"
                            />
                            {errors.password && <p className="text-xs text-red-600 mt-1">{errors.password}</p>}
                        </div>

                        <div className="flex items-center justify-between">
                            <label htmlFor="remember_me" className="flex items-center gap-2 cursor-pointer">
                                <Checkbox
                                    id="remember_me"
                                    checked={remember}
                                    onCheckedChange={(checked) => setRemember(checked === true)}
                                />
                                <span className="text-sm text-muted-foreground dark:text-slate-400">Remember me</span>
                            </label>

                            <Link
                                href="/forgot-password"
                                className="text-sm text-muted-foreground dark:text-slate-400 hover:text-primary transition-colors"
                            >
                                Forgot password?
                            </Link>
                        </div>

                        <Button type="submit" className="w-full justify-center" disabled={processing}>
                            Log in
                        </Button>
                    </form>
                </CardContent>
            </Card>
        </GuestLayout>
    );
}
