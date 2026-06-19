<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use App\Models\Transaction;
use App\Services\AllocationService;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function __construct(private readonly AllocationService $allocationService) {}

    public function index(Request $request)
    {
        $query = Transaction::with('allocations.salaryMonth')->orderBy('paid_at', 'desc');

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

        return view('transactions.index', compact('transactions', 'years', 'summary'));
    }

    public function create()
    {
        return view('transactions.create');
    }

    public function store(StoreTransactionRequest $request)
    {
        $data = $request->validated();
        $isSalary = (bool) ($data['is_salary'] ?? false);

        $valueDate = $data['value_date'] ?? null;

        $transaction = Transaction::create([
            'paid_at'    => $data['paid_at'].' 12:00:00',
            'value_date' => $valueDate ? $valueDate.' 12:00:00' : null,
            'label'      => $data['label'],
            'amount'     => $data['amount'],
            'source'     => 'manuel',
            'is_salary'  => $isSalary,
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
        $transaction->load('allocations.salaryMonth');

        return view('transactions.edit', compact('transaction'));
    }

    public function update(UpdateTransactionRequest $request, Transaction $transaction)
    {
        $transaction->is_salary = $request->boolean('is_salary');
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
