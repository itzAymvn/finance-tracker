import { useEffect } from 'react';
import { usePage } from '@inertiajs/react';
import { toast } from 'sonner';
import type { PageProps } from '@/lib/types';

export function FlashToaster() {
    const { flash, errors } = usePage<PageProps>().props;

    useEffect(() => {
        if (flash.success) {
            toast.success(flash.success);
        }
        if (flash.error) {
            toast.error(flash.error);
        }
        if (flash.status === 'profile-updated' || flash.status === 'password-updated') {
            toast.success('Saved.');
        }
        if (errors && Object.keys(errors).length > 0) {
            const messages = Object.values(errors).flat();
            if (messages.length === 1) {
                toast.error(messages[0]);
            } else if (messages.length > 1) {
                toast.error(
                    <div>
                        <p className="font-semibold mb-1">Please fix the following errors:</p>
                        <ul className="list-disc ml-4 space-y-0.5">
                            {messages.map((msg, i) => (
                                <li key={i}>{msg}</li>
                            ))}
                        </ul>
                    </div>
                );
            }
        }
    }, [flash.success, flash.error, flash.status, errors]);

    return null;
}
