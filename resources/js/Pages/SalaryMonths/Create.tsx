
import { Link } from '@inertiajs/react';
import { ArrowLeft } from 'lucide-react';
import { AppLayout } from '@/components/AppLayout';
import { SalaryMonthForm } from '@/components/SalaryMonthForm';
import { SalaryPeriodForm } from '@/components/SalaryPeriodForm';
import { Tabs, TabsList, TabsTrigger, TabsContent } from '@/components/ui/tabs';
import { Button } from '@/components/ui/button';

export default function SalaryMonthsCreate() {
    const url = new URL(window.location.href);
    const defaultTab = url.searchParams.get('tab') === 'period' ? 'period' : 'single';

    return (
        <div className="max-w-xl mx-auto">
            <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
                <div>
                    <h1 className="text-2xl font-bold text-foreground">Add Month</h1>
                    <p className="text-sm text-muted-foreground mt-0.5">Create a new salary month or a period of months</p>
                </div>
                <Button variant="outline" render={<Link href="/dashboard" />}>
                    <ArrowLeft className="w-4 h-4" />
                    Back
                </Button>
            </div>

            <div className="bg-card dark:bg-card rounded-xl border border-border dark:border-border shadow-sm overflow-hidden">
                <Tabs defaultValue={defaultTab}>
                    <div className="border-b border-border dark:border-border px-3 pt-3">
                        <TabsList>
                            <TabsTrigger value="single">Single Month</TabsTrigger>
                            <TabsTrigger value="period">Period</TabsTrigger>
                        </TabsList>
                    </div>
                    <TabsContent value="single">
                        <SalaryMonthForm />
                    </TabsContent>
                    <TabsContent value="period">
                        <SalaryPeriodForm />
                    </TabsContent>
                </Tabs>
            </div>
        </div>
    );
}

(SalaryMonthsCreate as any).layout = (page: React.ReactNode) => <AppLayout>{page}</AppLayout>;
