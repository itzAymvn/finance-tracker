import { useState } from 'react';
import { Link, usePage } from '@inertiajs/react';
import { ArrowLeft } from 'lucide-react';
import type { SalaryMonth } from '@/lib/types';
import { AppLayout } from '@/components/AppLayout';
import { SalaryMonthForm } from '@/components/SalaryMonthForm';
import { SalaryMonthDeleteDialog } from '@/components/SalaryMonthDeleteDialog';
import { Button } from '@/components/ui/button';

export default function SalaryMonthsEdit() {
    const page = usePage();
    const salaryMonth = (page.props as Record<string, unknown>).salaryMonth as SalaryMonth;
    const [deleteOpen, setDeleteOpen] = useState(false);

    return (
        <div className="max-w-xl mx-auto">
            <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
                <div>
                    <h1 className="text-2xl font-bold text-foreground">Edit {salaryMonth.label}</h1>
                    <p className="text-sm text-muted-foreground mt-0.5">Update salary month details</p>
                </div>
                <Button variant="outline" render={<Link href={`/salary-months/${salaryMonth.id}`} />}>
                    <ArrowLeft className="w-4 h-4" />
                    Back
                </Button>
            </div>

            <div className="bg-card dark:bg-card rounded-xl border border-border dark:border-border shadow-sm overflow-hidden mb-6">
                <div className="px-6 py-4 border-b border-border dark:border-border">
                    <h2 className="text-lg font-semibold text-foreground dark:text-white">Month Details</h2>
                </div>
                <SalaryMonthForm salaryMonth={salaryMonth} />
            </div>

            <div className="bg-card dark:bg-card rounded-xl border border-ruby/20 shadow-sm p-5">
                <p className="text-xs font-semibold tracking-wider uppercase text-red-600/60 mb-3">Danger Zone</p>
                <div className="flex items-center justify-between">
                    <div>
                        <p className="text-sm font-medium text-foreground dark:text-white">Delete this month</p>
                        <p className="text-xs text-muted-foreground dark:text-slate-400 mt-0.5">This will also remove all related allocations.</p>
                    </div>
                    <Button variant="destructive" onClick={() => setDeleteOpen(true)}>Delete Month</Button>
                </div>
            </div>

            <SalaryMonthDeleteDialog
                open={deleteOpen}
                onOpenChange={setDeleteOpen}
                salaryMonth={salaryMonth}
            />
        </div>
    );
}

// eslint-disable-next-line @typescript-eslint/no-explicit-any
(SalaryMonthsEdit as any).layout = (page: React.ReactNode) => <AppLayout>{page}</AppLayout>;
