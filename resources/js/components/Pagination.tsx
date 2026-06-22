import { router } from '@inertiajs/react';
import type { PaginationLink } from '@/lib/types';

interface PaginationProps {
    links: PaginationLink[];
}

export function Pagination({ links }: PaginationProps) {
    if (!links || links.length <= 3) return null;

    return (
        <div className="px-5 py-4 border-t border-border dark:border-border bg-muted/50 dark:bg-muted/50">
            <nav className="flex items-center justify-center gap-1">
                {links.map((link, i) => {
                    const isDisabled = !link.url;
                    const isActive = link.active;

                    if (link.label === '...') {
                        return (
                            <span
                                key={i}
                                className="px-3 py-1.5 text-sm text-muted-foreground dark:text-slate-400"
                            >
                                ...
                            </span>
                        );
                    }

                    return (
                        <button
                            key={i}
                            disabled={isDisabled}
                            onClick={() => {
                                if (link.url) {
                                    router.get(link.url);
                                }
                            }}
                            className={`px-3 py-1.5 rounded-lg text-sm font-medium transition-colors ${
                                isActive
                                    ? 'bg-primary text-white shadow-sm'
                                    : isDisabled
                                        ? 'text-muted-foreground/40 dark:text-slate-600 cursor-not-allowed'
                                        : 'text-muted-foreground dark:text-slate-400 hover:text-foreground dark:hover:text-white hover:bg-muted dark:hover:bg-muted'
                            }`}
                            dangerouslySetInnerHTML={{ __html: link.label }}
                        />
                    );
                })}
            </nav>
        </div>
    );
}
