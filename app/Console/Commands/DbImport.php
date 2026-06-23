<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DbImport extends Command
{
    protected $signature = 'db:import {path : Path to the JSON backup file}';

    protected $description = 'Import data from a JSON backup file';

    public function handle()
    {
        $path = $this->argument('path');

        if (! file_exists($path)) {
            $this->error("File not found: {$path}");

            return 1;
        }

        $data = json_decode(file_get_contents($path), true);

        if (! $data) {
            $this->error('Invalid JSON file');

            return 1;
        }

        if (! $this->confirm('This will replace all existing data. Continue?')) {
            return 0;
        }

        $this->info('Importing...');

        DB::transaction(function () use ($data) {
            DB::statement('PRAGMA foreign_keys = OFF');

            DB::delete('delete from salary_allocations');
            DB::delete('delete from transactions');
            DB::delete('delete from subscriptions');
            DB::delete('delete from salary_months');
            DB::delete('delete from users');

            $bar = $this->output->createProgressBar(count($data['users'] ?? []) + count($data['salary_months'] ?? []) + count($data['transactions'] ?? []) + count($data['subscriptions'] ?? []) + count($data['salary_allocations'] ?? []));
            $bar->start();

            foreach ($data['users'] ?? [] as $row) {
                DB::insert('insert into users (id, name, email, password, created_at, updated_at) values (?, ?, ?, ?, ?, ?)', [
                    $row['id'], $row['name'], $row['email'],
                    $row['password'] ?? bcrypt('password'),
                    $row['created_at'] ?? now(), $row['updated_at'] ?? now(),
                ]);
                $bar->advance();
            }

            foreach ($data['salary_months'] ?? [] as $row) {
                DB::insert('insert into salary_months (id, month_key, expected_salary, currency, notes, created_at, updated_at) values (?, ?, ?, ?, ?, ?, ?)', [
                    $row['id'], $row['month_key'], $row['expected_salary'], $row['currency'] ?? 'MAD', $row['notes'] ?? null,
                    $row['created_at'] ?? now(), $row['updated_at'] ?? now(),
                ]);
                $bar->advance();
            }

            foreach ($data['transactions'] ?? [] as $row) {
                $raw = $row['raw'] ?? null;
                if (is_array($raw)) {
                    $raw = json_encode($raw);
                }
                DB::insert('insert into transactions (id, paid_at, value_date, label, amount, source, subscription_id, raw, created_at, updated_at) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', [
                    $row['id'], $row['paid_at'], $row['value_date'] ?? null, $row['label'], $row['amount'],
                    $row['source'] ?? 'manual', $row['subscription_id'] ?? null, $raw,
                    $row['created_at'] ?? now(), $row['updated_at'] ?? now(),
                ]);
                $bar->advance();
            }

            foreach ($data['subscriptions'] ?? [] as $row) {
                DB::insert('insert into subscriptions (id, label, amount, frequency, start_at, status, category_id, last_generated_at, created_at, updated_at) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', [
                    $row['id'], $row['label'], $row['amount'], $row['frequency'],
                    $row['start_at'] ?? null, $row['status'] ?? 'active',
                    $row['category_id'] ?? null, $row['last_generated_at'] ?? null,
                    $row['created_at'] ?? now(), $row['updated_at'] ?? now(),
                ]);
                $bar->advance();
            }

            foreach ($data['salary_allocations'] ?? [] as $row) {
                DB::insert('insert into salary_allocations (id, transaction_id, salary_month_id, amount, created_at, updated_at) values (?, ?, ?, ?, ?, ?)', [
                    $row['id'] ?? null, $row['transaction_id'], $row['salary_month_id'], $row['amount'],
                    $row['created_at'] ?? now(), $row['updated_at'] ?? now(),
                ]);
                $bar->advance();
            }

            DB::statement('PRAGMA foreign_keys = ON');
            $bar->finish();
            $this->newLine();
        });

        $this->info('Import complete.');
    }
}
