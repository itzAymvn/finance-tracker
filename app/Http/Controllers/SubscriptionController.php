<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSubscriptionRequest;
use App\Models\Category;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SubscriptionController extends Controller
{
    public function index()
    {
        $subscriptions = Subscription::with('category')
            ->withCount('transactions')
            ->orderBy('status')
            ->orderBy('label')
            ->get()
            ->map(fn (Subscription $sub) => [
                'id' => $sub->id,
                'label' => $sub->label,
                'amount' => $sub->amount,
                'frequency' => $sub->frequency,
                'start_at' => $sub->start_at->toIso8601String(),
                'status' => $sub->status,
                'category_id' => $sub->category_id,
                'category' => $sub->category ? [
                    'id' => $sub->category->id,
                    'name' => $sub->category->name,
                    'icon' => $sub->category->icon,
                    'is_salary' => $sub->category->is_salary,
                ] : null,
                'last_generated_at' => $sub->last_generated_at?->toIso8601String(),
                'next_due_at' => $sub->isActive() ? $sub->getNextDueAt()?->toIso8601String() : null,
                'transactions_count' => $sub->transactions_count,
            ]);

        $summary = [
            'active' => $subscriptions->where('status', 'active')->count(),
            'paused' => $subscriptions->where('status', 'paused')->count(),
            'cancelled' => $subscriptions->where('status', 'cancelled')->count(),
            'monthly_cost' => $subscriptions
                ->where('status', 'active')
                ->reduce(function ($carry, $sub) {
                    $amount = abs((float) $sub['amount']);
                    return $carry + match ($sub['frequency']) {
                        'weekly'    => $amount * 4.33,
                        'biweekly'  => $amount * 2.17,
                        'monthly'   => $amount,
                        'quarterly' => $amount / 3,
                        'yearly'    => $amount / 12,
                        default     => 0,
                    };
                }, 0),
        ];

        return Inertia::render('Subscriptions/Index', [
            'subscriptions' => $subscriptions,
            'summary' => $summary,
            'categories' => Category::orderBy('name')->get(['id', 'name', 'icon', 'is_salary']),
        ]);
    }

    public function store(StoreSubscriptionRequest $request)
    {
        $data = $request->validated();

        Subscription::create([
            'label'       => $data['label'],
            'amount'      => $data['amount'],
            'frequency'   => $data['frequency'],
            'start_at'    => $data['start_at'],
            'category_id' => $data['category_id'] ?? null,
        ]);

        return redirect()
            ->route('subscriptions.index')
            ->with('success', 'Subscription created.');
    }

    public function update(StoreSubscriptionRequest $request, Subscription $subscription)
    {
        $data = $request->validated();

        $subscription->update([
            'label'       => $data['label'],
            'amount'      => $data['amount'],
            'frequency'   => $data['frequency'],
            'start_at'    => $data['start_at'],
            'category_id' => $data['category_id'] ?? null,
        ]);

        return redirect()
            ->route('subscriptions.index')
            ->with('success', 'Subscription updated.');
    }

    public function destroy(Subscription $subscription)
    {
        $subscription->delete();

        return redirect()
            ->route('subscriptions.index')
            ->with('success', 'Subscription deleted.');
    }

    public function pause(Subscription $subscription)
    {
        $subscription->pause();

        return redirect()
            ->route('subscriptions.index')
            ->with('success', 'Subscription paused.');
    }

    public function resume(Subscription $subscription)
    {
        $subscription->resume();

        return redirect()
            ->route('subscriptions.index')
            ->with('success', 'Subscription resumed.');
    }

    public function cancel(Subscription $subscription)
    {
        $subscription->cancel();

        return redirect()
            ->route('subscriptions.index')
            ->with('success', 'Subscription cancelled.');
    }
}
