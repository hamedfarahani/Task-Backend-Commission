<?php

namespace App\Models;

use App\Lib\Convertor\ConvertRates;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Transaction extends Model
{
    use ConvertRates;

    private $date;
    private $user;
    private $operation;
    private $amount;
    private $currency;

    public function __construct(
        Carbon $date,
        User $user,
        Operation $operation,
        $amount,
        $currency
    )
    {
        $this->date = $date;
        $this->user = $user;
        $this->operation = $operation;
        $this->amount = $amount;
        $this->currency = $currency;
    }

    public function changeAmount($newAmount)
    {
        $this->amount = $newAmount;

        return $this;
    }

    public function getDate(): Carbon
    {
        return $this->date;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getOperation(): Operation
    {
        return $this->operation;
    }

    public function getAmount(): float
    {
        return (float)$this->amount;
    }

    public function getCurrency()
    {
        return $this->currency;
    }

    public function multiply($commissionRate)
    {
        return new Transaction($this->date, $this->user, $this->operation, $this->amount * $commissionRate, $this->currency);
    }

    public function roundUp()
    {
        $pow = pow ( 10, 2 );
        $this->amount = ( ceil ( $pow * $this->amount ) + ceil ( $pow * $this->amount - ceil ( $pow * $this->amount ) ) ) / $pow;;

        return $this;
    }

    public function convertWithRate()
    {
        $rate = ConvertRates::convert($this->currency);
        $this->amount = $this->amount / $rate;

        return $this;
    }
}
