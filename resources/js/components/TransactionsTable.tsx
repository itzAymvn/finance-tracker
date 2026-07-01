import { createColumnHelper, useReactTable, getCoreRowModel, flexRender } from '@tanstack/react-table';
import { Info, AlertTriangle } from 'lucide-react';
import type { Transaction } from '@/lib/types';
import { formatDate, formatMoney, formatMoneyInteger } from '@/lib/format';
import { getCategoryIcon } from '@/lib/icons';
import { useModals } from '@/contexts/ModalContext';
import { Popover, PopoverTrigger, PopoverContent } from '@/components/ui/popover';
import { Button } from '@/components/ui/button';
import { Table, TableHeader, TableBody, TableRow, TableHead, TableCell } from '@/components/ui/table';
import { Badge } from '@/components/ui/badge';

const columnHelper = createColumnHelper<Transaction>();

const columns = [
    columnHelper.accessor('paid_at', {
        header: 'Date',
        cell: (info) => formatDate(info.getValue()),
    }),
    columnHelper.accessor('label', {
        header: 'Label',
        cell: (info) => {
            const details = info.row.original.details;
            if (details) {
                return (
                    <Popover>
                        <PopoverTrigger className="cursor-pointer">
                            <span className="max-w-[400px] truncate block hover:text-primary transition-colors text-left uppercase">
                                {info.getValue()}
                            </span>
                        </PopoverTrigger>
                        <PopoverContent className="w-72 p-4" align="start">
                            <p className="text-sm font-medium text-foreground mb-1 uppercase">{info.getValue()}</p>
                            <p className="text-xs text-muted-foreground leading-relaxed">{details}</p>
                        </PopoverContent>
                    </Popover>
                );
            }
            return (
                <div className="max-w-[400px] truncate uppercase" title={info.getValue()}>
                    {info.getValue()}
                </div>
            );
        },
    }),
    columnHelper.display({
        id: 'category',
        header: 'Category',
        cell: (info) => {
            const cat = info.row.original.category;
            if (!cat) {
                return <span className="text-muted-foreground text-xs">—</span>;
            }
            const IconComp = getCategoryIcon(cat.icon);
            return (
                <span className="inline-flex items-center gap-1.5 text-xs font-medium text-foreground">
                    <IconComp className="w-3.5 h-3.5 text-muted-foreground" />
                    {cat.name}
                </span>
            );
        },
    }),
    columnHelper.display({
        id: 'debit',
        header: () => <div className="text-right">Débit</div>,
        cell: (info) => {
            const amt = parseFloat(info.row.original.amount);
            if (amt < 0) {
                return (
                    <div className="text-right font-mono text-red-600 dark:text-red-400 font-medium">
                        {formatMoney(Math.abs(amt))}
                    </div>
                );
            }
            return <div className="text-right font-mono text-muted-foreground dark:text-slate-400">—</div>;
        },
    }),
    columnHelper.display({
        id: 'credit',
        header: () => <div className="text-right">Crédit</div>,
        cell: (info) => {
            const amt = parseFloat(info.row.original.amount);
            const tx = info.row.original;
            if (amt >= 0) {
                const hasAllocations = tx.allocations && tx.allocations.length > 0;
                const unallocated = tx.unallocated ?? 0;
                return (
                    <div className="text-right font-mono font-semibold text-emerald-600 dark:text-emerald-600-400">
                        {formatMoney(amt)}
                        {hasAllocations && (
                            <Popover>
                                <PopoverTrigger>
                                    <Info className="w-3.5 h-3.5 inline text-muted-foreground dark:text-slate-400 hover:text-primary transition-colors ml-1 cursor-pointer" />
                                </PopoverTrigger>
                                <PopoverContent className="w-64 p-4" align="end">
                                    <p className="font-semibold text-foreground dark:text-white mb-3 text-sm">Allocations</p>
                                    <div className="space-y-1.5">
                                        {tx.allocations!.map((a) => (
                                            <div key={a.id} className="flex items-center justify-between text-muted-foreground dark:text-slate-400 text-xs">
                                                <span>{a.salary_month?.label}</span>
                                                <span className="font-mono text-emerald-600 dark:text-emerald-600-400 font-medium">{formatMoneyInteger(a.amount)}</span>
                                            </div>
                                        ))}
                                    </div>
                                </PopoverContent>
                            </Popover>
                        )}
                        {tx.category?.is_salary && unallocated > 0.005 && (
                            <Popover>
                                <PopoverTrigger>
                                    <AlertTriangle className="w-3.5 h-3.5 inline text-amber-600 dark:text-amber-600-400 hover:text-amber-600/80 transition-colors ml-1 cursor-pointer" />
                                </PopoverTrigger>
                                <PopoverContent className="w-56 p-4" align="end">
                                    <p className="text-amber-600 dark:text-amber-600-400 font-medium mb-2 text-sm">Unallocated</p>
                                    <p className="text-muted-foreground dark:text-slate-400 text-xs">
                                        {formatMoneyInteger(unallocated)} MAD — no eligible month has remaining capacity.
                                    </p>
                                </PopoverContent>
                            </Popover>
                        )}
                    </div>
                );
            }
            return <div className="text-right font-mono text-muted-foreground dark:text-slate-400">—</div>;
        },
    }),
    columnHelper.display({
        id: 'type',
        header: 'Type',
        cell: (info) => {
            const tx = info.row.original;
            const unallocated = tx.unallocated ?? 0;
            if (tx.category?.is_salary) {
                return (
                    <div className="flex items-center gap-1.5">
                        <span className="badge-emerald">
                            <span className="w-1.5 h-1.5 rounded-full bg-emerald dark:bg-emerald-400 mr-1"></span>
                            Salary
                        </span>
                        {unallocated > 0.005 && (
                            <Popover>
                                <PopoverTrigger>
                                    <AlertTriangle className="w-3.5 h-3.5 text-amber-600 dark:text-amber-600-400 hover:text-amber-600/80 transition-colors cursor-pointer" />
                                </PopoverTrigger>
                                <PopoverContent className="w-56 p-4" align="end">
                                    <p className="text-amber-600 dark:text-amber-600-400 font-medium mb-2 text-sm">Unallocated</p>
                                    <p className="text-muted-foreground dark:text-slate-400 text-xs">
                                        {formatMoneyInteger(unallocated)} MAD — no eligible month has remaining capacity.
                                    </p>
                                </PopoverContent>
                            </Popover>
                        )}
                    </div>
                );
            }
            return <span className="text-muted-foreground dark:text-slate-400 text-xs">—</span>;
        },
    }),
    columnHelper.display({
        id: 'actions',
        header: () => <div className="text-right"></div>,
        cell: (info) => {
            const tx = info.row.original;
            return <EditButton tx={tx} />;
        },
    }),
];

