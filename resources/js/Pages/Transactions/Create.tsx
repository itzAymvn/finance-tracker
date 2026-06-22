import { Link, usePage } from '@inertiajs/react';
import { ArrowLeft } from 'lucide-react';
import type { Category } from '@/lib/types';
import { AppLayout } from '@/components/AppLayout';
import { TransactionForm } from '@/components/TransactionForm';
import { Button } from '@/components/ui/button';

export default function TransactionsCreate() {
    const { categories } = usePage<{ categories: Category[] }>().props;

    return (
        <div className="max-w-2xl mx-auto">
            <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
                <div>
                    <h1 className="text-2xl font-bold text-foreground">New Transaction</h1>
                    <p className="text-sm text-muted-foreground mt-0.5">Record a new credit or debit transaction</p>
                </div>
                <Button variant="outline" render={<Link href="/transactions" />}>
                    <ArrowLeft className="w-4 h-4" />
                    Cancel
                </Button>
            </div>

            <div className="bg-card dark:bg-card rounded-xl border border-border dark:border-border shadow-sm">
                <TransactionForm categories={categories} />
            </div>
        </div>
    );
}

// eslint-disable-next-line @typescript-eslint/no-explicit-any
(TransactionsCreate as any).layout = (page: React.ReactNode) => <AppLayout>{page}</AppLayout>;
