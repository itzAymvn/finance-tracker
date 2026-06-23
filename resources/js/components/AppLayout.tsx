import type { ReactNode } from 'react';
import { Toaster } from 'sonner';
import { TooltipProvider } from '@/components/ui/tooltip';
import { ThemeProvider } from '@/contexts/ThemeContext';
import { ModalProvider } from '@/contexts/ModalContext';
import { SidebarProvider, SidebarInset } from '@/components/ui/sidebar';
import { AppSidebar } from '@/components/AppSidebar';
import { TopBar } from '@/components/TopBar';
import { FlashToaster } from '@/components/FlashToaster';
import { useModals } from '@/contexts/ModalContext';
import { TransactionDialog } from '@/components/TransactionDialog';
import { SalaryMonthDialog } from '@/components/SalaryMonthDialog';
import { SalaryPeriodDialog } from '@/components/SalaryPeriodDialog';
import { SubscriptionDialog } from '@/components/SubscriptionDialog';

function AppLayoutInner({ children }: { children: ReactNode }) {
    const modals = useModals();

    return (
        <SidebarProvider>
            <AppSidebar />
            <SidebarInset>
                <TopBar
                    onCreateTransaction={() => modals.openTransaction()}
                    onCreateMonth={() => modals.openSalaryMonth()}
                    onCreatePeriod={() => modals.openSalaryPeriod()}
                />
                <main className="flex-1 overflow-y-auto">
                    <div className="max-w-6xl mx-auto px-4 lg:px-6 py-8">
                        {children}
                    </div>
                </main>
            </SidebarInset>

            <TransactionDialog />
            <SalaryMonthDialog />
            <SalaryPeriodDialog />
            <SubscriptionDialog />
        </SidebarProvider>
    );
}

export function AppLayout({ children }: { children: ReactNode }) {
    return (
        <ThemeProvider>
            <TooltipProvider>
                <ModalProvider>
                    <AppLayoutInner>{children}</AppLayoutInner>
                    <FlashToaster />
                    <Toaster
                        position="top-right"
                        toastOptions={{
                            duration: 4000,
                            className: 'text-sm',
                        }}
                        richColors
                        closeButton
                    />
                </ModalProvider>
            </TooltipProvider>
        </ThemeProvider>
    );
}
