<?php

namespace App\Services;

use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class WalletService
{
    public function credit(User $user, float $amount, string $description, ?int $referenceId = null): WalletTransaction
    {
        return DB::transaction(function () use ($user, $amount, $description, $referenceId) {
            $wallet = $user->wallet;
            $before = $wallet->balance;

            $wallet->increment('balance', $amount);
            $wallet->increment('total_topup', $amount);
            $wallet->refresh();

            return WalletTransaction::create([
                'wallet_id'        => $wallet->id,
                'user_id'          => $user->id,
                'transaction_code' => WalletTransaction::generateCode(),
                'type'             => 'credit',
                'amount'           => $amount,
                'balance_before'   => $before,
                'balance_after'    => $wallet->balance,
                'description'      => $description,
                'reference_id'     => $referenceId,
            ]);
        });
    }

    public function debit(User $user, float $amount, string $description, ?int $referenceId = null): WalletTransaction
    {
        return DB::transaction(function () use ($user, $amount, $description, $referenceId) {
            $wallet = $user->wallet;

            if (!$wallet->hasSufficientBalance($amount)) {
                throw new \Exception('Saldo wallet tidak mencukupi.');
            }

            $before = $wallet->balance;
            $wallet->decrement('balance', $amount);
            $wallet->increment('total_spent', $amount);
            $wallet->refresh();

            return WalletTransaction::create([
                'wallet_id'        => $wallet->id,
                'user_id'          => $user->id,
                'transaction_code' => WalletTransaction::generateCode(),
                'type'             => 'debit',
                'amount'           => $amount,
                'balance_before'   => $before,
                'balance_after'    => $wallet->balance,
                'description'      => $description,
                'reference_id'     => $referenceId,
            ]);
        });
    }
}