import { usePage } from '@inertiajs/react';
import { Plus, Sun, Moon, CalendarDays, CalendarRange } from 'lucide-react';
import { useTheme } from '@/contexts/ThemeContext';
import type { PageProps } from '@/lib/types';
import { DropdownMenu, DropdownMenuTrigger, DropdownMenuContent, DropdownMenuItem } from '@/components/ui/dropdown-menu';
import { Button } from '@/components/ui/button';
import { SidebarTrigger } from '@/components/ui/sidebar';
import { Separator } from '@/components/ui/separator';

interface TopBarProps {
    onCreateTransaction?: () => void;
    onCreateMonth?: () => void;
    onCreatePeriod?: () => void;
}

export function TopBar({ onCreateTransaction, onCreateMonth, onCreatePeriod }: TopBarProps) {
    const { isDark, toggle } = useTheme();
    const { auth } = usePage<PageProps>().props;

    const initials = auth.user?.name ? auth.user.name.substring(0, 2).toUpperCase() : '??';

    return (
        <header className="flex h-16 shrink-0 items-center gap-2 border-b px-4 transition-[width,height] ease-linear group-has-data-[collapsible=icon]/sidebar-wrapper:h-12">
            <div className="flex items-center gap-2">
                <SidebarTrigger className="-ml-1" />
                <Separator orientation="vertical" className="mr-2 data-[orientation=vertical]:h-4" />
            </div>

            <div className="flex-1" />

            <div className="flex items-center gap-2">
                <Button
                    variant="ghost"
                    size="icon"
                    onClick={toggle}
                    title="Toggle dark mode"
                >
                    {isDark ? <Sun className="w-4 h-4" /> : <Moon className="w-4 h-4" />}
                </Button>

                <DropdownMenu>
                    <DropdownMenuTrigger
                        render={<Button variant="outline" size="sm" />}
                    >
                        <Plus className="w-3.5 h-3.5" />
                        Month
                    </DropdownMenuTrigger>
                    <DropdownMenuContent align="end" sideOffset={4}>
                        <DropdownMenuItem onClick={onCreateMonth}>
                            <CalendarDays className="w-4 h-4" />
                            Single Month
                        </DropdownMenuItem>
                        <DropdownMenuItem onClick={onCreatePeriod}>
                            <CalendarRange className="w-4 h-4" />
                            Create Period
                        </DropdownMenuItem>
                    </DropdownMenuContent>
                </DropdownMenu>

                <Button
                    size="sm"
                    onClick={onCreateTransaction}
                >
                    <Plus className="w-3.5 h-3.5" />
                    New
                </Button>

                <Separator orientation="vertical" className="data-[orientation=vertical]:h-4" />

                <div className="flex items-center gap-2">
                    <div className="w-7 h-7 rounded-full bg-primary/10 text-primary flex items-center justify-center text-xs font-semibold">
                        {initials}
                    </div>
                    <span className="hidden sm:block text-sm font-medium text-foreground">
                        {auth.user?.name}
                    </span>
                </div>
            </div>
        </header>
    );
}
