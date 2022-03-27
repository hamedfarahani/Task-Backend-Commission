<?php


namespace App\Lib\Commissions;


use App\Models\Transaction;

interface Commission
{
    public function calculate(Transaction $transaction);

}
