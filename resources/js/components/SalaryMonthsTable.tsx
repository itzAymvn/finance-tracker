import { Link } from '@inertiajs/react';
import { createColumnHelper, useReactTable, getCoreRowModel, flexRender } from '@tanstack/react-table';
import type { SalaryMonth } from '@/lib/types';
import { formatMoneyInteger, statusBadgeClass } from '@/lib/format';
import { Tooltip, TooltipTrigger, TooltipContent } from '@/components/ui/tooltip';
import { Button } from '@/components/ui/button';
import { Table, TableHeader, TableBody, TableRow, TableHead, TableCell } from '@/components/ui/table';

const columnHelper = createColumnHelper<SalaryMonth>();

const columns = [
    columnHelper.accessor('label', {
        header: 'Month',
        cell: (info) => (
            <Link
                href={`/salary-months/${info.row.original.id}`}
                className="font-medium text-foreground dark:text-white hover:text-primary no-underline"
            >
                {info.getValue()}
            </Link>
        ),
    }),
    columnHelper.accessor('expected_salary', {
        header: () => <div className="text-right">Expected</div>,
        cell: (info) => (
            <div className="text-right">
                <span className="font-mono font-medium text-foreground dark:text-white">
                    {formatMoneyInteger(info.getValue(), info.row.original.currency)}
                </span>
                <span className="text-xs text-muted-foreground dark:text-slate-400 ml-1 uppercase">
                    {info.row.original.currency}
                </span>
            </div>
        ),
    }),
    columnHelper.accessor('total_paid', {
        header: () => <div className="text-right">Paid</div>,
        cell: (info) => (
            <div className={`text-right font-mono font-semibold ${info.getValue() > 0 ? 'text-emerald-600 dark:text-emerald-600-400' : 'text-muted-foreground dark:text-slate-400'}`}>
                {formatMoneyInteger(info.getValue())}
            </div>
        ),
    }),
    columnHelper.accessor('progress_percent', {
        header: 'Progress',
        cell: (info) => {
            const pct = info.getValue();
            const status = info.row.original.status;
            const barColor = status === 'paid' || status === 'overpaid'
                ? 'bg-emerald'
                : pct >= 50
                    ? 'bg-primary'
                    : 'bg-slate-300 dark:bg-slate-600';
            return (
                <div className="flex items-center gap-2">
                    <div className="flex-1 h-2 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
                        <div
                            className={`h-full rounded-full transition-all duration-500 ${barColor}`}
                            style={{ width: `${Math.min(pct, 100)}%` }}
                        />
                    </div>
                    <span className="font-mono text-xs text-muted-foreground dark:text-slate-400 w-8 text-right">{pct}%</span>
                </div>
            );
        },
    }),
    columnHelper.accessor('status', {
        header: () => <div className="text-center">Status</div>,
        cell: (info) => {
            const s = info.getValue();
            const cs = info.row.original.cumulative_status;
            const badgeClass = statusBadgeClass(s);
            return (
                <div className="flex items-center justify-center gap-1.5">
                    <span className={badgeClass}>{s.charAt(0).toUpperCase() + s.slice(1)}</span>
                    {cs !== s && (
                        <Tooltip>
                            <TooltipTrigger>
                                <span className={`inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium border ${
                                    cs === 'paid' ? 'border-emerald/30 text-emerald-600 dark:text-emerald-600-400' :
                                    cs === 'overpaid' ? 'border-sapphire/30 text-blue-600' :
                                    cs === 'partial' ? 'border-primary/30 text-primary' :
                                    'border-border dark:border-border text-muted-foreground dark:text-slate-400'
                                }`}>
                                    cum: {cs.charAt(0).toUpperCase() + cs.slice(1)}
                                </span>
                            </TooltipTrigger>
                            <TooltipContent>
                                Cumulative through {info.row.original.month_key}: {info.row.original.cumulative_paid.toFixed(2)} / {info.row.original.cumulative_due.toFixed(2)} (FIFO rollover)
                            </TooltipContent>
                        </Tooltip>
                    )}
                </div>
            );
        },
    }),
    columnHelper.display({
        id: 'actions',
        header: () => <div className="text-right"></div>,
        cell: (info) => (
            <div className="text-right">
                <Button variant="ghost" size="sm" render={<Link href={`/salary-months/${info.row.original.id}`} />}>
                    View
                </Button>
            </div>
        ),
    }),
];

interface SalaryMonthsTableProps {
    months: SalaryMonth[];
}

export function SalaryMonthsTable({ months }: SalaryMonthsTableProps) {
    const table = useReactTable({
        data: months,
        columns,
        getCoreRowModel: getCoreRowModel(),
    });

    if (months.length === 0) {
        return (
            <div className="text-center py-16 px-6">
                <div className="w-14 h-14 mx-auto mb-4 rounded-xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-2xl opacity-50">📅</div>
                <p className="text-sm text-muted-foreground dark:text-slate-400 mb-4">No salary months yet.</p>
                <Button render={<Link href="/salary-months/create" />}>Create first month</Button>
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
