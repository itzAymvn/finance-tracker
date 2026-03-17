<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePayoutRequest;
use App\Http\Requests\UpdatePayoutRequest;
use App\Models\Payout;
use App\Models\SalaryMonth;
use App\Services\AllocationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;

class PayoutController extends Controller
{
    public function __construct(private AllocationService $allocationService) {}

    public function index(Request $request)
    {
        $query = Payout::with('allocations')->orderBy('paid_at', 'desc');

        // Filter by date range
        if ($from = $request->query('from')) {
            $query->whereDate('paid_at', '>=', $from);
        }
        if ($to = $request->query('to')) {
            $query->whereDate('paid_at', '<=', $to);
        }

        // Filter: only payouts with unallocated amount
        if ($request->boolean('has_unallocated')) {
            $query->whereRaw('amount > COALESCE((SELECT SUM(amount) FROM payout_allocations WHERE payout_allocations.payout_id = payouts.id), 0)');
        }

        // Filter: only payouts with attachment
        if ($request->query('has_attachment') === '1') {
            $query->whereNotNull('attachment_path')->where('attachment_path', '!=', '');
        }
        if ($request->query('has_attachment') === '0') {
            $query->where(function ($q) {
                $q->whereNull('attachment_path')->orWhere('attachment_path', '');
            });
        }

        $payouts = $query->paginate(20)->withQueryString();

        return view('payouts.index', compact('payouts'));
    }

    public function create()
    {
        $months = SalaryMonth::orderBy('month_key')->get();

        return view('payouts.create', compact('months'));
    }

    public function store(StorePayoutRequest $request)
    {
        $data = $request->safe()->except(['attachment', 'allocation_mode', 'allocations']);

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $path = $file->store('attachments', 'local');
            $data['attachment_path'] = $path;
            $data['attachment_name'] = $file->getClientOriginalName();
            $data['attachment_mime'] = $file->getMimeType();
        }

        try {
            DB::transaction(function () use ($data, $request) {
                $payout = Payout::create($data);

                if ($request->input('allocation_mode') === 'manual' && $request->filled('allocations')) {
                    $this->allocationService->manualAllocate($payout, $request->input('allocations'));
                } else {
                    $this->allocationService->autoAllocate($payout);
                }
            });
        } catch (InvalidArgumentException $e) {
            return back()->withInput()->withErrors(['allocation' => $e->getMessage()]);
        }

        return redirect()->route('payouts.index')->with('success', 'Payout recorded.');
    }

    public function show(Payout $payout)
    {
        $payout->load(['allocations.salaryMonth']);

        return view('payouts.show', compact('payout'));
    }

    public function edit(Payout $payout)
    {
        $payout->load('allocations');
        $months = SalaryMonth::orderBy('month_key')->get();

        return view('payouts.edit', compact('payout', 'months'));
    }

    public function update(UpdatePayoutRequest $request, Payout $payout)
    {
        $data = $request->safe()->except(['attachment', 'allocation_mode', 'allocations']);

        if ($request->hasFile('attachment')) {
            if ($payout->attachment_path) {
                Storage::disk('local')->delete($payout->attachment_path);
            }

            $file = $request->file('attachment');
            $path = $file->store('attachments', 'local');
            $data['attachment_path'] = $path;
            $data['attachment_name'] = $file->getClientOriginalName();
            $data['attachment_mime'] = $file->getMimeType();
        }

        try {
            DB::transaction(function () use ($data, $request, $payout) {
                $payout->update($data);

                if ($request->input('allocation_mode') === 'manual' && $request->filled('allocations')) {
                    $this->allocationService->manualAllocate($payout, $request->input('allocations'));
                } else {
                    $this->allocationService->autoAllocate($payout);
                }
            });
        } catch (InvalidArgumentException $e) {
            return back()->withInput()->withErrors(['allocation' => $e->getMessage()]);
        }

        return redirect()->route('payouts.show', $payout)->with('success', 'Payout updated.');
    }

    public function destroy(Payout $payout)
    {
        if ($payout->attachment_path) {
            Storage::disk('local')->delete($payout->attachment_path);
        }

        $payout->delete();

        return redirect()->route('payouts.index')->with('success', 'Payout deleted.');
    }
}
