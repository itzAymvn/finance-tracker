import { useEffect, useState } from 'react';

/**
 * Returns the milliseconds remaining until `target`, re-rendering at a
 * cadence that matches the displayed precision: every second when under
 * a day (seconds shown), otherwise every minute. Returns null when
 * `target` is null/invalid.
 */
export function useCountdown(target: string | null): number | null {
    const [now, setNow] = useState(() => Date.now());

    const remaining = target ? new Date(target).getTime() - now : null;
    const intervalMs = remaining !== null && remaining < 86_400_000 ? 1000 : 60_000;

    useEffect(() => {
        if (!target) return;
        const id = setInterval(() => setNow(Date.now()), intervalMs);
        return () => clearInterval(id);
    }, [target, intervalMs]);

    return remaining === null || Number.isNaN(remaining) ? null : remaining;
}