function EditButton({ tx }: { tx: Transaction }) {
    const modals = useModals();
    return (
        <div className="text-right">
            <Button variant="ghost" size="sm" onClick={() => modals.openTransaction(tx)}>
                Edit
            </Button>
        </div>
    );
}

interface TransactionsTableProps {
    transactions: Transaction[];
}

export function TransactionsTable({ transactions }: TransactionsTableProps) {
    const table = useReactTable({
        data: transactions,
        columns,
        getCoreRowModel: getCoreRowModel(),
    });

    if (transactions.length === 0) {
        return (
            <div className="text-center py-16 px-6">
                <div className="w-14 h-14 mx-auto mb-4 rounded-xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-2xl opacity-50">💳</div>
                <p className="text-sm text-muted-foreground dark:text-slate-400 mb-4">No transactions match the current filter.</p>
            </div>
        );
    }

    return (
        <Table>
            <TableHeader>
                {table.getHeaderGroups().map((headerGroup) => (
                    <TableRow key={headerGroup.id}>
                        {headerGroup.headers.map((header) => (
                            <TableHead key={header.id} className="px-5 py-3.5 text-xs font-semibold tracking-wider uppercase text-muted-foreground bg-muted">
                                {header.isPlaceholder
                                    ? null
                                    : flexRender(header.column.columnDef.header, header.getContext())}
                            </TableHead>
                        ))}
                    </TableRow>
                ))}
            </TableHeader>
            <TableBody>
                {table.getRowModel().rows.map((row) => (
                    <TableRow key={row.id} className="border-b-border/60 hover:bg-muted/60">
                        {row.getVisibleCells().map((cell) => (
                            <TableCell key={cell.id} className="px-5 py-4 text-sm">
                                {flexRender(cell.column.columnDef.cell, cell.getContext())}
                            </TableCell>
                        ))}
                    </TableRow>
                ))}
            </TableBody>
        </Table>
    );
}
