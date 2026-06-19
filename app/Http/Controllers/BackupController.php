<?php

namespace App\Http\Controllers;

use App\Models\SalaryAllocation;
use App\Models\SalaryMonth;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BackupController extends Controller
{
    public function index()
    {
        $backups = collect(Storage::files('backups'))
            ->filter(fn ($f) => str_ends_with($f, '.json'))
            ->map(fn ($f) => [
                'name' => basename($f),
                'size' => Storage::size($f),
                'last_modified' => Storage::lastModified($f),
            ])
            ->sortByDesc('last_modified')
            ->values();

        return view('backup.index', compact('backups'));
    }

    public function export()
    {
        $path = 'backups/backup-' . now()->format('Y-m-d-His') . '.json';

        $data = [
            'exported_at' => now()->toIso8601String(),
            'users' => User::all()->map(fn ($u) => array_merge($u->toArray(), ['password' => $u->getAuthPassword()]))->values()->toArray(),
            'transactions' => Transaction::all()->toArray(),
            'salary_months' => SalaryMonth::all()->toArray(),
            'salary_allocations' => SalaryAllocation::all()->toArray(),
        ];

        Storage::put($path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        return redirect()->route('backup.index')->with('success', 'Backup created: ' . basename($path));
    }

    public function download($name)
    {
        $path = 'backups/' . basename($name);

        if (!Storage::exists($path)) {
            return redirect()->route('backup.index')->with('error', 'Backup not found.');
        }

        return Storage::download($path, $name);
    }

    public function restore(Request $request)
    {
        $request->validate(['file' => 'required|file|mimes:json']);

        $data = json_decode(file_get_contents($request->file('file')->getRealPath()), true);

        if (!$data || !isset($data['transactions'])) {
            return redirect()->route('backup.index')->with('error', 'Invalid backup file.');
        }

        $currentUser = auth()->user();

        DB::transaction(function () use ($data, $currentUser) {
            DB::delete('delete from salary_allocations');
            DB::delete('delete from transactions');
            DB::delete('delete from salary_months');
            DB::delete('delete from users');

            foreach ($data['users'] ?? [] as $row) {
                DB::insert('insert into users (id, name, email, password, created_at, updated_at) values (?, ?, ?, ?, ?, ?)', [
                    $row['id'], $row['name'], $row['email'],
                    $row['password'] ?? $currentUser?->getAuthPassword() ?? bcrypt('password'),
                    $row['created_at'] ?? now(), $row['updated_at'] ?? now(),
                ]);
            }

            foreach ($data['salary_months'] ?? [] as $row) {
                DB::insert('insert into salary_months (id, month_key, expected_salary, currency, notes, created_at, updated_at) values (?, ?, ?, ?, ?, ?, ?)', [
                    $row['id'], $row['month_key'], $row['expected_salary'], $row['currency'] ?? 'MAD', $row['notes'] ?? null,
                    $row['created_at'] ?? now(), $row['updated_at'] ?? now(),
                ]);
            }

            foreach ($data['transactions'] ?? [] as $row) {
                $raw = $row['raw'] ?? null;
                if (is_array($raw)) $raw = json_encode($raw);
                DB::insert('insert into transactions (id, paid_at, value_date, label, amount, source, is_salary, raw, created_at, updated_at) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', [
                    $row['id'], $row['paid_at'], $row['value_date'] ?? null, $row['label'], $row['amount'],
                    $row['source'] ?? 'manual', $row['is_salary'] ?? false, $raw,
                    $row['created_at'] ?? now(), $row['updated_at'] ?? now(),
                ]);
            }

            foreach ($data['salary_allocations'] ?? [] as $row) {
                DB::insert('insert into salary_allocations (id, transaction_id, salary_month_id, amount, created_at, updated_at) values (?, ?, ?, ?, ?, ?)', [
                    $row['id'] ?? null, $row['transaction_id'], $row['salary_month_id'], $row['amount'],
                    $row['created_at'] ?? now(), $row['updated_at'] ?? now(),
                ]);
            }
        });

        $newUser = User::where('email', $currentUser?->email)->first();
        if ($newUser) {
            auth()->login($newUser);
        }

        return redirect()->route('backup.index')->with('success', 'Data restored successfully.');
    }
}
