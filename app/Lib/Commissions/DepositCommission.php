<?php


namespace App\Lib\Commissions;


use App\Models\Transaction;

class DepositCommission implements Commission
{
    const COMMISSION_RATE = 0.0003;

    public function calculate(Transaction $transaction)
    {
        return $transaction->multiply(self::COMMISSION_RATE)
                        ->roundUp()
                        ->getAmount();
    }
}
