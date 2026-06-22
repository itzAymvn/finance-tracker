import { Link, usePage } from '@inertiajs/react';
import { ArrowLeft } from 'lucide-react';
import type { Transaction, Category } from '@/lib/types';
import { AppLayout } from '@/components/AppLayout';
import { TransactionForm } from '@/components/TransactionForm';
import { Button } from '@/components/ui/button';

export default function TransactionsEdit() {
    const page = usePage();
    const props = page.props as Record<string, unknown>;
    const transaction = props.transaction as Transaction;
    const categories = props.categories as Category[];

    return (
        <div className="max-w-2xl mx-auto">
            <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
                <div>
                    <h1 className="text-2xl font-bold text-foreground">Edit Transaction</h1>
                    <p className="text-sm text-muted-foreground mt-0.5">Modify transaction details and tagging</p>
                </div>
                <Button variant="outline" render={<Link href="/transactions" />}>
                    <ArrowLeft className="w-4 h-4" />
                    Back
                </Button>
            </div>

            <TransactionForm transaction={transaction} categories={categories} />
        </div>
    );
}

// eslint-disable-next-line @typescript-eslint/no-explicit-any
(TransactionsEdit as any).layout = (page: React.ReactNode) => <AppLayout>{page}</AppLayout>;
