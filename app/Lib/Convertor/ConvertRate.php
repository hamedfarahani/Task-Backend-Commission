<?php


namespace App\Lib\Convertor;


use HttpException;
use Illuminate\Support\Facades\Http;

class ConvertRate
{
    const rate = 1;

    public function convert(string $currency)
    {
        $rates = $this->getRate();
        if (isset($rates[$currency])) {
            $rate = $rates[$currency];
        } else {
            $rate = self::rate;
        }

        return $rate;
    }

    private function getRate()
    {

        try {
            $rates = Http::get('https://developers.paysera.com/tasks/api/currency-exchange-rates')->body();
            $rates = json_decode($rates, true);
        } catch (Exception $exception) {
            throw new HttpException('api request failed');
        }

        return $rates['rates'];
    }
}
