import { useState } from 'react';
import { router, usePage } from '@inertiajs/react';
import { Plus, Search, SlidersHorizontal } from 'lucide-react';
import type { TransactionsIndexProps } from '@/lib/types';
import { formatMoney } from '@/lib/format';
import { getCategoryIcon } from '@/lib/icons';
import { useModals } from '@/contexts/ModalContext';
import { AppLayout } from '@/components/AppLayout';
import { TransactionsTable } from '@/components/TransactionsTable';
import { Pagination } from '@/components/Pagination';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';

export default function TransactionsIndex() {
    const { transactions, years, summary, categories } = usePage<TransactionsIndexProps>().props;
    const modals = useModals();

    const params = new URLSearchParams(window.location.search);
    const [search, setSearch] = useState(params.get('search') ?? '');
    const [type, setType] = useState(params.get('type') ?? 'all-types');
    const [category, setCategory] = useState(params.get('category') ?? 'all-categories');
    const [month, setMonth] = useState(params.get('month') ?? '');
    const [year, setYear] = useState(params.get('year') ?? 'any-year');

    const hasFilters = search || (type && type !== 'all-types') || (category && category !== 'all-categories') || month || (year && year !== 'any-year');

    function applyFilters() {
        const p: Record<string, string> = {};
        if (search) p.search = search;
        if (type && type !== 'all-types') p.type = type;
        if (category && category !== 'all-categories') p.category = category;
        if (month) p.month = month;
        if (year && year !== 'any-year') p.year = year;
        router.get('/transactions', p, { preserveState: true });
    }

    function clearFilters() {
        setSearch('');
        setType('all-types');
        setCategory('all-categories');
        setMonth('');
        setYear('any-year');
        router.get('/transactions');
    }

    return (
        <>
            <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
                <div>
                    <h1 className="text-2xl font-bold text-foreground">Transactions</h1>
                    <p className="text-sm text-muted-foreground mt-0.5">View and manage all your financial transactions</p>
                </div>
                <div className="flex gap-2">
                    <Button onClick={() => modals.openTransaction()}>
                        <Plus className="w-4 h-4" />
                        New Transaction
                    </Button>
                </div>
            </div>

            <div className="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <Card>
                    <CardContent>
                        <p className="text-xs font-semibold tracking-wider uppercase text-muted-foreground dark:text-slate-400 mb-1.5">Showing</p>
                        <p className="font-mono text-xl font-medium text-foreground dark:text-white">{summary.count} <span className="text-xs text-muted-foreground dark:text-slate-400 font-normal">tx</span></p>
                    </CardContent>
                </Card>
                <Card className="relative overflow-hidden">
                    <CardContent>
                        <p className="text-xs font-semibold tracking-wider uppercase text-muted-foreground dark:text-slate-400 mb-1.5">Credits</p>
                        <p className="font-mono text-xl font-medium text-emerald-600 dark:text-emerald-600-400">+{formatMoney(summary.credits)}</p>
                    </CardContent>
                    <div className="absolute bottom-0 left-0 right-0 h-[3px] bg-emerald"></div>
                </Card>
                <Card className="relative overflow-hidden">
                    <CardContent>
                        <p className="text-xs font-semibold tracking-wider uppercase text-muted-foreground dark:text-slate-400 mb-1.5">Debits</p>
                        <p className="font-mono text-xl font-medium text-red-600 dark:text-red-400">{formatMoney(summary.debits)}</p>
                    </CardContent>
                    <div className="absolute bottom-0 left-0 right-0 h-[3px] bg-red-500"></div>
                </Card>
                <Card className="relative overflow-hidden">
                    <CardContent>
                        <p className="text-xs font-semibold tracking-wider uppercase text-muted-foreground dark:text-slate-400 mb-1.5">Net</p>
                        <p className={`font-mono text-xl font-medium ${summary.net >= 0 ? 'text-emerald-600 dark:text-emerald-600-400' : 'text-red-600 dark:text-red-400'}`}>
                            {summary.net >= 0 ? '+' : ''}{formatMoney(summary.net)}
                        </p>
                    </CardContent>
                    <div className={`absolute bottom-0 left-0 right-0 h-[3px] ${summary.net >= 0 ? 'bg-emerald' : 'bg-red-500'}`}></div>
                </Card>
            </div>

            <div className="bg-card dark:bg-card rounded-xl border border-border dark:border-border shadow-sm overflow-hidden">
                <div className="px-6 py-5 border-b border-border dark:border-border bg-muted/50 dark:bg-muted/50">
                    <div className="flex items-center gap-2 mb-4">
                        <SlidersHorizontal className="w-4 h-4 text-muted-foreground" />
                        <span className="text-xs font-semibold tracking-wider uppercase text-muted-foreground">Filters</span>
                        {hasFilters && (
                            <Button variant="ghost" size="sm" onClick={clearFilters} className="ml-auto h-6 text-xs">
                                Clear all
                            </Button>
                        )}
                    </div>
                    <div className="flex flex-wrap items-end gap-3">
                        <div className="relative flex-1 min-w-[200px]">
                            <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground/70 pointer-events-none" />
                            <Input
                                type="text"
                                value={search}
                                onChange={(e) => setSearch(e.target.value)}
                                placeholder="Search label..."
                                className="h-9 text-xs pl-9"
                                onKeyDown={(e) => { if (e.key === 'Enter') applyFilters(); }}
                            />
                        </div>
                        <Select
                            value={type}
                            onValueChange={setType}
                            items={{
                                'all-types': 'All types',
                                'credit': 'Credits only',
                                'debit': 'Debits only',
                                'salary': 'Salary only',
                            }}
                        >
                            <SelectTrigger className="h-9 text-xs w-[140px]">
                                <SelectValue placeholder="All types" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="all-types">All types</SelectItem>
                                <SelectItem value="credit">Credits only</SelectItem>
                                <SelectItem value="debit">Debits only</SelectItem>
                                <SelectItem value="salary">Salary only</SelectItem>
                            </SelectContent>
                        </Select>
                        <Select
                            value={category}
                            onValueChange={setCategory}
                            items={{
                                'all-categories': 'All categories',
                                'null': 'Uncategorized',
                                ...Object.fromEntries(
                                    categories.map((cat) => {
                                        const IconComp = getCategoryIcon(cat.icon);
                                        return [String(cat.id), <span key={cat.id} className="flex items-center gap-2"><IconComp className="w-3.5 h-3.5 text-muted-foreground" />{cat.name}</span>];
                                    })
                                ),
                            }}
                        >
                            <SelectTrigger className="h-9 text-xs w-[160px]">
                                <SelectValue placeholder="All categories" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="all-categories">All categories</SelectItem>
                                <SelectItem value="null">Uncategorized</SelectItem>
                                {categories.map((cat) => {
                                    const IconComp = getCategoryIcon(cat.icon);
                                    return (
                                        <SelectItem key={cat.id} value={String(cat.id)}>
                                            <span className="flex items-center gap-2">
                                                <IconComp className="w-3.5 h-3.5 text-muted-foreground" />
                                                {cat.name}
                                            </span>
                                        </SelectItem>
                                    );
                                })}
                            </SelectContent>
                        </Select>
                        <Input
                            type="month"
                            value={month}
                            onChange={(e) => setMonth(e.target.value)}
                            title="Specific month"
                            className="h-9 text-xs font-mono w-[150px]"
                        />
                        <Select
                            value={year}
                            onValueChange={setYear}
                            items={{
                                'any-year': 'Any year',
                                ...Object.fromEntries(years.map((y) => [y, y])),
                            }}
                        >
                            <SelectTrigger className="h-9 text-xs w-[120px]">
                                <SelectValue placeholder="Any year" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="any-year">Any year</SelectItem>
                                {years.map((y) => (
                                    <SelectItem key={y} value={y}>{y}</SelectItem>
                                ))}
                            </SelectContent>
                        </Select>
                        <Button size="sm" onClick={applyFilters} className="h-9 text-xs px-4">
                            Apply
                        </Button>
                    </div>
                </div>

                <TransactionsTable transactions={transactions.data} />

                <Pagination links={transactions.links} />
            </div>
        </>
    );
}

// eslint-disable-next-line @typescript-eslint/no-explicit-any
(TransactionsIndex as any).layout = (page: React.ReactNode) => <AppLayout>{page}</AppLayout>;
