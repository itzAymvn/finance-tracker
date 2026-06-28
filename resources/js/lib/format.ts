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
    const abs = Math.abs(diff);
    const unit = abs < 60 ? 'just now'
        : abs < 3600 ? `${Math.floor(abs / 60)}m`
        : abs < 86400 ? `${Math.floor(abs / 3600)}h`
        : abs < 2592000 ? `${Math.floor(abs / 86400)}d`
        : null;
    if (unit === null) return formatDate(new Date(timestamp * 1000).toISOString());
    if (unit === 'just now') return 'just now';
    return diff > 0 ? `${unit} ago` : `in ${unit}`;
}

export function monthKeyToLabel(monthKey: string): string {
    const [year, month] = monthKey.split('-');
    const d = new Date(parseInt(year), parseInt(month) - 1, 1);
    return d.toLocaleDateString('en-US', { month: 'long', year: 'numeric' });
}

export function formatCountdown(ms: number): string {
    const sign = ms < 0 ? '-' : '';
    let s = Math.floor(Math.abs(ms) / 1000);
    const d = Math.floor(s / 86400);
    s -= d * 86400;
    const h = Math.floor(s / 3600);
    s -= h * 3600;
    const m = Math.floor(s / 60);
    s -= m * 60;

    if (d > 0) return `${sign}${d}d ${String(h).padStart(2, '0')}h ${String(m).padStart(2, '0')}m`;
    if (h > 0) return `${sign}${h}h ${String(m).padStart(2, '0')}m ${String(s).padStart(2, '0')}s`;
    if (m > 0) return `${sign}${m}m ${String(s).padStart(2, '0')}s`;
    return `${sign}${s}s`;
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
