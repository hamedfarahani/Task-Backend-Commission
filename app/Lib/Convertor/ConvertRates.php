<?php


namespace App\Lib\Convertor;


use Illuminate\Support\Facades\Http;

trait ConvertRates
{

    public function convert(string $currency)
    {
        try {
            $rates = Http::get('https://developers.paysera.com/tasks/api/currency-exchange-rates')->body();
            $rates = json_decode($rates, true)['rates'];
        } catch (Exception $exception) {
            throw new HttpException('api request failed');
        }
        if (isset($rates[$currency])) {
            $rate = $rates[$currency];
        }

        return $rate;
    }
}
