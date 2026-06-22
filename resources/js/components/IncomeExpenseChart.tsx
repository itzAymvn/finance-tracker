import { BarChart, Bar, XAxis, YAxis, CartesianGrid, Tooltip, ResponsiveContainer, Legend } from 'recharts';
import type { MonthlyChartData } from '@/lib/types';
import { formatMoney } from '@/lib/format';
import { Card, CardContent } from '@/components/ui/card';

interface IncomeExpenseChartProps {
    data: MonthlyChartData[];
}

function CustomTooltip({ active, payload, label }: { active?: boolean; payload?: Array<{ value: number; name: string }>; label?: string }) {
    if (!active || !payload) return null;
    return (
        <div className="bg-card border border-border rounded-lg shadow-lg p-3 text-xs">
            <p className="font-semibold text-foreground mb-2">{label}</p>
            {payload.map((entry, i) => (
                <div key={i} className="flex items-center justify-between gap-4">
                    <span className="flex items-center gap-1.5">
                        <span
                            className="w-2.5 h-2.5 rounded-full"
                            style={{ backgroundColor: entry.name === 'income' ? '#10b981' : '#ef4444' }}
                        />
                        <span className="text-muted-foreground capitalize">{entry.name}</span>
                    </span>
                    <span className="font-mono font-medium text-foreground">{formatMoney(entry.value)}</span>
                </div>
            ))}
        </div>
    );
}

export function IncomeExpenseChart({ data }: IncomeExpenseChartProps) {
    if (data.length === 0) {
        return (
            <Card>
                <CardContent>
                    <p className="text-xs font-semibold tracking-wider uppercase text-muted-foreground mb-4">Income vs Expense</p>
                    <p className="text-sm text-muted-foreground text-center py-8">No transaction data available.</p>
                </CardContent>
            </Card>
        );
    }

    return (
        <Card>
            <CardContent>
                <p className="text-xs font-semibold tracking-wider uppercase text-muted-foreground mb-4">Income vs Expense</p>
                <div className="h-64">
                    <ResponsiveContainer width="100%" height="100%">
                        <BarChart data={data} margin={{ top: 5, right: 5, left: 0, bottom: 5 }}>
                            <CartesianGrid strokeDasharray="3 3" className="stroke-border" />
                            <XAxis
                                dataKey="label"
                                tick={{ fontSize: 11 }}
                                className="text-muted-foreground"
                                tickLine={false}
                                axisLine={false}
                            />
                            <YAxis
                                tick={{ fontSize: 11 }}
                                className="text-muted-foreground"
                                tickLine={false}
                                axisLine={false}
                                tickFormatter={(v) => `${(v / 1000).toFixed(0)}k`}
                            />
                            <Tooltip content={<CustomTooltip />} />
                            <Legend
                                wrapperStyle={{ fontSize: 12 }}
                                formatter={(value) => <span className="text-muted-foreground capitalize">{value}</span>}
                            />
                            <Bar dataKey="income" fill="#10b981" radius={[4, 4, 0, 0]} />
                            <Bar dataKey="expense" fill="#ef4444" radius={[4, 4, 0, 0]} />
                        </BarChart>
                    </ResponsiveContainer>
                </div>
            </CardContent>
        </Card>
    );
}
