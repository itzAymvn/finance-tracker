import { PieChart, Pie, Cell, ResponsiveContainer, Tooltip, Legend } from 'recharts';
import type { CategoryChartData } from '@/lib/types';
import { getCategoryIcon } from '@/lib/icons';
import { formatMoney } from '@/lib/format';
import { Card, CardContent } from '@/components/ui/card';

interface CategoryPieChartProps {
    data: CategoryChartData[];
}

const COLORS = [
    '#6366f1', // indigo
    '#f59e0b', // amber
    '#10b981', // emerald
    '#ef4444', // red
    '#8b5cf6', // violet
    '#06b6d4', // cyan
    '#f97316', // orange
    '#ec4899', // pink
    '#14b8a6', // teal
];

function CustomTooltip({ active, payload }: { active?: boolean; payload?: Array<{ name: string; value: number; payload: { name: string } }> }) {
    if (!active || !payload || !payload.length) return null;
    const item = payload[0];
    return (
        <div className="bg-card border border-border rounded-lg shadow-lg p-3 text-xs">
            <p className="font-semibold text-foreground mb-1">{item.name}</p>
            <p className="font-mono text-foreground">{formatMoney(item.value)}</p>
        </div>
    );
}

function CustomLegend({ payload }: { payload?: Array<{ value: string; color: string; payload: CategoryChartData }> }) {
    if (!payload) return null;
    return (
        <div className="flex flex-wrap gap-x-4 gap-y-1.5 justify-center mt-2">
            {payload.map((entry, i) => {
                const IconComp = getCategoryIcon(entry.payload?.icon);
                return (
                    <div key={i} className="flex items-center gap-1.5 text-xs text-muted-foreground">
                        <span className="w-2.5 h-2.5 rounded-full" style={{ backgroundColor: entry.color }} />
                        <IconComp className="w-3 h-3" />
                        <span>{entry.value}</span>
                    </div>
                );
            })}
        </div>
    );
}

export function CategoryPieChart({ data }: CategoryPieChartProps) {
    if (data.length === 0) {
        return (
            <Card>
                <CardContent>
                    <p className="text-xs font-semibold tracking-wider uppercase text-muted-foreground mb-4">Expenses by Category</p>
                    <p className="text-sm text-muted-foreground text-center py-8">No expense data available.</p>
                </CardContent>
            </Card>
        );
    }

    return (
        <Card>
            <CardContent>
                <p className="text-xs font-semibold tracking-wider uppercase text-muted-foreground mb-4">Expenses by Category</p>
                <div className="h-64">
                    <ResponsiveContainer width="100%" height="100%">
                        <PieChart>
                            <Pie
                                data={data}
                                cx="50%"
                                cy="50%"
                                innerRadius={50}
                                outerRadius={85}
                                paddingAngle={3}
                                dataKey="value"
                            >
                                {data.map((_, i) => (
                                    <Cell key={i} fill={COLORS[i % COLORS.length]} />
                                ))}
                            </Pie>
                            <Tooltip content={<CustomTooltip />} />
                            <Legend content={<CustomLegend />} />
                        </PieChart>
                    </ResponsiveContainer>
                </div>
            </CardContent>
        </Card>
    );
}
