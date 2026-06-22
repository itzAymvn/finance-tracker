export function formatMoney(amount: number | string, currency = 'MAD'): string {
    const n = typeof amount === 'string' ? parseFloat(amount) : amount;
    if (isNaN(n)) return '0.00';
    const formatted = new Intl.NumberFormat('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    }).format(Math.abs(n));
    return `${formatted} ${currency}`;
}

export function formatMoneyInteger(amount: number | string, currency = 'MAD'): string {
    const n = typeof amount === 'string' ? parseFloat(amount) : amount;
    if (isNaN(n)) return '0';
    const formatted = new Intl.NumberFormat('en-US', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
    }).format(Math.abs(n));
    return `${formatted} ${currency}`;
}

export function formatDate(date: string, style: 'short' | 'medium' | 'long' = 'medium'): string {
    const d = new Date(date);
    if (isNaN(d.getTime())) return date;
    const options: Intl.DateTimeFormatOptions =
        style === 'short'
            ? { day: '2-digit', month: '2-digit', year: 'numeric' }
            : style === 'medium'
            ? { day: 'numeric', month: 'short', year: 'numeric' }
            : { day: 'numeric', month: 'long', year: 'numeric' };
    return d.toLocaleDateString('en-GB', options);
}

export function formatDateTime(date: string): string {
    const d = new Date(date);
    if (isNaN(d.getTime())) return date;
    return d.toLocaleDateString('en-GB', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
}

export function formatSize(bytes: number): string {
    if (bytes >= 1048576) return `${(bytes / 1048576).toFixed(1)} MB`;
    if (bytes >= 1024) return `${(bytes / 1024).toFixed(1)} KB`;
    return `${bytes} B`;
}

export function relativeTime(timestamp: number): string {
    const now = Date.now() / 1000;
    const diff = now - timestamp;
    if (diff < 60) return 'just now';
    if (diff < 3600) return `${Math.floor(diff / 60)}m ago`;
    if (diff < 86400) return `${Math.floor(diff / 3600)}h ago`;
    if (diff < 2592000) return `${Math.floor(diff / 86400)}d ago`;
    return formatDate(new Date(timestamp * 1000).toISOString());
}

export function monthKeyToLabel(monthKey: string): string {
    const [year, month] = monthKey.split('-');
    const d = new Date(parseInt(year), parseInt(month) - 1, 1);
    return d.toLocaleDateString('en-US', { month: 'long', year: 'numeric' });
}

export function statusBadgeClass(status: string): string {
    switch (status) {
        case 'paid': return 'badge-emerald';
        case 'partial': return 'badge-amber';
        case 'overpaid': return 'badge-sapphire';
        default: return 'badge-slate';
    }
}

export function statusColor(status: string): string {
    switch (status) {
        case 'paid': return 'text-emerald-600 dark:text-emerald-400';
        case 'partial': return 'text-amber-600 dark:text-amber-400';
        case 'overpaid': return 'text-blue-600 dark:text-blue-400';
        default: return 'text-muted-foreground';
    }
}
