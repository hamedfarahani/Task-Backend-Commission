<?php


namespace App\Lib;


use App\Lib\Commissions\DepositCommission;
use App\Lib\Commissions\WithdrawPrivateCommission;
use App\Lib\Commissions\WithdrawBusinessCommission;
use App\Models\Operation;
use App\Models\Transaction;
use App\Models\User;

class CommissionCalculator
{
    private $deposit;
    private $withdrawPrivate;
    private $withdrawBusiness;

    public function __construct()
    {
        $this->deposit = new DepositCommission();
        $this->withdrawBusiness = new WithdrawBusinessCommission();
        $this->withdrawPrivate = new WithdrawPrivateCommission();
    }

    public function calculateCommission(Transaction $transaction)
    {
        return $this
            ->pickStrategy($transaction)
            ->calculate($transaction);
    }

    protected function pickStrategy(Transaction $transaction)
    {
        if ($transaction->getOperation()->getType() === Operation::DEPOSIT) {
            return new DepositCommission();
        } elseif ($transaction->getOperation()->getType() === Operation::WITHDRAW
            && $transaction->getUser()->getType() == User::TYPE_BUSINESS) {
            return new WithdrawBusinessCommission();
        } elseif ($transaction->getOperation()->getType() === Operation::WITHDRAW
            && $transaction->getUser()->getType() == User::TYPE_PRIVATE) {

            return $this->withdrawPrivate;
        } else {
            throw new \DomainException('Unexpected transaction type');
        }
    }
}
