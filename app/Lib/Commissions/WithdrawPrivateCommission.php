<?php


namespace App\Lib\Commissions;


use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;

class WithdrawPrivateCommission implements Commission
{
    const MAX_DISCOUNT_TIMES = 3;
    const DISCOUNT_RATE = 0.003;
    const MAX_DISCOUNT_AMOUNT = 1000;

   public static $consumptions = [];

    public function calculate(Transaction $transaction)
    {
        $transaction->convertWithRate();
        $key = $transaction->getUser()->getId() . '-' . $transaction->getDate()->format('oW');
        $discountData = $this->getDiscount($transaction->getUser(), $transaction->getDate(), $transaction->getAmount(),$key);
        if($discountData['time'] <= self::MAX_DISCOUNT_TIMES && self::$consumptions[$key]['flagContinueDiscount'] == true){
            $commissioned = ($transaction->getAmount() - $discountData['amount']);
            if($commissioned <= 0){
                $this->updateDiscountFlag($discountData,$transaction->getAmount(),$key);
                return 0;
            }else{
                $maxAmount = $discountData['amount'];
                $this->updateDiscountFlag($discountData,$transaction->getAmount(),$key);
                $transaction->changeAmount($commissioned);
            }
        }

        return $transaction->multiply(self::DISCOUNT_RATE)
            ->roundUp()
            ->getAmount();
    }

    private function getDiscount(User $user, Carbon $date, $amount, $key)
    {
        $key = $user->getId() . '-' . $date->format('oW');
        if (!isset(self::$consumptions[$key])) {
           $amount = (self::MAX_DISCOUNT_AMOUNT - $amount)  > 0 ? (self::MAX_DISCOUNT_AMOUNT - $amount) : self::MAX_DISCOUNT_AMOUNT;
            $flagContinueDiscount = true;
            self::$consumptions[$key] = [
                'amount' => self::MAX_DISCOUNT_AMOUNT,
                'time' => 1,
                'flagContinueDiscount' => $flagContinueDiscount,
            ];
       }
        return self::$consumptions[$key];
    }

    private function updateDiscountFlag($discountData,$transactionAmount,$key){
        $maxAmount = $discountData['amount'];
        if($maxAmount - $transactionAmount < 0){
            $flagContinueDiscount = false;
            self::$consumptions[$key]['flagContinueDiscount'] = $flagContinueDiscount;
        }else{
            $newAmount = $maxAmount - $transactionAmount;
            self::$consumptions[$key]['amount'] = $newAmount;
        }
    }
}
