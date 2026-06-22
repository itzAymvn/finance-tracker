import type { ReactNode } from 'react';

export function GuestLayout({ children }: { children: ReactNode }) {
    return (
        <div className="min-h-screen flex flex-col items-center justify-center bg-background dark:bg-background p-4">
            <a href="/login" className="flex items-center gap-3 mb-8">
                <div className="w-10 h-10 rounded-xl bg-primary flex items-center justify-center">
                    <svg className="w-5 h-5 fill-white" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 14.93V18h-2v-1.07A4.01 4.01 0 0 1 8 13h2c0 1.1.9 2 2 2s2-.9 2-2c0-1.1-.9-2-2-2a4 4 0 0 1 0-8V6h2v-.07A4.01 4.01 0 0 1 16 9h-2c0-1.1-.9-2-2-2s-2 .9-2 2 .9 2 2 2a4 4 0 0 1 0 8z" />
                    </svg>
                </div>
                <span className="text-xl font-semibold text-foreground dark:text-white">Payroll</span>
            </a>
            <div className="w-full max-w-md">
                {children}
            </div>
        </div>
    );
}
