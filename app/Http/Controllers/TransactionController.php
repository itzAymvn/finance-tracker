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
                'debit' => $query->where('amount', '<', 0),
                'salary' => $query->whereHas('category', fn ($cq) => $cq->where('is_salary', true)),
                default => null,
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
            'count' => (int) $clone->count(),
            'credits' => (float) (clone $query)->where('amount', '>', 0)->sum('amount'),
            'debits' => (float) (clone $query)->where('amount', '<', 0)->sum('amount'),
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
                'details' => $tx->details,
                'amount' => $tx->amount,
                'source' => $tx->source,
                'category_id' => $tx->category_id,
                'category' => $tx->category ? [
                    'id' => $tx->category->id,
                    'name' => $tx->category->name,
                    'icon' => $tx->category->icon,
                    'is_salary' => $tx->category->is_salary,
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
            'categories' => Category::orderBy('name')->get(['id', 'name', 'icon', 'is_salary']),
        ]);
    }

    public function create()
    {
        return Inertia::render('Transactions/Create', [
            'categories' => Category::orderBy('name')->get(['id', 'name', 'icon', 'is_salary']),
        ]);
    }

    public function store(StoreTransactionRequest $request)
    {
        $data = $request->validated();

        $valueDate = $data['value_date'] ?? null;

        $transaction = Transaction::create([
            'paid_at' => $data['paid_at'].' 12:00:00',
            'value_date' => $valueDate ? $valueDate.' 12:00:00' : null,
            'label' => $data['label'],
            'details' => $data['details'] ?? null,
            'amount' => $data['amount'],
            'source' => 'manuel',
            'category_id' => $data['category_id'] ?? null,
        ]);

        $transaction->load('category');
        if ($transaction->isSalary()) {
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
                'details' => $transaction->details,
                'amount' => $transaction->amount,
                'source' => $transaction->source,
                'category_id' => $transaction->category_id,
                'category' => $transaction->category ? [
                    'id' => $transaction->category->id,
                    'name' => $transaction->category->name,
                    'icon' => $transaction->category->icon,
                    'is_salary' => $transaction->category->is_salary,
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
            'categories' => Category::orderBy('name')->get(['id', 'name', 'icon', 'is_salary']),
        ]);
    }

    public function update(UpdateTransactionRequest $request, Transaction $transaction)
    {
        $transaction->label = $request->input('label');
        $transaction->details = $request->input('details');
        $transaction->category_id = $request->input('category_id');

        if ($request->has('amount')) {
            $sign = (float) $transaction->amount >= 0 ? 1 : -1;
            $transaction->amount = round(abs((float) $request->input('amount')) * $sign, 2);
        }

        $transaction->save();

        $transaction->load('category');
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
