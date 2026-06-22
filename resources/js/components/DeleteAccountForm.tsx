import { useState } from 'react';
import { router } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import {
    AlertDialog,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
} from '@/components/ui/alert-dialog';

export function DeleteAccountForm() {
    const [open, setOpen] = useState(false);
    const [password, setPassword] = useState('');
    const [error, setError] = useState('');

    function handleDelete() {
        setError('');
        router.delete('/profile', {
            data: { password },
            onError: (errors) => {
                if (errors.password) {
                    setError(errors.password);
                }
            },
        });
    }

    return (
        <div className="space-y-4">
            <div className="rounded-xl border border-destructive/30 bg-destructive/5 p-6">
                <h3 className="text-base font-semibold text-destructive mb-1">Delete Account</h3>
                <p className="text-sm text-muted-foreground mb-4">
                    Once your account is deleted, all of its resources and data will be permanently deleted.
                </p>
                <Button
                    type="button"
                    variant="destructive"
                    onClick={() => {
                        setPassword('');
                        setError('');
                        setOpen(true);
                    }}
                >
                    Delete Account
                </Button>
            </div>

            <AlertDialog open={open} onOpenChange={setOpen}>
                <AlertDialogContent>
                    <AlertDialogHeader>
                        <AlertDialogTitle>Are you sure?</AlertDialogTitle>
                        <AlertDialogDescription>
                            This action cannot be undone. Please enter your password to confirm you would like to permanently delete your account.
                        </AlertDialogDescription>
                    </AlertDialogHeader>

                    <div className="space-y-2">
                        <label className="text-sm font-medium">Password</label>
                        <Input
                            type="password"
                            value={password}
                            onChange={(e) => setPassword(e.target.value)}
                            placeholder="Enter your password"
                            autoComplete="current-password"
                        />
                        {error && <p className="text-xs text-destructive">{error}</p>}
                    </div>

                    <AlertDialogFooter>
                        <AlertDialogCancel>Cancel</AlertDialogCancel>
                        <Button
                            variant="destructive"
                            onClick={handleDelete}
                            disabled={!password}
                        >
                            Delete Account
                        </Button>
                    </AlertDialogFooter>
                </AlertDialogContent>
            </AlertDialog>
        </div>
    );
}
