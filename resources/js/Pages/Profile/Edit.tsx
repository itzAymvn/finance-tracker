import { usePage } from '@inertiajs/react';
import type { ProfileEditProps } from '@/lib/types';
import { AppLayout } from '@/components/AppLayout';
import { ProfileInfoForm } from '@/components/ProfileInfoForm';
import { PasswordForm } from '@/components/PasswordForm';
import { DeleteAccountForm } from '@/components/DeleteAccountForm';

export default function ProfileEdit() {
    const { user } = usePage<ProfileEditProps>().props;

    return (
        <>
            <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
                <div>
                    <h1 className="text-2xl font-bold text-foreground">Profile</h1>
                    <p className="text-sm text-muted-foreground mt-0.5">Manage your account settings</p>
                </div>
            </div>

            <div className="max-w-2xl mx-auto space-y-6">
                <div className="bg-card dark:bg-card rounded-xl border border-border dark:border-border shadow-sm p-6">
                    <ProfileInfoForm user={user} />
                </div>
                <div className="bg-card dark:bg-card rounded-xl border border-border dark:border-border shadow-sm p-6">
                    <PasswordForm />
                </div>
                <div className="bg-card dark:bg-card rounded-xl border border-ruby/20 shadow-sm p-6">
                    <DeleteAccountForm />
                </div>
            </div>
        </>
    );
}

(ProfileEdit as any).layout = (page: React.ReactNode) => <AppLayout>{page}</AppLayout>;
