<?php


namespace App\Transformer;


use App\Models\Operation;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Carbon;

class CsvToTransaction
{

    public function __invoke($args)
    {
        $date = $this->buildDate($args[0]);
        $actor = $this->buildTypeUser($args[1], $args[2]);
        $operation = $this->buildOperation($args[3]);
        $amount = $this->buildAmount($args[4]);
        $currency = $this->buildCurrency($args[5]);

        return new Transaction($date, $actor, $operation, $amount, $currency);
    }

    private function buildDate(string $value): Carbon
    {
        $date = Carbon::createFromFormat($dateFormat = 'Y-m-d', $value);
        if ($date instanceof Carbon && $date->format($dateFormat) == $value) {
            $date = $date->setTime(0, 0, 0);
        } else {
            throw new \DomainException("Value $value is not a Y-m-d format date");
        }

        return $date;
    }

    private function buildTypeUser(string $userId, string $userType): User
    {
        $userId = filter_var($userId, FILTER_VALIDATE_INT);
        if (false !== $userId) {
            $userId = intval($userId);
        } else {
            throw new \DomainException("Value $userId is not a numeric id");
        }
        if ($userType == User::TYPE_PRIVATE) {
            $userType = User::TYPE_PRIVATE;
        } elseif ($userType == User::TYPE_BUSINESS) {
            $userType = User::TYPE_BUSINESS;
        } else {
            throw new \DomainException("Value $userType is not valid actor type");
        }

        return new User($userId, $userType);
    }

    private function buildOperation(string $operationType): Operation
    {
        if ($operationType == Operation::DEPOSIT) {
            $operationType = Operation::DEPOSIT;
        } elseif ($operationType == Operation::WITHDRAW) {
            $operationType = Operation::WITHDRAW;
        } else {
            throw new \DomainException("Value $operationType is not valid opration type");
        }

        return new Operation($operationType);
    }

    private function buildAmount(string $amount)
    {
        $amount = filter_var($amount, FILTER_VALIDATE_FLOAT);
        if (false === $amount) {
            throw new \DomainException("Value $amount is not a valid amount of money");
        }

        return (float)$amount;
    }

    private function buildCurrency(string $currency){

        return $currency;
    }
}
