<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use App\Models\Category;
use App\Models\Transaction;
use App\Services\AllocationService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TransactionController extends Controller
{
    public function __construct(private readonly AllocationService $allocationService) {}

    public function index(Request $request)
    {
        $query = Transaction::with('allocations.salaryMonth', 'category')->orderBy('paid_at', 'desc');

        if ($search = trim((string) $request->query('search', ''))) {
            $query->where('label', 'like', '%'.$search.'%');
        }
        if ($year = $request->query('year')) {
            $query->whereRaw("strftime('%Y', paid_at) = ?", [(string) $year]);
        }
        if ($month = $request->query('month')) {
            $query->whereRaw("strftime('%Y-%m', paid_at) = ?", [(string) $month]);
        }
        if ($type = $request->query('type')) {
            match ($type) {
                'credit' => $query->where('amount', '>', 0),
                'debit'  => $query->where('amount', '<', 0),
                'salary' => $query->where('is_salary', true),
                default  => null,
            };
        }
        if ($category = $request->query('category')) {
            if ($category === 'null') {
                $query->whereNull('category_id');
            } else {
                $query->where('category_id', (int) $category);
            }
        }

        // Snapshot of the filtered set for the summary strip (without pagination).
        $clone = clone $query;
        $summary = [
            'count'   => (int) $clone->count(),
            'credits' => (float) (clone $query)->where('amount', '>', 0)->sum('amount'),
            'debits'  => (float) (clone $query)->where('amount', '<', 0)->sum('amount'),
        ];
        $summary['net'] = $summary['credits'] + $summary['debits'];

        $transactions = $query->paginate(50)->withQueryString();

        $years = Transaction::selectRaw("DISTINCT strftime('%Y', paid_at) as y")
            ->orderBy('y', 'desc')
            ->pluck('y');

        return Inertia::render('Transactions/Index', [
            'transactions' => $transactions->through(fn ($tx) => [
                'id' => $tx->id,
                'paid_at' => $tx->paid_at->toIso8601String(),
                'value_date' => $tx->value_date?->toIso8601String(),
                'label' => $tx->label,
                'amount' => $tx->amount,
                'source' => $tx->source,
                'is_salary' => $tx->is_salary,
                'category' => $tx->category ? [
                    'id' => $tx->category->id,
                    'name' => $tx->category->name,
                    'icon' => $tx->category->icon,
                ] : null,
                'allocations' => $tx->allocations->map(fn ($a) => [
                    'id' => $a->id,
                    'amount' => $a->amount,
                    'salary_month' => $a->salaryMonth ? [
                        'id' => $a->salaryMonth->id,
                        'month_key' => $a->salaryMonth->month_key,
                        'label' => $a->salaryMonth->label,
                    ] : null,
                ])->toArray(),
                'allocated_total' => $tx->allocated_total,
                'unallocated' => $tx->unallocated,
            ])->toArray(),
            'years' => $years,
            'summary' => $summary,
            'categories' => Category::orderBy('name')->get(['id', 'name', 'icon']),
        ]);
    }

    public function create()
    {
        return Inertia::render('Transactions/Create', [
            'categories' => Category::orderBy('name')->get(['id', 'name', 'icon']),
        ]);
    }

    public function store(StoreTransactionRequest $request)
    {
        $data = $request->validated();
        $isSalary = (bool) ($data['is_salary'] ?? false);

        $valueDate = $data['value_date'] ?? null;

        $transaction = Transaction::create([
            'paid_at'      => $data['paid_at'].' 12:00:00',
            'value_date'   => $valueDate ? $valueDate.' 12:00:00' : null,
            'label'        => $data['label'],
            'amount'       => $data['amount'],
            'source'       => 'manuel',
            'is_salary'    => $isSalary,
            'category_id'  => $data['category_id'] ?? null,
        ]);

        if ($isSalary) {
            $this->allocationService->reallocate($transaction);
        }

        return redirect()
            ->route('transactions.index')
            ->with('success', 'Transaction created.');
    }

    public function edit(Transaction $transaction)
    {
        $transaction->load('allocations.salaryMonth', 'category');

        return Inertia::render('Transactions/Edit', [
            'transaction' => [
                'id' => $transaction->id,
                'paid_at' => $transaction->paid_at->toIso8601String(),
                'value_date' => $transaction->value_date?->toIso8601String(),
                'label' => $transaction->label,
                'amount' => $transaction->amount,
                'source' => $transaction->source,
                'is_salary' => $transaction->is_salary,
                'category_id' => $transaction->category_id,
                'category' => $transaction->category ? [
                    'id' => $transaction->category->id,
                    'name' => $transaction->category->name,
                    'icon' => $transaction->category->icon,
                ] : null,
                'allocations' => $transaction->allocations->map(fn ($a) => [
                    'id' => $a->id,
                    'amount' => $a->amount,
                    'salary_month' => $a->salaryMonth ? [
                        'id' => $a->salaryMonth->id,
                        'month_key' => $a->salaryMonth->month_key,
                        'label' => $a->salaryMonth->label,
                    ] : null,
                ])->toArray(),
                'allocated_total' => $transaction->allocated_total,
                'unallocated' => $transaction->unallocated,
            ],
            'categories' => Category::orderBy('name')->get(['id', 'name', 'icon']),
        ]);
    }

    public function update(UpdateTransactionRequest $request, Transaction $transaction)
    {
        $transaction->is_salary = $request->boolean('is_salary');
        $transaction->label = $request->input('label');
        $transaction->category_id = $request->input('category_id');
        $transaction->save();

        $this->allocationService->reallocate($transaction);

        return redirect()
            ->route('transactions.index', ['page' => $request->query('page')])
            ->with('success', 'Transaction updated.');
    }

    public function destroy(Transaction $transaction)
    {
        $transaction->delete();

        return redirect()->route('transactions.index')->with('success', 'Transaction deleted.');
    }
}
