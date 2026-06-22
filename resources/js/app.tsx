import '../css/app.css';
import { createInertiaApp } from '@inertiajs/react';
import { createRoot } from 'react-dom/client';

const pages = import.meta.glob('./Pages/**/*.tsx');

createInertiaApp({
    title: (title) => title ? `${title} — Payroll` : 'Payroll',
    // @ts-expect-error — Vite glob types don't match Inertia's resolver signature
    resolve: (name) => {
        const page = pages[`./Pages/${name}.tsx`];
        if (!page) throw new Error(`Page not found: ./Pages/${name}.tsx`);
        return page();
    },
    setup({ el, App, props }) {
        const root = createRoot(el);
        root.render(<App {...props} />);
    },
    progress: {
        color: '#6366f1',
    },
});
