export interface Category {
    id: number;
    name: string;
    icon: string | null;
    created_at?: string;
    transaction_count?: number;
}

export interface SalaryMonth {
    id: number;
    month_key: string;
    expected_salary: string;
    currency: string;
    notes: string | null;
    created_at: string;
    updated_at: string;
    // Computed (appended)
    label: string;
    total_paid: number;
    remaining: number;
    status: 'paid' | 'partial' | 'unpaid' | 'overpaid';
    progress_percent: number;
    cumulative_paid: number;
    cumulative_due: number;
    cumulative_remaining: number;
    cumulative_status: 'paid' | 'partial' | 'unpaid' | 'overpaid';
    cumulative_progress_percent: number;
}

export interface Transaction {
    id: number;
    paid_at: string;
    value_date: string | null;
    label: string;
    amount: string;
    source: string;
    is_salary: boolean;
    category_id?: number | null;
    category?: Category | null;
    salary_month_id: number | null;
    raw: unknown;
    created_at: string;
    updated_at: string;
    allocations?: SalaryAllocation[];
    allocated_total?: number;
    unallocated?: number;
}

export interface SalaryAllocation {
    id: number;
    transaction_id: number;
    salary_month_id: number;
    amount: string;
    salary_month?: SalaryMonth;
    transaction?: Transaction;
}

export interface Backup {
    name: string;
    kind: 'auto' | 'manual';
    size: number;
    last_modified: number;
}

export interface BackupSettings {
    backup_enabled: boolean;
    backup_interval_hours: number;
}

export interface User {
    id: number;
    name: string;
    email: string;
}

export interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

export interface PaginatedData<T> {
    data: T[];
    current_page: number;
    first_page_url: string;
    from: number;
    last_page: number;
    last_page_url: string;
    links: PaginationLink[];
    next_page_url: string | null;
    path: string;
    per_page: number;
    prev_page_url: string | null;
    to: number;
    total: number;
}

export interface PageProps {
    auth: {
        user: User | null;
    };
    flash: {
        success: string | null;
        error: string | null;
        status: string | null;
    };
    errors: Record<string, string[]>;
    [key: string]: unknown;
}

export interface MonthlyChartData {
    month: string;
    label: string;
    income: number;
    expense: number;
}

export interface CategoryChartData {
    name: string;
    value: number;
    icon: string | null;
}

export interface DashboardProps extends PageProps {
    months: SalaryMonth[];
    totalExpected: number;
    totalPaid: number;
    totalRemaining: number;
    currentMonthKey: string;
    toDateExpected: number;
    toDatePaid: number;
    toDateRemaining: number;
    toDateLabel: string;
    years: string[];
    currentBalance: number;
    monthlyChart: MonthlyChartData[];
    categoryChart: CategoryChartData[];
}

export interface TransactionsIndexProps extends PageProps {
    transactions: PaginatedData<Transaction>;
    years: string[];
    summary: {
        count: number;
        credits: number;
        debits: number;
        net: number;
    };
    categories: Category[];
}

export interface SalaryMonthShowProps extends PageProps {
    salaryMonth: SalaryMonth;
    allocations: (SalaryAllocation & { transaction: Transaction })[];
}

export interface BackupIndexProps extends PageProps {
    backups: Backup[];
    settings: BackupSettings;
    nextRun: string | null;
}

export interface ProfileEditProps extends PageProps {
    user: User;
}

export interface CategoriesIndexProps extends PageProps {
    categories: Category[];
}
