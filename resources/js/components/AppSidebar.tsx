import { usePage, router } from '@inertiajs/react';
import { Home, FileText, Tags, User, Database, LogOut } from 'lucide-react';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarGroup,
    SidebarGroupContent,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
    SidebarSeparator,
} from '@/components/ui/sidebar';

const navItems = [
    { label: 'Overview', href: '/', route: 'dashboard', icon: Home },
    { label: 'Transactions', href: '/transactions', route: 'transactions.*', icon: FileText },
    { label: 'Categories', href: '/categories', route: 'categories.*', icon: Tags },
];

const bottomItems = [
    { label: 'Profile', href: '/profile', route: 'profile.*', icon: User },
    { label: 'Backup', href: '/backup', route: 'backup.*', icon: Database },
];

function isActive(pattern: string, url: string) {
    if (pattern === 'dashboard') return url === '/';
    if (pattern === 'transactions.*') return url.startsWith('/transactions');
    if (pattern === 'profile.*') return url.startsWith('/profile');
    if (pattern === 'backup.*') return url.startsWith('/backup');
    if (pattern === 'categories.*') return url.startsWith('/categories');
    return false;
}

export function AppSidebar() {
    const url = usePage().url;

    const handleClick = (e: React.MouseEvent, href: string) => {
        e.preventDefault();
        router.visit(href);
    };

    const handleLogout = (e: React.MouseEvent) => {
        e.preventDefault();
        router.post('/logout');
    };

    return (
        <Sidebar collapsible="icon">
            <SidebarHeader>
                <SidebarMenu>
                    <SidebarMenuItem>
                        <SidebarMenuButton size="lg">
                            <div className="flex aspect-square size-8 items-center justify-center rounded-lg bg-primary text-primary-foreground">
                                <svg className="size-4" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 14.93V18h-2v-1.07A4.01 4.01 0 0 1 8 13h2c0 1.1.9 2 2 2s2-.9 2-2c0-1.1-.9-2-2-2a4 4 0 0 1 0-8V6h2v-.07A4.01 4.01 0 0 1 16 9h-2c0-1.1-.9-2-2-2s-2 .9-2 2 .9 2 2 2a4 4 0 0 1 0 8z" />
                                </svg>
                            </div>
                            <div className="grid flex-1 text-left text-sm leading-tight">
                                <span className="truncate font-semibold">Payroll</span>
                                <span className="truncate text-xs text-muted-foreground">Salary Tracker</span>
                            </div>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarHeader>

            <SidebarSeparator />

            <SidebarContent>
                <SidebarGroup>
                    <SidebarGroupContent>
                        <SidebarMenu>
                            {navItems.map((item) => (
                                <SidebarMenuItem key={item.href}>
                                    <SidebarMenuButton
                                        isActive={isActive(item.route, url)}
                                        tooltip={item.label}
                                        onClick={(e) => handleClick(e as unknown as React.MouseEvent, item.href)}
                                    >
                                        <item.icon />
                                        <span>{item.label}</span>
                                    </SidebarMenuButton>
                                </SidebarMenuItem>
                            ))}
                        </SidebarMenu>
                    </SidebarGroupContent>
                </SidebarGroup>
            </SidebarContent>

            <SidebarSeparator />

            <SidebarFooter>
                <SidebarMenu>
                    {bottomItems.map((item) => (
                        <SidebarMenuItem key={item.href}>
                            <SidebarMenuButton
                                isActive={isActive(item.route, url)}
                                tooltip={item.label}
                                onClick={(e) => handleClick(e as unknown as React.MouseEvent, item.href)}
                            >
                                <item.icon />
                                <span>{item.label}</span>
                            </SidebarMenuButton>
                        </SidebarMenuItem>
                    ))}
                    <SidebarMenuItem>
                        <SidebarMenuButton tooltip="Log out" onClick={handleLogout}>
                            <LogOut />
                            <span>Log out</span>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarFooter>
        </Sidebar>
    );
}
